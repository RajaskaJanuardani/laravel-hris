<?php

namespace App\Exports;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeavesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
        private readonly ?string $status = null,
        private readonly ?int $employeeId = null
    ) {
    }

    public function collection(): Collection
    {
        return Leave::query()
            ->with(['employee', 'leaveType', 'approvedBy'])
            ->whereDate('start_date', '<=', $this->to->toDateString())
            ->whereDate('end_date', '>=', $this->from->toDateString())
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->employeeId, fn ($q) => $q->where('employee_id', $this->employeeId))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'Tipe',
            'Mulai',
            'Selesai',
            'Durasi (hari)',
            'Status',
            'Approved By',
            'Approved At',
        ];
    }

    public function map($row): array
    {
        return [
            $row->employee?->employee_id,
            $row->employee?->full_name,
            $row->leaveType?->name,
            $row->start_date?->format('Y-m-d'),
            $row->end_date?->format('Y-m-d'),
            (int) $row->number_of_days,
            $row->status,
            $row->approvedBy?->name,
            $row->approved_at?->format('Y-m-d H:i:s'),
        ];
    }
}

