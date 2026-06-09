<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        return view('admin.settings.leave-types', ['leaveTypes' => LeaveType::latest()->get()]);
    }

    public function store(Request $request)
    {
        LeaveType::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:jenis_cuti,kode'],
            'kuota_per_tahun' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]));

        return back()->with('success', 'Tipe cuti berhasil disimpan.');
    }
}
