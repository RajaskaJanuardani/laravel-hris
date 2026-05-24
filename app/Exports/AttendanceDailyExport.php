<?php

namespace App\Exports;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceDailyExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Carbon $date,
        private readonly ?int $departmentId = null,
        private readonly ?int $shiftTimeId = null
    ) {
    }

    public function collection(): Collection
    {
        return Attendance::query()
            ->with(['employee'])
            ->whereDate('attendance_date', $this->date)
            ->orderBy('attendance_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'NIK',
            'Nama',
            'Masuk',
            'Pulang',
            'Status',
            'Telat (menit)',
            'Lembur (jam)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->attendance_date?->format('Y-m-d'),
            $row->employee?->employee_id,
            $row->employee?->full_name,
            $row->check_in_time?->format('H:i'),
            $row->check_out_time?->format('H:i'),
            $row->status,
            (int) $row->late_minutes,
            (float) $row->overtime_hours,
        ];
    }
}
