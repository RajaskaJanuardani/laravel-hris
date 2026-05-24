<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\RFIDCard;
use App\Services\RFIDService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendances = Attendance::with('employee')
            ->when($request->date, fn ($query) => $query->whereDate('attendance_date', $request->date))
            ->latest('attendance_date')
            ->paginate(15);

        return view('admin.attendance.index', compact('attendances'));
    }

    public function monitoring()
    {
        return view('admin.attendance.monitoring', [
            'logs' => AttendanceLog::with('employee')->latest('scanned_at')->take(25)->get(),
            'cards' => RFIDCard::with('employee')->active()->get(),
        ]);
    }

    public function simulate(Request $request, RFIDService $rfidService)
    {
        $request->validate(['uid' => ['required', 'string', 'max:100']]);
        $result = $rfidService->scan($request->uid, $request, 'simulator');

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function report(Request $request)
    {
        $attendances = Attendance::with('employee')
            ->when($request->from, fn ($query) => $query->whereDate('attendance_date', '>=', $request->from))
            ->when($request->to, fn ($query) => $query->whereDate('attendance_date', '<=', $request->to))
            ->latest('attendance_date')
            ->get();

        return view('admin.attendance.report', compact('attendances'));
    }
}
