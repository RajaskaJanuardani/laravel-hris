<?php

namespace App\Exports;

use App\Models\Payslip;
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
            ->whereBetween('payroll_date', [$this->from->toDateString(), $this->to->toDateString()])
            ->when($this->periodId, fn ($q) => $q->where('payroll_period_id', $this->periodId))
            ->latest('payroll_date')
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
            $row->payroll_date?->format('Y-m-d'),
            $row->employee?->employee_id,
            $row->employee?->full_name,
            (float) $row->base_salary,
            (float) $row->overtime_hours,
            (float) $row->overtime_amount,
            (float) $row->total_deduction,
            (float) $row->net_salary,
            $row->status,
        ];
    }
}

