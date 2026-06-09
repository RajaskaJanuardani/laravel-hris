<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OvertimeApproval;
use App\Models\Leave;
use App\Models\Payslip;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee?->load('currentLeave');
        $today = today()->toDateString();
        $todayAttendance = $employee?->getTodayAttendance();
        $recentAttendance = $employee ? $employee->absensi()->latest('tanggal_absensi')->take(8)->get() : collect();

        if ($employee && ! $todayAttendance) {
            $todayAttendance = $this->virtualAbsentAttendance($employee, $today);
            $recentAttendance = $recentAttendance->prepend($todayAttendance)->take(8);
        }

        $monthAttendance = $employee
            ? Attendance::where('karyawan_id', $employee->id)
                ->whereMonth('tanggal_absensi', now()->month)
                ->whereYear('tanggal_absensi', now()->year)
            : null;

        return view('employee.dashboard', [
            'employee' => $employee,
            'todayAttendance' => $todayAttendance,
            'currentStatus' => $employee?->current_status ?? ['label' => 'Belum tersedia', 'badge' => 'secondary'],
            'absensi' => $recentAttendance,
            'leaves' => $employee ? $employee->leaves()->with('leaveType')->latest()->take(5)->get() : collect(),
            'overtimeApprovals' => $employee ? $employee->overtimeApprovals()->with('approvedBy')->latest('tanggal_lembur')->take(5)->get() : collect(),
            'monthlyPresent' => $monthAttendance ? (clone $monthAttendance)->where('status', 'present')->count() : 0,
            'monthlyLate' => $monthAttendance ? (clone $monthAttendance)->where('status', 'late')->count() : 0,
            'monthlyAbsent' => $monthAttendance ? (clone $monthAttendance)->where('status', 'absent')->count() : 0,
            'monthlyOvertimeHours' => $monthAttendance ? (float) (clone $monthAttendance)->sum('jam_lembur') : 0,
            'pendingLeaves' => $employee ? Leave::where('karyawan_id', $employee->id)->pending()->count() : 0,
            'approvedOvertimeThisMonth' => $employee ? OvertimeApproval::approved()->where('karyawan_id', $employee->id)->whereMonth('tanggal_lembur', now()->month)->whereYear('tanggal_lembur', now()->year)->count() : 0,
            'latestPayslip' => $employee ? Payslip::with('payrollPeriod')->where('karyawan_id', $employee->id)->latest('tanggal_penggajian')->first() : null,
        ]);
    }

    private function virtualAbsentAttendance(Employee $employee, string $date): object
    {
        return (object) [
            'tanggal_absensi' => Carbon::parse($date),
            'employee' => $employee,
            'jam_masuk' => null,
            'jam_pulang' => null,
            'menit_telat' => 0,
            'jam_lembur' => 0,
            'status' => 'absent',
            'virtual' => true,
        ];
    }
}
