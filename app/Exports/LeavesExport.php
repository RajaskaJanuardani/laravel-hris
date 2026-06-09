<?php

namespace App\Exports;

use App\Models\Leave;
use App\Support\DisplayLabel;
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
            ->whereDate('tanggal_mulai', '<=', $this->to->toDateString())
            ->whereDate('tanggal_selesai', '>=', $this->from->toDateString())
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->employeeId, fn ($q) => $q->where('karyawan_id', $this->employeeId))
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
            'Disetujui Oleh',
            'Waktu Persetujuan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->employee?->karyawan_id,
            $row->employee?->full_name,
            $row->leaveType?->name,
            $row->tanggal_mulai?->format('Y-m-d'),
            $row->tanggal_selesai?->format('Y-m-d'),
            (int) $row->jumlah_hari,
            DisplayLabel::statusLabel($row->status),
            $row->approvedBy?->name,
            $row->disetujui_pada?->format('Y-m-d H:i:s'),
        ];
    }
}
