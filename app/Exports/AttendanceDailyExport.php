<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Support\DisplayLabel;
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
            ->whereDate('tanggal_absensi', $this->date)
            ->orderBy('tanggal_absensi')
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
            $row->tanggal_absensi?->format('Y-m-d'),
            $row->employee?->karyawan_id,
            $row->employee?->full_name,
            $row->jam_masuk?->format('H:i'),
            $row->jam_pulang?->format('H:i'),
            DisplayLabel::statusLabel($row->status),
            (int) $row->menit_telat,
            (float) $row->jam_lembur,
        ];
    }
}
