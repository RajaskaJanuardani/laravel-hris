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

        if (! $card || ! $card->employee || ! $card->employee->aktif) {
            AttendanceLog::create([
                'uid' => $normalizedUid,
                'source' => $source,
                'nama_perangkat' => $request->input('nama_perangkat', 'Perangkat tidak dikenal'),
                'alamat_ip' => $request->ip(),
                'tipe_scan' => 'unknown',
                'status' => 'failed',
                'message' => 'Kartu tidak terdaftar atau karyawan tidak aktif',
                'dipindai_pada' => $scanTime,
                'payload' => $request->all(),
            ]);

            return [
                'ok' => false,
                'message' => 'Kartu tidak terdaftar atau karyawan tidak aktif',
                'lcd' => [
                    'line_1' => 'Kartu tidak',
                    'line_2' => 'terdaftar',
                ],
            ];
        }

        $result = $this->attendanceService->recordScan($card->employee, Carbon::parse($scanTime));
        $ok = $result['ok'] ?? true;
        $attendance = $result['attendance'] ?? null;
        $logType = $this->normalizeLogScanType($result['type'] ?? null);
        $lcd = $result['lcd'] ?? [
            'line_1' => $result['message'],
            'line_2' => $card->employee->full_name,
        ];

        AttendanceLog::create([
            'karyawan_id' => $card->karyawan_id,
            'kartu_rfid_id' => $card->id,
            'uid' => $normalizedUid,
            'source' => $source,
            'nama_perangkat' => $request->input('nama_perangkat', 'Simulator'),
            'alamat_ip' => $request->ip(),
            'tipe_scan' => $logType,
            'status' => $ok ? 'success' : 'failed',
            'message' => $result['message'],
            'dipindai_pada' => $scanTime,
            'payload' => $request->all(),
        ]);

        return [
            'ok' => $ok,
            'saved' => $result['saved'] ?? $ok,
            'message' => $result['message'],
            'tipe_scan' => $logType,
            'employee' => $card->employee->full_name,
            'nama_karyawan' => $card->employee->full_name,
            'status' => $attendance?->status,
            'menit_telat' => $attendance?->menit_telat ?? 0,
            'lcd' => $lcd,
        ];
    }

    private function normalizeLogScanType(?string $type): string
    {
        return match ($type) {
            'check_in', 'check_out', 'unknown' => $type,
            default => 'unknown',
        };
    }
}
