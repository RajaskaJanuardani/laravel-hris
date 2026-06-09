<?php

namespace App\Exports;

use App\Models\Payslip;
use App\Support\DisplayLabel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PayrollExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
        private readonly ?int $periodId = null
    ) {
    }

    public function collection(): Collection
    {
        return Payslip::query()
            ->with(['employee', 'payrollPeriod'])
            ->whereBetween('tanggal_penggajian', [$this->from->toDateString(), $this->to->toDateString()])
            ->when($this->periodId, fn ($q) => $q->where('periode_penggajian_id', $this->periodId))
            ->latest('tanggal_penggajian')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Periode Payroll',
            'Tanggal Payroll',
            'NIK',
            'Nama',
            'Gaji Pokok',
            'Lembur (jam)',
            'Lembur (Rp)',
            'Potongan (Rp)',
            'Gaji Bersih (Rp)',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->payrollPeriod?->name,
            $row->tanggal_penggajian?->format('Y-m-d'),
            $row->employee?->karyawan_id,
            $row->employee?->full_name,
            (float) $row->gaji_pokok,
            (float) $row->jam_lembur,
            (float) $row->upah_lembur,
            (float) $row->total_potongan,
            (float) $row->gaji_bersih,
            DisplayLabel::statusLabel($row->status),
        ];
    }
}
