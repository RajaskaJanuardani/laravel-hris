<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OvertimeApproval;
use Carbon\Carbon;

class AttendanceService
{
    private const WORK_START = '08:00:00';
    private const WORK_END = '17:00:00';
    private const OVERTIME_END = '22:00:00';
    private const LATE_TOLERANCE_MINUTES = 10;

    public function recordScan(Employee $employee, Carbon $scanTime): array
    {
        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $scanTime->toDateString(),
            ],
            [
                'status' => 'absent',
            ]
        );

        if (! $attendance->check_in_time) {
            $lateMinutes = $this->lateMinutes($employee, $scanTime);
            $attendance->update([
                'check_in_time' => $scanTime->format('H:i:s'),
                'status' => $lateMinutes > 0 ? 'late' : 'present',
                'late_minutes' => $lateMinutes,
                'notes' => 'Check-in via RFID',
            ]);

            return ['type' => 'check_in', 'attendance' => $attendance->fresh(), 'message' => 'Check-in berhasil'];
        }

        $attendance->update([
            'check_out_time' => $scanTime->format('H:i:s'),
            'overtime_hours' => $this->overtimeHours($employee, $scanTime),
            'notes' => 'Check-out via RFID',
        ]);

        return ['type' => 'check_out', 'attendance' => $attendance->fresh(), 'message' => 'Check-out berhasil'];
    }

    private function lateMinutes(Employee $employee, Carbon $scanTime): int
    {
        $start = Carbon::parse($scanTime->toDateString().' '.self::WORK_START)
            ->addMinutes(self::LATE_TOLERANCE_MINUTES);

        return $scanTime->greaterThan($start) ? $start->diffInMinutes($scanTime) : 0;
    }

    private function overtimeHours(Employee $employee, Carbon $scanTime): float
    {
        $approval = OvertimeApproval::approved()
            ->where('employee_id', $employee->id)
            ->whereDate('overtime_date', $scanTime->toDateString())
            ->first();

        if (! $approval) {
            return 0;
        }

        $start = Carbon::parse($scanTime->toDateString().' '.self::WORK_END);
        $approvedEnd = Carbon::parse($scanTime->toDateString().' '.($approval->end_time?->format('H:i:s') ?? self::OVERTIME_END));
        $maxEnd = Carbon::parse($scanTime->toDateString().' '.self::OVERTIME_END);
        $end = $scanTime->min($approvedEnd)->min($maxEnd);

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        return round($start->diffInMinutes($end) / 60, 2);
    }
}
