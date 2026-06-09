<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $today = today();

        return view('admin.leaves.index', [
            'leaves' => Leave::with(['employee', 'leaveType', 'approvedBy'])
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
                ->when($request->boolean('aktif'), fn ($query) => $query
                    ->where('status', 'approved')
                    ->whereDate('tanggal_mulai', '<=', $today)
                    ->whereDate('tanggal_selesai', '>=', $today))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function approve(Request $request, Leave $leave)
    {
        $leave->approve($request->user(), $request->input('catatan_persetujuan'));

        return back()->with('success', 'Pengajuan cuti disetujui.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate(['catatan_persetujuan' => ['required', 'string']]);
        $leave->reject($request->user(), $request->catatan_persetujuan);

        return back()->with('success', 'Pengajuan cuti ditolak.');
    }
}
