<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\OvertimeApproval;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function attendanceDaily(Carbon $date): Collection
    {
        return Attendance::query()
            ->with(['employee'])
            ->whereDate('attendance_date', $date)
            ->orderBy(Employee::select('first_name')->whereColumn('employees.id', 'attendances.employee_id'))
            ->get();
    }

    public function attendanceRecap(Carbon $from, Carbon $to): LengthAwarePaginator
    {
        $base = Employee::query()
            ->active();

        $base->withCount([
            'attendances as present_days' => fn ($q) => $q->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
                ->whereIn('status', ['present', 'late']),
            'attendances as late_days' => fn ($q) => $q->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
                ->where('status', 'late'),
            'attendances as absent_days' => fn ($q) => $q->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
                ->where('status', 'absent'),
        ]);

        $base->addSelect([
            'late_minutes_total' => Attendance::selectRaw('COALESCE(SUM(late_minutes), 0)')
                ->whereColumn('employee_id', 'employees.id')
                ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()]),
            'overtime_hours_total' => Attendance::selectRaw('COALESCE(SUM(overtime_hours), 0)')
                ->whereColumn('employee_id', 'employees.id')
                ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()]),
            'working_days_total' => Attendance::selectRaw('COUNT(*)')
                ->whereColumn('employee_id', 'employees.id')
                ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()]),
            'leave_days_total' => Leave::selectRaw('COALESCE(SUM(number_of_days), 0)')
                ->whereColumn('employee_id', 'employees.id')
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $to->toDateString())
                ->whereDate('end_date', '>=', $from->toDateString()),
        ]);

        return $base->orderBy('first_name')->paginate(15)->withQueryString();
    }

    public function lateRanking(Carbon $from, Carbon $to): LengthAwarePaginator
    {
        $base = Employee::query()
            ->active();

        $base->withCount([
            'attendances as late_days' => fn ($q) => $q->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
                ->where('status', 'late'),
        ]);

        $base->addSelect([
            'late_minutes_total' => Attendance::selectRaw('COALESCE(SUM(late_minutes), 0)')
                ->whereColumn('employee_id', 'employees.id')
                ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()]),
            'latest_late_date' => Attendance::selectRaw('MAX(attendance_date)')
                ->whereColumn('employee_id', 'employees.id')
                ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
                ->where('status', 'late'),
        ]);

        return $base
            ->orderByDesc('late_days')
            ->orderByDesc('late_minutes_total')
            ->paginate(15)
            ->withQueryString();
    }

    public function overtime(Carbon $from, Carbon $to, ?int $employeeId = null): LengthAwarePaginator
    {
        return OvertimeApproval::query()
            ->with(['employee', 'approvedBy'])
            ->approved()
            ->whereBetween('overtime_date', [$from->toDateString(), $to->toDateString()])
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->latest('overtime_date')
            ->paginate(15)
            ->withQueryString();
    }

    public function leaves(Carbon $from, Carbon $to, ?string $status = null, ?int $employeeId = null): LengthAwarePaginator
    {
        return Leave::query()
            ->with(['employee', 'leaveType', 'approvedBy'])
            ->whereDate('start_date', '<=', $to->toDateString())
            ->whereDate('end_date', '>=', $from->toDateString())
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function payroll(Carbon $from, Carbon $to, ?int $periodId = null): LengthAwarePaginator
    {
        return Payslip::query()
            ->with(['employee', 'payrollPeriod'])
            ->whereBetween('payroll_date', [$from->toDateString(), $to->toDateString()])
            ->when($periodId, fn ($q) => $q->where('payroll_period_id', $periodId))
            ->latest('payroll_date')
            ->paginate(15)
            ->withQueryString();
    }

    public function rfidAudit(Carbon $from, Carbon $to, ?string $status = null, ?string $source = null): LengthAwarePaginator
    {
        return AttendanceLog::query()
            ->with('employee')
            ->whereBetween('scanned_at', [$from->startOfDay(), $to->endOfDay()])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($source, fn ($q) => $q->where('source', $source))
            ->latest('scanned_at')
            ->paginate(20)
            ->withQueryString();
    }

    // Catatan: project ini tidak memakai konsep departemen/shift untuk laporan.
}
