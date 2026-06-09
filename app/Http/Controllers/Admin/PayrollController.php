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
    public function index()
    {
        $summary = [
            'total_slip' => Payslip::count(),
            'total_gaji_bersih' => Payslip::sum('gaji_bersih'),
            'total_thr' => Payslip::sum('bonus_thr'),
            'draft' => Payslip::where('status', 'draft')->count(),
        ];

        return view('admin.payroll.index', [
            'periods' => PayrollPeriod::latest()->get(),
            'slip_gaji' => Payslip::with(['employee', 'payrollPeriod'])->latest()->paginate(12),
            'summary' => $summary,
        ]);
    }

    public function create()
    {
        return view('admin.payroll.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:periode_penggajian,nama'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'include_thr' => ['nullable', 'boolean'],
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
}
