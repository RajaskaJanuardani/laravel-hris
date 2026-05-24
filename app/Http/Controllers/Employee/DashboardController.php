<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\OvertimeApproval;
use App\Models\Leave;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee?->load('currentLeave');

        return view('employee.dashboard', [
            'employee' => $employee,
            'todayAttendance' => $employee?->getTodayAttendance(),
            'currentStatus' => $employee?->current_status ?? ['label' => 'Belum tersedia', 'badge' => 'secondary'],
            'attendances' => $employee ? $employee->attendances()->latest('attendance_date')->take(8)->get() : collect(),
            'leaves' => $employee ? $employee->leaves()->with('leaveType')->latest()->take(5)->get() : collect(),
            'overtimeApprovals' => $employee ? $employee->overtimeApprovals()->with('approvedBy')->latest('overtime_date')->take(5)->get() : collect(),
            'monthlyPresent' => $employee ? Attendance::where('employee_id', $employee->id)->whereMonth('attendance_date', now()->month)->whereIn('status', ['present', 'late'])->count() : 0,
            'pendingLeaves' => $employee ? Leave::where('employee_id', $employee->id)->pending()->count() : 0,
            'approvedOvertimeThisMonth' => $employee ? OvertimeApproval::approved()->where('employee_id', $employee->id)->whereMonth('overtime_date', now()->month)->whereYear('overtime_date', now()->year)->count() : 0,
        ]);
    }
}
