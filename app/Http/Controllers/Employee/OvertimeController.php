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
                ->latest('tanggal_lembur')
                ->paginate(10),
            'approvedOvertimeThisMonth' => OvertimeApproval::approved()
                ->where('karyawan_id', $employee->id)
                ->whereMonth('tanggal_lembur', now()->month)
                ->whereYear('tanggal_lembur', now()->year)
                ->count(),
            'nextOvertime' => OvertimeApproval::approved()
                ->where('karyawan_id', $employee->id)
                ->whereDate('tanggal_lembur', '>=', today())
                ->orderBy('tanggal_lembur')
                ->first(),
        ]);
    }
}
