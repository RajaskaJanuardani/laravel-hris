<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OvertimeApproval;
use Illuminate\Http\Request;

class OvertimeApprovalController extends Controller
{
    public function index()
    {
        return view('admin.overtime.index', [
            'employees' => Employee::active()->orderBy('first_name')->get(),
            'approvals' => OvertimeApproval::with(['employee', 'approvedBy'])
                ->latest('overtime_date')
                ->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'overtime_date' => ['required', 'date'],
            'end_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($data['end_time'] <= '17:00' || $data['end_time'] > '22:00') {
            return back()->withErrors(['end_time' => 'Jam lembur harus setelah 17:00 dan maksimal 22:00.'])->withInput();
        }

        OvertimeApproval::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'overtime_date' => $data['overtime_date'],
            ],
            [
                'start_time' => '17:00:00',
                'end_time' => $data['end_time'].':00',
                'status' => 'approved',
                'notes' => $data['notes'] ?? null,
                'approved_by' => $request->user()->id,
            ]
        );

        return back()->with('success', 'Approval lembur berhasil disimpan.');
    }

    public function destroy(OvertimeApproval $overtime)
    {
        $overtime->update(['status' => 'cancelled']);

        return back()->with('success', 'Approval lembur dibatalkan.');
    }
}
