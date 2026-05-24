<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        return view('admin.payroll.index', [
            'periods' => PayrollPeriod::latest()->get(),
            'payslips' => Payslip::with(['employee', 'payrollPeriod'])->latest()->paginate(12),
        ]);
    }

    public function create()
    {
        return view('admin.payroll.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:payroll_periods,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $period = PayrollPeriod::create($data + ['status' => 'draft']);

        Employee::active()->each(function (Employee $employee) use ($period) {
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$period->start_date, $period->end_date]);
            $lateCount = (clone $attendance)->where('status', 'late')->count();
            $absentDays = (clone $attendance)->where('status', 'absent')->count();
            $lateDeduction = $lateCount * 10000;
            $overtimeHours = (clone $attendance)->sum('overtime_hours');
            $overtimeAmount = $overtimeHours * 25000;
            $baseSalary = $employee->salary;

            Payslip::updateOrCreate(
                ['employee_id' => $employee->id, 'payroll_period_id' => $period->id],
                [
                    'payroll_date' => now(),
                    'working_days' => (clone $attendance)->count(),
                    'absent_days' => $absentDays,
                    'late_count' => $lateCount,
                    'late_deduction' => $lateDeduction,
                    'overtime_hours' => $overtimeHours,
                    'overtime_amount' => $overtimeAmount,
                    'base_salary' => $baseSalary,
                    'total_allowance' => $overtimeAmount,
                    'total_deduction' => $lateDeduction,
                    'net_salary' => max(0, $baseSalary + $overtimeAmount - $lateDeduction),
                    'status' => 'draft',
                ]
            );
        });

        return redirect()->route('admin.payroll.index')->with('success', 'Payroll berhasil dibuat.');
    }

    public function payslips()
    {
        return view('admin.payroll.payslips', [
            'payslips' => Payslip::with(['employee', 'payrollPeriod'])->latest()->paginate(15),
        ]);
    }
}
