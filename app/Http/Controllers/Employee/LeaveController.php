<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use Carbon\Carbon;
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

        $today = today()->toDateString();

        $data = $request->validate([
            'jenis_cuti_id' => ['required', 'exists:jenis_cuti,id'],
            'tanggal_mulai' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$today],
            'tanggal_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'alasan' => ['nullable', 'string'],
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai cuti/izin tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai cuti/izin harus sama atau setelah tanggal mulai.',
            'tanggal_mulai.date_format' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.date_format' => 'Format tanggal selesai tidak valid.',
        ], [
            'jenis_cuti_id' => 'jenis cuti',
            'tanggal_mulai' => 'tanggal mulai',
            'tanggal_selesai' => 'tanggal selesai',
            'alasan' => 'alasan',
        ]);

        $start = Carbon::parse($data['tanggal_mulai']);
        $end = Carbon::parse($data['tanggal_selesai']);

        Leave::create($data + [
            'karyawan_id' => $employee->id,
            'jumlah_hari' => $start->diffInDays($end) + 1,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}
