<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\RFIDCard;
use App\Services\RFIDService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    private const WORK_START = '08:00:00';
    private const LATE_TOLERANCE_MINUTES = 10;

    public function index(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $statusMap = [
            'hadir' => 'present',
            'telat' => 'late',
            'tidak_hadir' => 'absent',
            'present' => 'present',
            'late' => 'late',
            'absent' => 'absent',
        ];
        $selectedDate = Carbon::parse($validated['date'] ?? today())->toDateString();
        $requestStatus = $validated['status'] ?? null;
        $selectedStatus = $requestStatus ? ($statusMap[$requestStatus] ?? null) : null;
        $search = trim((string) ($validated['q'] ?? ''));

        $rows = Employee::active()
            ->with(['absensi' => fn ($query) => $query->whereDate('tanggal_absensi', $selectedDate)])
            ->orderBy('nama_depan')
            ->orderBy('nama_belakang')
            ->get()
            ->map(function (Employee $employee) use ($selectedDate) {
                $attendance = $employee->absensi->first();

                if ($attendance) {
                    return $attendance->setRelation('employee', $employee);
                }

                return $this->virtualAbsentAttendance($employee, $selectedDate);
            })
            ->filter(fn ($attendance) => ! $selectedStatus || $attendance->status === $selectedStatus)
            ->filter(fn ($attendance) => $search === '' || $this->matchesAttendanceSearch($attendance->employee, $search))
            ->values();

        $absensi = $this->paginateRows($rows, $request, 15);

        return view('admin.attendance.index', [
            'absensi' => $absensi,
            'selectedStatus' => $request->input('status'),
            'selectedDate' => $selectedDate,
            'search' => $search,
        ]);
    }

    public function monitoring()
    {
        return view('admin.attendance.monitoring', [
            'logs' => AttendanceLog::with('employee')->latest('dipindai_pada')->take(25)->get(),
            'cards' => RFIDCard::with('employee')->active()->get(),
        ]);
    }

    public function simulate(Request $request, RFIDService $rfidService)
    {
        $request->validate(['uid' => ['required', 'string', 'max:100']]);
        $result = $rfidService->scan($request->uid, $request, 'simulator');

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function updateManualStatus(Request $request)
    {
        $data = $request->validate([
            'karyawan_id' => ['required', 'exists:karyawan,id'],
            'tanggal_absensi' => ['required', 'date'],
            'status' => ['required', 'in:present,absent'],
            'jam_masuk_manual' => ['nullable', 'required_if:status,present', 'date_format:H:i'],
            'catatan' => ['required', 'string', 'max:500'],
        ]);

        $date = Carbon::parse($data['tanggal_absensi'])->toDateString();
        $attendance = Attendance::firstOrNew([
            'karyawan_id' => $data['karyawan_id'],
            'tanggal_absensi' => $date,
        ]);

        $note = 'Dikoreksi manual oleh admin '.auth()->user()->name.' pada '.now()->format('d/m/Y H:i').': '.$data['catatan'];
        $existingNotes = trim((string) $attendance->catatan);
        $attendance->catatan = $existingNotes === '' ? $note : $existingNotes."\n".$note;

        if ($data['status'] === 'absent') {
            $attendance->status = 'absent';
            $attendance->jam_masuk = null;
            $attendance->jam_pulang = null;
            $attendance->menit_telat = 0;
            $attendance->jam_lembur = 0;
        }

        if ($data['status'] === 'present') {
            $manualCheckIn = Carbon::parse($date.' '.$data['jam_masuk_manual']);
            $lateMinutes = $this->manualLateMinutes($manualCheckIn);

            $attendance->status = $lateMinutes > 0 ? 'late' : 'present';
            $attendance->jam_masuk = $manualCheckIn->format('H:i:s');
            $attendance->menit_telat = $lateMinutes;
            $attendance->jam_lembur = $attendance->jam_lembur ?? 0;
        }

        $attendance->save();

        return back()->with('success', 'Status absensi berhasil dikoreksi manual.');
    }

    public function report(Request $request)
    {
        $absensi = Attendance::with('employee')
            ->when($request->from, fn ($query) => $query->whereDate('tanggal_absensi', '>=', $request->from))
            ->when($request->to, fn ($query) => $query->whereDate('tanggal_absensi', '<=', $request->to))
            ->latest('tanggal_absensi')
            ->get();

        return view('admin.attendance.report', compact('absensi'));
    }

    private function virtualAbsentAttendance(Employee $employee, string $date): object
    {
        return (object) [
            'tanggal_absensi' => Carbon::parse($date),
            'employee' => $employee,
            'jam_masuk' => null,
            'jam_pulang' => null,
            'menit_telat' => 0,
            'jam_lembur' => 0,
            'status' => 'absent',
            'virtual' => true,
        ];
    }

    private function paginateRows($rows, Request $request, int $perPage): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = collect($rows)->values();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ],
        );
    }

    private function manualLateMinutes(Carbon $checkIn): int
    {
        $lateStart = Carbon::parse($checkIn->toDateString().' '.self::WORK_START)
            ->addMinutes(self::LATE_TOLERANCE_MINUTES);

        return $checkIn->greaterThan($lateStart) ? $lateStart->diffInMinutes($checkIn) : 0;
    }

    private function matchesAttendanceSearch(Employee $employee, string $search): bool
    {
        $needle = mb_strtolower($search);
        $activeCard = $employee->getActiveRFIDCard();

        $haystack = [
            $employee->full_name,
            $employee->karyawan_id,
            $employee->nomor_karyawan,
            $employee->email,
            $employee->jabatan,
            $activeCard?->uid,
        ];

        foreach ($haystack as $value) {
            if ($value !== null && str_contains(mb_strtolower((string) $value), $needle)) {
                return true;
            }
        }

        return false;
    }
}
