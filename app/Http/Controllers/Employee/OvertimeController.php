<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\OvertimeApproval;

class OvertimeController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;

        abort_unless($employee, 404, 'Profil karyawan belum tersedia.');

        return view('employee.overtime.index', [
            'overtimeApprovals' => $employee->overtimeApprovals()
                ->with('approvedBy')
                ->latest('overtime_date')
                ->paginate(10),
            'approvedOvertimeThisMonth' => OvertimeApproval::approved()
                ->where('employee_id', $employee->id)
                ->whereMonth('overtime_date', now()->month)
                ->whereYear('overtime_date', now()->year)
                ->count(),
            'nextOvertime' => OvertimeApproval::approved()
                ->where('employee_id', $employee->id)
                ->whereDate('overtime_date', '>=', today())
                ->orderBy('overtime_date')
                ->first(),
        ]);
    }
}
