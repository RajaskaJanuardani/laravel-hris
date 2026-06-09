<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceRecapExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
        private readonly ?int $departmentId = null,
        private readonly ?int $shiftTimeId = null
    ) {
    }

    public function collection(): Collection
    {
        $base = Employee::query()
            ->active();

        $base->withCount([
            'absensi as present_days' => fn ($q) => $q->whereBetween('tanggal_absensi', [$this->from->toDateString(), $this->to->toDateString()])
                ->whereIn('status', ['present', 'late']),
            'absensi as late_days' => fn ($q) => $q->whereBetween('tanggal_absensi', [$this->from->toDateString(), $this->to->toDateString()])
                ->where('status', 'late'),
            'absensi as hari_tidak_hadir' => fn ($q) => $q->whereBetween('tanggal_absensi', [$this->from->toDateString(), $this->to->toDateString()])
                ->where('status', 'absent'),
        ]);

        $base->addSelect([
            'menit_telat_total' => Attendance::selectRaw('COALESCE(SUM(menit_telat), 0)')
                ->whereColumn('karyawan_id', 'karyawan.id')
                ->whereBetween('tanggal_absensi', [$this->from->toDateString(), $this->to->toDateString()]),
            'jam_lembur_total' => Attendance::selectRaw('COALESCE(SUM(jam_lembur), 0)')
                ->whereColumn('karyawan_id', 'karyawan.id')
                ->whereBetween('tanggal_absensi', [$this->from->toDateString(), $this->to->toDateString()]),
            'leave_days_total' => Leave::selectRaw('COALESCE(SUM(jumlah_hari), 0)')
                ->whereColumn('karyawan_id', 'karyawan.id')
                ->where('status', 'approved')
                ->whereDate('tanggal_mulai', '<=', $this->to->toDateString())
                ->whereDate('tanggal_selesai', '>=', $this->from->toDateString()),
        ]);

        return $base->orderBy('nama_depan')->get();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'Hadir (hari)',
            'Telat (hari)',
            'Telat (menit)',
            'Tidak Hadir (hari)',
            'Cuti (hari)',
            'Lembur (jam)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->karyawan_id,
            $row->full_name,
            (int) $row->present_days,
            (int) $row->late_days,
            (int) $row->menit_telat_total,
            (int) $row->hari_tidak_hadir,
            (int) $row->leave_days_total,
            (float) $row->jam_lembur_total,
        ];
    }
}
