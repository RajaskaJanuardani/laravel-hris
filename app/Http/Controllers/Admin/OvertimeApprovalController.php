<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OvertimeApproval;
use Illuminate\Http\Request;

class OvertimeApprovalController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.overtime.index', [
            'karyawan' => Employee::active()->orderBy('nama_depan')->get(),
            'approvals' => OvertimeApproval::with(['employee', 'approvedBy'])
                ->when($request->filled('date'), fn ($query) => $query->whereDate('tanggal_lembur', $request->date))
                ->latest('tanggal_lembur')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'karyawan_id' => ['required', 'exists:karyawan,id'],
            'tanggal_lembur' => ['required', 'date'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'catatan' => ['nullable', 'string'],
        ]);

        if ($data['jam_selesai'] <= '17:00' || $data['jam_selesai'] > '22:00') {
            return back()->withErrors(['jam_selesai' => 'Jam lembur harus setelah 17:00 dan maksimal 22:00.'])->withInput();
        }

        OvertimeApproval::updateOrCreate(
            [
                'karyawan_id' => $data['karyawan_id'],
                'tanggal_lembur' => $data['tanggal_lembur'],
            ],
            [
                'jam_mulai' => '17:00:00',
                'jam_selesai' => $data['jam_selesai'].':00',
                'status' => 'approved',
                'catatan' => $data['catatan'] ?? null,
                'disetujui_oleh' => $request->user()->id,
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
