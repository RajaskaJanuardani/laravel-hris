<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function history(Request $request)
    {
        $statusFilter = $request->query('status');
        $allowedStatuses = ['present', 'late', 'absent'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = null;
        }

        $employee = auth()->user()->employee;
        $today = today()->toDateString();
        $statusFilters = [
            ['label' => 'Semua', 'value' => null],
            ['label' => 'Hadir', 'value' => 'present'],
            ['label' => 'Telat', 'value' => 'late'],
            ['label' => 'Tidak Hadir', 'value' => 'absent'],
        ];

        $rows = $employee
            ? $employee->absensi()->latest('tanggal_absensi')->get()
            : collect();

        if ($employee && ! $rows->contains(fn ($attendance) => $attendance->tanggal_absensi->isSameDay($today))) {
            $rows->prepend($this->virtualAbsentAttendance($employee, $today));
        }

        $absensi = $this->paginateRows(
            $rows
                ->filter(fn ($attendance) => ! $statusFilter || $attendance->status === $statusFilter)
                ->sortByDesc(fn ($attendance) => $attendance->tanggal_absensi->timestamp)
                ->values(),
            $request,
            15,
        );

        return view('employee.attendance.history', compact('absensi', 'statusFilter', 'statusFilters'));
    }

    public function checkIn()
    {
        return view('employee.attendance.check-in', ['employee' => auth()->user()->employee]);
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
}
