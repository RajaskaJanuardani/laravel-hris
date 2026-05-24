<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceRecapExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
        private readonly ?int $departmentId = null,
        private readonly ?int $shiftTimeId = null
    ) {
    }

    public function collection(): Collection
    {
        $base = Employee::query()
            ->active();

        $base->withCount([
            'attendances as present_days' => fn ($q) => $q->whereBetween('attendance_date', [$this->from->toDateString(), $this->to->toDateString()])
                ->whereIn('status', ['present', 'late']),
            'attendances as late_days' => fn ($q) => $q->whereBetween('attendance_date', [$this->from->toDateString(), $this->to->toDateString()])
                ->where('status', 'late'),
            'attendances as absent_days' => fn ($q) => $q->whereBetween('attendance_date', [$this->from->toDateString(), $this->to->toDateString()])
                ->where('status', 'absent'),
        ]);

        $base->addSelect([
            'late_minutes_total' => Attendance::selectRaw('COALESCE(SUM(late_minutes), 0)')
                ->whereColumn('employee_id', 'employees.id')
                ->whereBetween('attendance_date', [$this->from->toDateString(), $this->to->toDateString()]),
            'overtime_hours_total' => Attendance::selectRaw('COALESCE(SUM(overtime_hours), 0)')
                ->whereColumn('employee_id', 'employees.id')
                ->whereBetween('attendance_date', [$this->from->toDateString(), $this->to->toDateString()]),
            'leave_days_total' => Leave::selectRaw('COALESCE(SUM(number_of_days), 0)')
                ->whereColumn('employee_id', 'employees.id')
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $this->to->toDateString())
                ->whereDate('end_date', '>=', $this->from->toDateString()),
        ]);

        return $base->orderBy('first_name')->get();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'Hadir (hari)',
            'Telat (hari)',
            'Telat (menit)',
            'Absen (hari)',
            'Cuti (hari)',
            'Lembur (jam)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->employee_id,
            $row->full_name,
            (int) $row->present_days,
            (int) $row->late_days,
            (int) $row->late_minutes_total,
            (int) $row->absent_days,
            (int) $row->leave_days_total,
            (float) $row->overtime_hours_total,
        ];
    }
}
