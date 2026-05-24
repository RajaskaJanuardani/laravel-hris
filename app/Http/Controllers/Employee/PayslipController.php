<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;

class PayslipController extends Controller
{
    public function index()
    {
        $payslips = auth()->user()->employee?->payslips()->with('payrollPeriod')->latest()->paginate(12) ?? collect();

        return view('employee.payslip.index', compact('payslips'));
    }

    public function show(string $id)
    {
        $payslip = auth()->user()->employee?->payslips()->with('payrollPeriod')->findOrFail($id);

        return view('employee.payslip.show', compact('payslip'));
    }
}
