<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function history()
    {
        $attendances = auth()->user()->employee?->attendances()->latest('attendance_date')->paginate(15) ?? collect();

        return view('employee.attendance.history', compact('attendances'));
    }

    public function checkIn()
    {
        return view('employee.attendance.check-in', ['employee' => auth()->user()->employee]);
    }
}
