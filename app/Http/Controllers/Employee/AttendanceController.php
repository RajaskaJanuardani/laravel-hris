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

        if ($employee) {
            $rows = $this->withCurrentMonthCalendar($employee, $rows, Carbon::parse($today));
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

    private function withCurrentMonthCalendar(Employee $employee, $rows, Carbon $today)
    {
        $items = collect($rows);
        $startDate = $today->copy()->startOfMonth();

        if ($employee->tanggal_masuk && $employee->tanggal_masuk->greaterThan($startDate)) {
            $startDate = $employee->tanggal_masuk->copy();
        }

        if ($startDate->greaterThan($today)) {
            return $items;
        }

        $rowsByDate = $items->keyBy(fn ($attendance) => $attendance->tanggal_absensi->toDateString());
        $calendarRows = collect();

        for ($date = $today->copy(); $date->greaterThanOrEqualTo($startDate); $date->subDay()) {
            $key = $date->toDateString();

            $calendarRows->push($rowsByDate->get($key) ?? $this->virtualAbsentAttendance($employee, $key));
        }

        $olderRows = $items->filter(fn ($attendance) => $attendance->tanggal_absensi->lessThan($startDate));

        return $calendarRows->merge($olderRows)->values();
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
