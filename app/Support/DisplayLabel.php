<?php

namespace App\Support;

class DisplayLabel
{
    private const STATUSES = [
        'present' => ['label' => 'Hadir', 'badge' => 'success'],
        'late' => ['label' => 'Telat', 'badge' => 'warning'],
        'absent' => ['label' => 'Tidak Hadir', 'badge' => 'danger'],
        'leave' => ['label' => 'Cuti', 'badge' => 'info'],
        'sick' => ['label' => 'Sakit', 'badge' => 'info'],
        'holiday' => ['label' => 'Libur', 'badge' => 'secondary'],
        'approved' => ['label' => 'Disetujui', 'badge' => 'success'],
        'pending' => ['label' => 'Menunggu', 'badge' => 'warning'],
        'rejected' => ['label' => 'Ditolak', 'badge' => 'danger'],
        'cancelled' => ['label' => 'Dibatalkan', 'badge' => 'secondary'],
        'draft' => ['label' => 'Draf', 'badge' => 'warning'],
        'final' => ['label' => 'Final', 'badge' => 'primary'],
        'paid' => ['label' => 'Dibayar', 'badge' => 'success'],
        'locked' => ['label' => 'Terkunci', 'badge' => 'primary'],
        'archived' => ['label' => 'Diarsipkan', 'badge' => 'secondary'],
        'active' => ['label' => 'Aktif', 'badge' => 'success'],
        'inactive' => ['label' => 'Nonaktif', 'badge' => 'secondary'],
        'expired' => ['label' => 'Kedaluwarsa', 'badge' => 'secondary'],
        'lost' => ['label' => 'Hilang', 'badge' => 'danger'],
        'success' => ['label' => 'Berhasil', 'badge' => 'success'],
        'failed' => ['label' => 'Gagal', 'badge' => 'danger'],
        'unknown' => ['label' => 'Tidak Dikenal', 'badge' => 'secondary'],
    ];

    private const ROLES = [
        'admin' => 'Admin',
        'employee' => 'Karyawan',
    ];

    private const JOB_ROLES = [
        'staff' => 'Staf',
        'mandor' => 'Mandor',
    ];

    private const EMPLOYMENT_TYPES = [
        'permanent' => 'Tetap',
        'contract' => 'Kontrak',
        'internship' => 'Magang',
    ];

    private const SCAN_TYPES = [
        'check_in' => 'Masuk',
        'check_out' => 'Pulang',
        'unknown' => 'Ditolak',
    ];

    private const OVERTIME_STATUSES = [
        'approved' => ['label' => 'Ditetapkan', 'badge' => 'success'],
        'cancelled' => ['label' => 'Dibatalkan', 'badge' => 'secondary'],
    ];

    public static function status(?string $status): array
    {
        $key = self::key($status);

        return self::STATUSES[$key] ?? [
            'label' => self::fallback($status),
            'badge' => 'secondary',
        ];
    }

    public static function statusLabel(?string $status): string
    {
        return self::status($status)['label'];
    }

    public static function statusBadge(?string $status): string
    {
        return self::status($status)['badge'];
    }

    public static function overtimeStatus(?string $status): array
    {
        $key = self::key($status);

        return self::OVERTIME_STATUSES[$key] ?? self::status($status);
    }

    public static function overtimeStatusLabel(?string $status): string
    {
        return self::overtimeStatus($status)['label'];
    }

    public static function role(?string $role): string
    {
        $key = self::key($role);

        return self::ROLES[$key] ?? self::fallback($role);
    }

    public static function jobRole(?string $role): string
    {
        $key = self::key($role);

        return self::JOB_ROLES[$key] ?? self::fallback($role);
    }

    public static function scanType(?string $type): string
    {
        $key = self::key($type);

        return self::SCAN_TYPES[$key] ?? self::fallback($type);
    }

    public static function employmentType(?string $type): string
    {
        $key = self::key($type);

        return self::EMPLOYMENT_TYPES[$key] ?? self::fallback($type);
    }

    private static function key(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    private static function fallback(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '-';
        }

        return ucwords(str_replace('_', ' ', $value));
    }
}
