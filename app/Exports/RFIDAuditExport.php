<?php

namespace App\Exports;

use App\Models\AttendanceLog;
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
            ->whereBetween('scanned_at', [$this->from->startOfDay(), $this->to->endOfDay()])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->source, fn ($q) => $q->where('source', $this->source))
            ->latest('scanned_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'NIK',
            'Nama',
            'UID',
            'Source',
            'Device',
            'IP',
            'Type',
            'Status',
            'Message',
        ];
    }

    public function map($row): array
    {
        return [
            $row->scanned_at?->format('Y-m-d H:i:s'),
            $row->employee?->employee_id,
            $row->employee?->full_name,
            $row->uid,
            $row->source,
            $row->device_name,
            $row->ip_address,
            $row->scan_type,
            $row->status,
            $row->message,
        ];
    }
}

