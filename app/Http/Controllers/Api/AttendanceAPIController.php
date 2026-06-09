<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RFIDService;
use Illuminate\Http\Request;

class AttendanceAPIController extends Controller
{
    public function scan(Request $request, RFIDService $rfidService)
    {
        $data = $request->validate([
            'uid' => ['required', 'string', 'max:100'],
            'nama_perangkat' => ['nullable', 'string', 'max:100'],
            'token' => ['nullable', 'string', 'max:100'],
        ]);

        $configuredToken = config('services.rfid.token');
        if ($configuredToken && ($data['token'] ?? null) !== $configuredToken) {
            return response()->json(['ok' => false, 'message' => 'Token device tidak valid.'], 401);
        }

        return response()->json($rfidService->scan($data['uid'], $request, $request->input('source', 'esp32')));
    }
}
