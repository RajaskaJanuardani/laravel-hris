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
        $workStart = Carbon::parse($scanTime->toDateString().' '.self::WORK_START);
        $workEnd = Carbon::parse($scanTime->toDateString().' '.self::WORK_END);
        $existingAttendance = Attendance::where('karyawan_id', $employee->id)
            ->whereDate('tanggal_absensi', $scanTime->toDateString())
            ->first();

        if (! $existingAttendance && $scanTime->lessThan($workStart)) {
            return $this->blockedResponse(
                'too_early',
                'Absen belum di mulai',
                'Absen belum',
                'di mulai',
            );
        }

        if (! $existingAttendance && $scanTime->greaterThanOrEqualTo($workEnd)) {
            return $this->blockedResponse(
                'closed',
                'Absen belum di mulai, tunggu hari besok',
                'Absen belum',
                'di mulai',
            );
        }

        $attendance = Attendance::firstOrCreate(
            [
                'karyawan_id' => $employee->id,
                'tanggal_absensi' => $scanTime->toDateString(),
            ],
            [
                'status' => 'absent',
            ]
        );

        if (! $attendance->jam_masuk) {
            $lateMinutes = $this->lateMinutes($employee, $scanTime);
            $attendance->update([
                'jam_masuk' => $scanTime->format('H:i:s'),
                'status' => $lateMinutes > 0 ? 'late' : 'present',
                'menit_telat' => $lateMinutes,
                'catatan' => 'Masuk via RFID',
            ]);

            return ['type' => 'check_in', 'attendance' => $attendance->fresh(), 'message' => 'Masuk berhasil'];
        }

        if ($attendance->jam_pulang) {
            return [
                'ok' => true,
                'saved' => false,
                'type' => 'already_completed',
                'attendance' => $attendance->fresh(),
                'message' => 'Absensi hari ini sudah lengkap',
                'lcd' => [
                    'line_1' => 'Absensi sudah',
                    'line_2' => 'lengkap',
                ],
            ];
        }

        $attendance->update([
            'jam_pulang' => $scanTime->format('H:i:s'),
            'jam_lembur' => $this->overtimeHours($employee, $scanTime),
            'catatan' => 'Pulang via RFID',
        ]);

        return ['type' => 'check_out', 'attendance' => $attendance->fresh(), 'message' => 'Pulang berhasil'];
    }

    private function blockedResponse(string $type, string $message, string $line1, string $line2): array
    {
        return [
            'ok' => false,
            'saved' => false,
            'type' => $type,
            'attendance' => null,
            'message' => $message,
            'lcd' => [
                'line_1' => $line1,
                'line_2' => $line2,
            ],
        ];
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
            ->where('karyawan_id', $employee->id)
            ->whereDate('tanggal_lembur', $scanTime->toDateString())
            ->first();

        if (! $approval) {
            return 0;
        }

        $start = Carbon::parse($scanTime->toDateString().' '.self::WORK_END);
        $approvedEnd = Carbon::parse($scanTime->toDateString().' '.($approval->jam_selesai?->format('H:i:s') ?? self::OVERTIME_END));
        $maxEnd = Carbon::parse($scanTime->toDateString().' '.self::OVERTIME_END);
        $end = $scanTime->min($approvedEnd)->min($maxEnd);

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        return round($start->diffInMinutes($end) / 60, 2);
    }
}
