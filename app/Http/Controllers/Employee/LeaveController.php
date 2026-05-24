<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;

        return view('employee.leaves.index', [
            'leaves' => $employee ? $employee->leaves()->with('leaveType')->latest()->paginate(10) : collect(),
            'leaveTypes' => LeaveType::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 422, 'Profil karyawan belum tersedia.');

        $data = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string'],
        ]);

        $start = \Carbon\Carbon::parse($data['start_date']);
        $end = \Carbon\Carbon::parse($data['end_date']);

        Leave::create($data + [
            'employee_id' => $employee->id,
            'number_of_days' => $start->diffInDays($end) + 1,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}
