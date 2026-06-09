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
            ->whereDate('tanggal_absensi', $date)
            ->orderBy(Employee::select('nama_depan')->whereColumn('karyawan.id', 'absensi.karyawan_id'))
            ->get();
    }

    public function attendanceRecap(Carbon $from, Carbon $to): LengthAwarePaginator
    {
        $base = Employee::query()
            ->active();

        $base->withCount([
            'absensi as present_days' => fn ($q) => $q->whereBetween('tanggal_absensi', [$from->toDateString(), $to->toDateString()])
                ->whereIn('status', ['present', 'late']),
            'absensi as late_days' => fn ($q) => $q->whereBetween('tanggal_absensi', [$from->toDateString(), $to->toDateString()])
                ->where('status', 'late'),
            'absensi as hari_tidak_hadir' => fn ($q) => $q->whereBetween('tanggal_absensi', [$from->toDateString(), $to->toDateString()])
                ->where('status', 'absent'),
        ]);

        $base->addSelect([
            'menit_telat_total' => Attendance::selectRaw('COALESCE(SUM(menit_telat), 0)')
                ->whereColumn('karyawan_id', 'karyawan.id')
                ->whereBetween('tanggal_absensi', [$from->toDateString(), $to->toDateString()]),
            'jam_lembur_total' => Attendance::selectRaw('COALESCE(SUM(jam_lembur), 0)')
                ->whereColumn('karyawan_id', 'karyawan.id')
                ->whereBetween('tanggal_absensi', [$from->toDateString(), $to->toDateString()]),
            'hari_kerja_total' => Attendance::selectRaw('COUNT(*)')
                ->whereColumn('karyawan_id', 'karyawan.id')
                ->whereBetween('tanggal_absensi', [$from->toDateString(), $to->toDateString()]),
            'leave_days_total' => Leave::selectRaw('COALESCE(SUM(jumlah_hari), 0)')
                ->whereColumn('karyawan_id', 'karyawan.id')
                ->where('status', 'approved')
                ->whereDate('tanggal_mulai', '<=', $to->toDateString())
                ->whereDate('tanggal_selesai', '>=', $from->toDateString()),
        ]);

        return $base->orderBy('nama_depan')->paginate(15)->withQueryString();
    }

    public function lateRanking(Carbon $from, Carbon $to): LengthAwarePaginator
    {
        $base = Employee::query()
            ->active();

        $base->withCount([
            'absensi as late_days' => fn ($q) => $q->whereBetween('tanggal_absensi', [$from->toDateString(), $to->toDateString()])
                ->where('status', 'late'),
        ]);

        $base->addSelect([
            'menit_telat_total' => Attendance::selectRaw('COALESCE(SUM(menit_telat), 0)')
                ->whereColumn('karyawan_id', 'karyawan.id')
                ->whereBetween('tanggal_absensi', [$from->toDateString(), $to->toDateString()]),
            'latest_late_date' => Attendance::selectRaw('MAX(tanggal_absensi)')
                ->whereColumn('karyawan_id', 'karyawan.id')
                ->whereBetween('tanggal_absensi', [$from->toDateString(), $to->toDateString()])
                ->where('status', 'late'),
        ]);

        return $base
            ->orderByDesc('late_days')
            ->orderByDesc('menit_telat_total')
            ->paginate(15)
            ->withQueryString();
    }

    public function overtime(Carbon $from, Carbon $to, ?int $employeeId = null): LengthAwarePaginator
    {
        return OvertimeApproval::query()
            ->with(['employee', 'approvedBy'])
            ->approved()
            ->whereBetween('tanggal_lembur', [$from->toDateString(), $to->toDateString()])
            ->when($employeeId, fn ($q) => $q->where('karyawan_id', $employeeId))
            ->latest('tanggal_lembur')
            ->paginate(15)
            ->withQueryString();
    }

    public function leaves(Carbon $from, Carbon $to, ?string $status = null, ?int $employeeId = null): LengthAwarePaginator
    {
        return Leave::query()
            ->with(['employee', 'leaveType', 'approvedBy'])
            ->whereDate('tanggal_mulai', '<=', $to->toDateString())
            ->whereDate('tanggal_selesai', '>=', $from->toDateString())
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($employeeId, fn ($q) => $q->where('karyawan_id', $employeeId))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function payroll(Carbon $from, Carbon $to, ?int $periodId = null): LengthAwarePaginator
    {
        return Payslip::query()
            ->with(['employee', 'payrollPeriod'])
            ->whereBetween('tanggal_penggajian', [$from->toDateString(), $to->toDateString()])
            ->when($periodId, fn ($q) => $q->where('periode_penggajian_id', $periodId))
            ->latest('tanggal_penggajian')
            ->paginate(15)
            ->withQueryString();
    }

    public function rfidAudit(Carbon $from, Carbon $to, ?string $status = null, ?string $source = null): LengthAwarePaginator
    {
        return AttendanceLog::query()
            ->with('employee')
            ->whereBetween('dipindai_pada', [$from->startOfDay(), $to->endOfDay()])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($source, fn ($q) => $q->where('sumber', $source))
            ->latest('dipindai_pada')
            ->paginate(20)
            ->withQueryString();
    }

    // Catatan: project ini tidak memakai konsep departemen/shift untuk laporan.
}
