<?php

namespace App\Exports;

use App\Models\AttendanceLog;
use App\Support\DisplayLabel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RFIDAuditExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
        private readonly ?string $status = null,
        private readonly ?string $source = null
    ) {
    }

    public function collection(): Collection
    {
        return AttendanceLog::query()
            ->with('employee')
            ->whereBetween('dipindai_pada', [$this->from->startOfDay(), $this->to->endOfDay()])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->source, fn ($q) => $q->where('sumber', $this->source))
            ->latest('dipindai_pada')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'NIK',
            'Nama',
            'UID',
            'Sumber',
            'Perangkat',
            'IP',
            'Tipe',
            'Status',
            'Pesan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->dipindai_pada?->format('Y-m-d H:i:s'),
            $row->employee?->karyawan_id,
            $row->employee?->full_name,
            $row->uid,
            $row->source,
            $row->nama_perangkat,
            $row->alamat_ip,
            DisplayLabel::scanType($row->tipe_scan),
            DisplayLabel::statusLabel($row->status),
            $row->message,
        ];
    }
}
