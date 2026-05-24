<?php

namespace App\Exports;

use App\Models\OvertimeApproval;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OvertimeExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
        private readonly ?int $employeeId = null
    ) {
    }

    public function collection(): Collection
    {
        return OvertimeApproval::query()
            ->with(['employee', 'approvedBy'])
            ->approved()
            ->whereBetween('overtime_date', [$this->from->toDateString(), $this->to->toDateString()])
            ->when($this->employeeId, fn ($q) => $q->where('employee_id', $this->employeeId))
            ->latest('overtime_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'NIK',
            'Nama',
            'Jam Mulai',
            'Jam Selesai',
            'Catatan',
            'Approved By',
        ];
    }

    public function map($row): array
    {
        return [
            $row->overtime_date?->format('Y-m-d'),
            $row->employee?->employee_id,
            $row->employee?->full_name,
            $row->start_time?->format('H:i') ?? '17:00',
            $row->end_time?->format('H:i'),
            $row->notes,
            $row->approvedBy?->name,
        ];
    }
}

