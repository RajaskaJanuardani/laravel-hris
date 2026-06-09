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
            'jenis_cuti_id' => ['required', 'exists:jenis_cuti,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alasan' => ['nullable', 'string'],
        ]);

        $start = \Carbon\Carbon::parse($data['tanggal_mulai']);
        $end = \Carbon\Carbon::parse($data['tanggal_selesai']);

        Leave::create($data + [
            'karyawan_id' => $employee->id,
            'jumlah_hari' => $start->diffInDays($end) + 1,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}
