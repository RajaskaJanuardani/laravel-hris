<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        return view('admin.leaves.index', [
            'leaves' => Leave::with(['employee', 'leaveType', 'approvedBy'])->latest()->paginate(15),
        ]);
    }

    public function approve(Request $request, Leave $leave)
    {
        $leave->approve($request->user(), $request->input('approval_notes'));

        return back()->with('success', 'Pengajuan cuti disetujui.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate(['approval_notes' => ['required', 'string']]);
        $leave->reject($request->user(), $request->approval_notes);

        return back()->with('success', 'Pengajuan cuti ditolak.');
    }
}
