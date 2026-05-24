<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\OvertimeApproval;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        return view('admin.dashboard', [
            'totalEmployees' => Employee::active()->count(),
            'presentToday' => Attendance::whereDate('attendance_date', $today)->whereIn('status', ['present', 'late'])->count(),
            'lateToday' => Attendance::whereDate('attendance_date', $today)->where('status', 'late')->count(),
            'leaveToday' => Leave::approved()->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->count(),
            'pendingLeaves' => Leave::pending()->count(),
            'overtimeToday' => OvertimeApproval::approved()->whereDate('overtime_date', $today)->count(),
            'jobRoleStats' => Employee::selectRaw('job_role, COUNT(*) as total')->groupBy('job_role')->pluck('total', 'job_role'),
            'recentLogs' => AttendanceLog::with('employee')->latest('scanned_at')->take(8)->get(),
            'monthlyAttendance' => Attendance::selectRaw('DAY(attendance_date) as day, COUNT(*) as total')
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day'),
        ]);
    }
}
