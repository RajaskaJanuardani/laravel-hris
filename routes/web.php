<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\OvertimeApprovalController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Employee\OvertimeController as EmployeeOvertimeController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Employee\PayslipController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware('auth')->get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('employee.dashboard');
})->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/karyawan', AdminEmployeeController::class)->names('karyawan');
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/monitoring', [AdminAttendanceController::class, 'monitoring'])->name('attendance.monitoring');
    Route::post('/attendance/simulate', [AdminAttendanceController::class, 'simulate'])->name('attendance.simulate');
    Route::patch('/attendance/manual-status', [AdminAttendanceController::class, 'updateManualStatus'])->name('attendance.manual-status');
    Route::get('/attendance/report', [AdminAttendanceController::class, 'report'])->name('attendance.report');
    Route::get('/leaves', [AdminLeaveController::class, 'index'])->name('leaves.index');
    Route::patch('/leaves/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leaves.approve');
    Route::patch('/leaves/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leaves.reject');
    Route::get('/overtime', [OvertimeApprovalController::class, 'index'])->name('overtime.index');
    Route::post('/overtime', [OvertimeApprovalController::class, 'store'])->name('overtime.store');
    Route::delete('/overtime/{overtime}', [OvertimeApprovalController::class, 'destroy'])->name('overtime.destroy');
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
    Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
    Route::patch('/payroll/periods/{period}/paid', [PayrollController::class, 'markPeriodAsPaid'])->name('payroll.periods.paid');
    Route::get('/payslips', [PayrollController::class, 'payslips'])->name('payroll.payslips');
    Route::get('/settings/leave-types', [LeaveTypeController::class, 'index'])->name('settings.leave-types');
    Route::post('/settings/leave-types', [LeaveTypeController::class, 'store'])->name('settings.leave-types.store');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/attendance/daily', [ReportController::class, 'attendanceDaily'])->name('attendance.daily');
        Route::get('/attendance/daily/excel', [ReportController::class, 'attendanceDailyExcel'])->name('attendance.daily.excel');
        Route::get('/attendance/daily/pdf', [ReportController::class, 'attendanceDailyPdf'])->name('attendance.daily.pdf');
        Route::get('/attendance/recap', [ReportController::class, 'attendanceRecap'])->name('attendance.recap');
        Route::get('/attendance/recap/excel', [ReportController::class, 'attendanceRecapExcel'])->name('attendance.recap.excel');
        Route::get('/late-ranking', [ReportController::class, 'lateRanking'])->name('late-ranking');
        Route::get('/overtime', [ReportController::class, 'overtime'])->name('overtime');
        Route::get('/overtime/excel', [ReportController::class, 'overtimeExcel'])->name('overtime.excel');
        Route::get('/leaves', [ReportController::class, 'leaves'])->name('leaves');
        Route::get('/leaves/excel', [ReportController::class, 'leavesExcel'])->name('leaves.excel');
        Route::get('/payroll', [ReportController::class, 'payroll'])->name('payroll');
        Route::get('/payroll/excel', [ReportController::class, 'payrollExcel'])->name('payroll.excel');
        Route::get('/payroll/payslip/{payslip}/pdf', [ReportController::class, 'payslipPdf'])->name('payroll.payslip.pdf');
        Route::get('/rfid-audit', [ReportController::class, 'rfidAudit'])->name('rfid-audit');
        Route::get('/rfid-audit/excel', [ReportController::class, 'rfidAuditExcel'])->name('rfid-audit.excel');
    });
});

Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [EmployeeProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [EmployeeProfileController::class, 'update'])->name('profile.update');
    Route::get('/attendance', [EmployeeAttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/check-in', [EmployeeAttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::get('/overtime', [EmployeeOvertimeController::class, 'index'])->name('overtime.index');
    Route::get('/leaves', [EmployeeLeaveController::class, 'index'])->name('leaves.index');
    Route::post('/leaves', [EmployeeLeaveController::class, 'store'])->name('leaves.store');
    Route::get('/payslips', [PayslipController::class, 'index'])->name('payslip.index');
    Route::get('/payslips/{id}', [PayslipController::class, 'show'])->name('payslip.show');
});
