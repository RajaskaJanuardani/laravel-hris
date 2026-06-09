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
            ->whereBetween('tanggal_lembur', [$this->from->toDateString(), $this->to->toDateString()])
            ->when($this->employeeId, fn ($q) => $q->where('karyawan_id', $this->employeeId))
            ->latest('tanggal_lembur')
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
            'Disetujui Oleh',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal_lembur?->format('Y-m-d'),
            $row->employee?->karyawan_id,
            $row->employee?->full_name,
            $row->jam_mulai?->format('H:i') ?? '17:00',
            $row->jam_selesai?->format('H:i'),
            $row->catatan,
            $row->approvedBy?->name,
        ];
    }
}
