<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;

class PayslipController extends Controller
{
    public function index()
    {
        $slip_gaji = auth()->user()->employee?->slip_gaji()->with('payrollPeriod')->latest()->paginate(12) ?? collect();

        return view('employee.payslip.index', compact('slip_gaji'));
    }

    public function show(string $id)
    {
        $payslip = auth()->user()->employee?->slip_gaji()->with('payrollPeriod')->findOrFail($id);

        return view('employee.payslip.show', compact('payslip'));
    }
}
