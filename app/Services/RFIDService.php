<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\RFIDCard;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RFIDService
{
    public function __construct(private AttendanceService $attendanceService)
    {
    }

    public function scan(string $uid, Request $request, string $source = 'simulator'): array
    {
        $normalizedUid = strtoupper(trim($uid));
        $card = RFIDCard::with('employee')->active()->byUID($normalizedUid)->first();
        $scanTime = now();

        if (! $card || ! $card->employee || ! $card->employee->is_active) {
            AttendanceLog::create([
                'uid' => $normalizedUid,
                'source' => $source,
                'device_name' => $request->input('device_name', 'Unknown device'),
                'ip_address' => $request->ip(),
                'scan_type' => 'unknown',
                'status' => 'failed',
                'message' => 'Kartu tidak terdaftar atau karyawan tidak aktif',
                'scanned_at' => $scanTime,
                'payload' => $request->all(),
            ]);

            return [
                'ok' => false,
                'message' => 'Kartu tidak terdaftar atau karyawan tidak aktif',
            ];
        }

        $result = $this->attendanceService->recordScan($card->employee, Carbon::parse($scanTime));

        AttendanceLog::create([
            'employee_id' => $card->employee_id,
            'rfid_card_id' => $card->id,
            'uid' => $normalizedUid,
            'source' => $source,
            'device_name' => $request->input('device_name', 'Simulator'),
            'ip_address' => $request->ip(),
            'scan_type' => $result['type'],
            'status' => 'success',
            'message' => $result['message'],
            'scanned_at' => $scanTime,
            'payload' => $request->all(),
        ]);

        return [
            'ok' => true,
            'message' => $result['message'],
            'scan_type' => $result['type'],
            'employee' => $card->employee->full_name,
            'status' => $result['attendance']->status,
            'late_minutes' => $result['attendance']->late_minutes,
        ];
    }
}
