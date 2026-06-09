<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\OvertimeApproval;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();
        $totalEmployees = Employee::active()->count();
        $period = CarbonPeriod::create(now()->startOfMonth(), $today);
        $attendanceByDay = Attendance::selectRaw('DATE(tanggal_absensi) as tanggal')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as hadir")
            ->selectRaw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as telat")
            ->selectRaw('SUM(jam_lembur) as lembur')
            ->whereBetween('tanggal_absensi', [now()->startOfMonth()->toDateString(), $today->toDateString()])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $labels = [];
        $hadirSeries = [];
        $telatSeries = [];
        $absenSeries = [];
        $lemburSeries = [];

        foreach ($period as $date) {
            $key = $date->toDateString();
            $daily = $attendanceByDay->get($key);
            $hadir = (int) ($daily->hadir ?? 0);
            $telat = (int) ($daily->telat ?? 0);

            $labels[] = $date->translatedFormat('d M');
            $hadirSeries[] = $hadir;
            $telatSeries[] = $telat;
            $absenSeries[] = max(0, $totalEmployees - $hadir - $telat);
            $lemburSeries[] = round((float) ($daily->lembur ?? 0), 2);
        }

        $peakIndex = $hadirSeries === [] ? null : array_keys($hadirSeries, max($hadirSeries))[0];
        $attendanceInsight = [
            'rata_hadir' => count($hadirSeries) ? (int) round(array_sum($hadirSeries) / count($hadirSeries)) : 0,
            'puncak_hadir' => $peakIndex !== null ? $hadirSeries[$peakIndex] : 0,
            'hari_puncak' => $peakIndex !== null ? $labels[$peakIndex] : '-',
            'total_lembur' => round(array_sum($lemburSeries), 2),
        ];
        $presentToday = Attendance::whereDate('tanggal_absensi', $today)->where('status', 'present')->count();
        $lateToday = Attendance::whereDate('tanggal_absensi', $today)->where('status', 'late')->count();

        return view('admin.dashboard', [
            'totalEmployees' => $totalEmployees,
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'absentToday' => max(0, $totalEmployees - $presentToday - $lateToday),
            'leaveToday' => Leave::approved()->whereDate('tanggal_mulai', '<=', $today)->whereDate('tanggal_selesai', '>=', $today)->count(),
            'pendingLeaves' => Leave::pending()->count(),
            'overtimeToday' => OvertimeApproval::approved()->whereDate('tanggal_lembur', $today)->count(),
            'jobRoleStats' => Employee::selectRaw('jabatan, COUNT(*) as total')->groupBy('jabatan')->pluck('total', 'jabatan'),
            'recentLogs' => AttendanceLog::with('employee')->latest('dipindai_pada')->take(8)->get(),
            'attendanceTrend' => [
                'labels' => $labels,
                'hadir' => $hadirSeries,
                'telat' => $telatSeries,
                'absen' => $absenSeries,
                'lembur' => $lemburSeries,
            ],
            'attendanceInsight' => $attendanceInsight,
        ]);
    }
}
