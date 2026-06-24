<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $statusFilter = $request->query('status');
        $periodStatuses = ['draft', 'paid'];

        if (! in_array($statusFilter, $periodStatuses, true)) {
            $statusFilter = null;
        }

        $summary = [
            'total_slip' => Payslip::count(),
            'total_gaji_bersih' => Payslip::sum('gaji_bersih'),
            'total_thr' => Payslip::sum('bonus_thr'),
            'draft' => Payslip::where('status', 'draft')->count(),
            'total_periode' => PayrollPeriod::count(),
        ];

        $periods = PayrollPeriod::query()
            ->withCount([
                'slip_gaji',
                'slip_gaji as draft_slip_count' => fn ($query) => $query->where('status', 'draft'),
                'slip_gaji as paid_slip_count' => fn ($query) => $query->where('status', 'paid'),
            ])
            ->withSum('slip_gaji as total_gaji_bersih', 'gaji_bersih')
            ->when($search !== '', fn ($query) => $this->applyPeriodSearchWithEmployee($query, $search))
            ->when($statusFilter, fn ($query) => $query->where('status', $statusFilter))
            ->latest('tanggal_mulai')
            ->latest()
            ->paginate(6, ['*'], 'periode_page')
            ->withQueryString();

        $slipGaji = Payslip::query()
            ->with(['employee', 'payrollPeriod'])
            ->when($search !== '', function ($query) use ($search): void {
                $like = "%{$search}%";

                $query->where(function ($query) use ($like, $search): void {
                    $query->whereHas('payrollPeriod', fn ($period) => $this->applyPeriodSearch($period, $search))
                        ->orWhereHas('employee', function ($employee) use ($like): void {
                            $employee->where('nomor_karyawan', 'like', $like)
                                ->orWhere('nama_depan', 'like', $like)
                                ->orWhere('nama_belakang', 'like', $like)
                                ->orWhere('email', 'like', $like);
                        });
                });
            })
            ->when($statusFilter, fn ($query) => $query->whereHas('payrollPeriod', fn ($period) => $period->where('status', $statusFilter)))
            ->latest('tanggal_penggajian')
            ->latest()
            ->paginate(12, ['*'], 'slip_page')
            ->withQueryString();

        return view('admin.payroll.index', [
            'periods' => $periods,
            'slip_gaji' => $slipGaji,
            'summary' => $summary,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'periodStatuses' => $periodStatuses,
        ]);
    }

    public function markPeriodAsPaid(PayrollPeriod $period)
    {
        if ($period->status === 'paid') {
            return back()->with('success', 'Periode payroll sudah berstatus dibayar.');
        }

        if ($period->status !== 'draft') {
            return back()->with('error', 'Hanya periode payroll berstatus draf yang bisa ditandai dibayar.');
        }

        $paidAt = now();
        $paidSlipCount = $period->slip_gaji()
            ->whereIn('status', ['draft', 'final'])
            ->update([
                'status' => 'paid',
                'dibayar_pada' => $paidAt,
                'updated_at' => $paidAt,
            ]);

        $period->update([
            'status' => 'paid',
            'tanggal_penggajian' => $paidAt,
        ]);

        return back()->with('success', "Periode {$period->name} berhasil ditandai dibayar untuk {$paidSlipCount} slip.");
    }

    public function create()
    {
        return view('admin.payroll.create');
    }

    public function store(Request $request)
    {
        $today = today()->toDateString();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:periode_penggajian,nama'],
            'tanggal_mulai' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$today],
            'tanggal_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'include_thr' => ['nullable', 'boolean'],
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai payroll tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai payroll harus sama atau setelah tanggal mulai.',
            'tanggal_mulai.date_format' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.date_format' => 'Format tanggal selesai tidak valid.',
        ], [
            'name' => 'nama periode',
            'tanggal_mulai' => 'tanggal mulai',
            'tanggal_selesai' => 'tanggal selesai',
            'include_thr' => 'THR',
        ]);

        $start = Carbon::parse($data['tanggal_mulai']);
        $end = Carbon::parse($data['tanggal_selesai']);

        if ($start->diffInDays($end) > 13) {
            return back()
                ->withErrors(['tanggal_selesai' => 'Periode payroll maksimal 14 hari sesuai aturan penggajian 2 minggu.'])
                ->withInput();
        }

        $includeThr = $request->boolean('include_thr');

        $period = PayrollPeriod::create([
            'name' => $data['name'],
            'tanggal_mulai' => $start,
            'tanggal_selesai' => $end,
            'status' => 'draft',
            'tanggal_penggajian' => now(),
            'catatan' => $includeThr ? 'Payroll termasuk bonus THR.' : null,
        ]);

        Employee::active()->each(function (Employee $employee) use ($period, $includeThr) {
            $attendance = Attendance::where('karyawan_id', $employee->id)
                ->whereBetween('tanggal_absensi', [$period->tanggal_mulai, $period->tanggal_selesai]);

            $dailyRate = $employee->dailyRate();
            $hourlyRate = $employee->hourlyRate();
            $workingDays = (clone $attendance)->whereIn('status', ['present', 'late'])->count();
            $absentDays = (clone $attendance)->where('status', 'absent')->count();
            $lateCount = (clone $attendance)->where('status', 'late')->count();
            $totalLateMinutes = (int) (clone $attendance)->sum('menit_telat');
            $lateDeduction = round($totalLateMinutes * ($hourlyRate / 60), 2);
            $overtimeHours = (float) (clone $attendance)->sum('jam_lembur');
            $overtimeAmount = round($overtimeHours * $hourlyRate * Employee::OVERTIME_MULTIPLIER, 2);
            $baseSalary = $workingDays * $dailyRate;
            $thrBonus = $includeThr ? $employee->thrBonus() : 0;
            $allowance = $overtimeAmount + $thrBonus;
            $netSalary = max(0, $baseSalary + $allowance - $lateDeduction);

            Payslip::updateOrCreate(
                ['karyawan_id' => $employee->id, 'periode_penggajian_id' => $period->id],
                [
                    'tanggal_penggajian' => now(),
                    'hari_kerja' => $workingDays,
                    'hari_tidak_hadir' => $absentDays,
                    'jumlah_telat' => $lateCount,
                    'total_menit_telat' => $totalLateMinutes,
                    'potongan_telat' => $lateDeduction,
                    'jam_lembur' => $overtimeHours,
                    'upah_lembur' => $overtimeAmount,
                    'tarif_harian' => $dailyRate,
                    'gaji_pokok' => $baseSalary,
                    'total_tunjangan' => $allowance,
                    'bonus_thr' => $thrBonus,
                    'total_potongan' => $lateDeduction,
                    'gaji_bersih' => $netSalary,
                    'status' => 'draft',
                ]
            );
        });

        return redirect()->route('admin.payroll.index')->with('success', 'Payroll berhasil dibuat.');
    }

    public function payslips()
    {
        return view('admin.payroll.payslips', [
            'slip_gaji' => Payslip::with(['employee', 'payrollPeriod'])->latest()->paginate(15),
        ]);
    }

    private function applyPeriodSearch($query, string $search)
    {
        $like = "%{$search}%";

        return $query->where(function ($query) use ($like): void {
            $query->where('nama', 'like', $like)
                ->orWhere('tanggal_mulai', 'like', $like)
                ->orWhere('tanggal_selesai', 'like', $like)
                ->orWhere('tanggal_penggajian', 'like', $like)
                ->orWhere('status', 'like', $like);
        });
    }

    private function applyPeriodSearchWithEmployee($query, string $search)
    {
        $like = "%{$search}%";

        return $query->where(function ($query) use ($like, $search): void {
            $this->applyPeriodSearch($query, $search);

            $query->orWhereHas('slip_gaji.employee', function ($employee) use ($like): void {
                $employee->where('nomor_karyawan', 'like', $like)
                    ->orWhere('nama_depan', 'like', $like)
                    ->orWhere('nama_belakang', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        });
    }
}
