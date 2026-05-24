<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\AttendanceDailyExport;
use App\Exports\AttendanceRecapExport;
use App\Exports\LeavesExport;
use App\Exports\OvertimeExport;
use App\Exports\PayrollExport;
use App\Exports\RFIDAuditExport;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function attendanceDaily(Request $request)
    {
        $date = Carbon::parse($request->input('date', today()->toDateString()));

        return view('admin.reports.attendance-daily', [
            'date' => $date,
            'rows' => $this->reportService->attendanceDaily($date),
        ]);
    }

    public function attendanceDailyExcel(Request $request)
    {
        $date = Carbon::parse($request->input('date', today()->toDateString()));

        $name = 'absensi-harian-'.$date->format('Y-m-d').'.xlsx';

        return Excel::download(new AttendanceDailyExport($date, null, null), $name);
    }

    public function attendanceDailyPdf(Request $request)
    {
        $date = Carbon::parse($request->input('date', today()->toDateString()));

        $rows = $this->reportService->attendanceDaily($date);

        $pdf = Pdf::loadView('admin.reports.pdf.attendance-daily', [
            'date' => $date,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('absensi-harian-'.$date->format('Y-m-d').'.pdf');
    }

    public function attendanceRecap(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));

        return view('admin.reports.attendance-recap', [
            'from' => $from,
            'to' => $to,
            'rows' => $this->reportService->attendanceRecap($from, $to),
        ]);
    }

    public function attendanceRecapExcel(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));

        $name = 'rekap-absensi-'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.xlsx';

        return Excel::download(new AttendanceRecapExport($from, $to, null, null), $name);
    }

    public function lateRanking(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));

        return view('admin.reports.late-ranking', [
            'from' => $from,
            'to' => $to,
            'rows' => $this->reportService->lateRanking($from, $to),
        ]);
    }

    public function overtime(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));
        $employeeId = $request->integer('employee_id') ?: null;

        return view('admin.reports.overtime', [
            'from' => $from,
            'to' => $to,
            'employeeId' => $employeeId,
            'employees' => Employee::active()->orderBy('first_name')->get(),
            'rows' => $this->reportService->overtime($from, $to, $employeeId),
        ]);
    }

    public function overtimeExcel(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));
        $employeeId = $request->integer('employee_id') ?: null;

        $name = 'lembur-'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.xlsx';

        return Excel::download(new OvertimeExport($from, $to, $employeeId), $name);
    }

    public function leaves(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));
        $status = $request->input('status') ?: null;
        $employeeId = $request->integer('employee_id') ?: null;

        return view('admin.reports.leaves', [
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'employeeId' => $employeeId,
            'employees' => Employee::active()->orderBy('first_name')->get(),
            'rows' => $this->reportService->leaves($from, $to, $status, $employeeId),
        ]);
    }

    public function leavesExcel(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));
        $status = $request->input('status') ?: null;
        $employeeId = $request->integer('employee_id') ?: null;

        $name = 'cuti-izin-'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.xlsx';

        return Excel::download(new LeavesExport($from, $to, $status, $employeeId), $name);
    }

    public function payroll(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));
        $periodId = $request->integer('payroll_period_id') ?: null;

        return view('admin.reports.payroll', [
            'from' => $from,
            'to' => $to,
            'periodId' => $periodId,
            'periods' => PayrollPeriod::latest()->take(24)->get(),
            'rows' => $this->reportService->payroll($from, $to, $periodId),
        ]);
    }

    public function payrollExcel(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));
        $periodId = $request->integer('payroll_period_id') ?: null;

        $name = 'payroll-'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.xlsx';

        return Excel::download(new PayrollExport($from, $to, $periodId), $name);
    }

    public function payslipPdf(Payslip $payslip)
    {
        $payslip->load(['employee', 'payrollPeriod']);

        $pdf = Pdf::loadView('admin.reports.pdf.payslip', [
            'payslip' => $payslip,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('slip-gaji-'.$payslip->employee?->employee_id.'-'.$payslip->payrollPeriod?->name.'.pdf');
    }

    public function rfidAudit(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));
        $status = $request->input('status') ?: null;
        $source = $request->input('source') ?: null;

        return view('admin.reports.rfid-audit', [
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'source' => $source,
            'rows' => $this->reportService->rfidAudit($from, $to, $status, $source),
        ]);
    }

    public function rfidAuditExcel(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));
        $status = $request->input('status') ?: null;
        $source = $request->input('source') ?: null;

        $name = 'audit-rfid-'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.xlsx';

        return Excel::download(new RFIDAuditExport($from, $to, $status, $source), $name);
    }
}
