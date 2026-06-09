<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\OvertimeApproval;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\RFIDCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    private const ADMIN_NAME = 'Rajaska Januardani';

    public function run(): void
    {
        $this->resetDemoData();

        $admin = $this->createAdmin();
        $leaveTypes = $this->createLeaveTypes();
        $employees = $this->createEmployees();

        $previousPeriod = $this->createPayrollPeriod(
            'Payroll 18 Mei - 31 Mei 2026',
            Carbon::create(2026, 5, 18),
            Carbon::create(2026, 5, 31),
            'paid'
        );

        $currentPeriod = $this->createPayrollPeriod(
            'Payroll 01 Jun - 14 Jun 2026',
            Carbon::create(2026, 6, 1),
            Carbon::create(2026, 6, 14),
            'draft'
        );

        $this->createAttendanceAndOvertime($employees, $previousPeriod, $admin, true);
        $this->createAttendanceAndOvertime($employees, $currentPeriod, $admin, false);
        $this->createLeaves($employees, $leaveTypes, $admin);
        $this->createPayslips($employees, $previousPeriod, true, 'paid');
        $this->createPayslips($employees, $currentPeriod, false, 'draft');
    }

    private function resetDemoData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'log_absensi',
            'absensi',
            'kartu_rfid',
            'cuti',
            'persetujuan_lembur',
            'slip_gaji',
            'periode_penggajian',
            'gaji',
            'karyawan',
            'pengguna',
            'jenis_cuti',
        ] as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function createAdmin(): User
    {
        return User::create([
            'name' => self::ADMIN_NAME,
            'email' => 'admin@hris.test',
            'email_diverifikasi_pada' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);
    }

    /**
     * @return array<string, LeaveType>
     */
    private function createLeaveTypes(): array
    {
        return collect([
            ['nama' => 'Cuti Tahunan', 'kode' => 'CT', 'kuota_per_tahun' => 12, 'deskripsi' => 'Hak cuti tahunan karyawan.'],
            ['nama' => 'Izin', 'kode' => 'IZN', 'kuota_per_tahun' => 6, 'deskripsi' => 'Izin keperluan pribadi.'],
            ['nama' => 'Sakit', 'kode' => 'SKT', 'kuota_per_tahun' => 12, 'deskripsi' => 'Izin sakit dengan keterangan.'],
        ])->mapWithKeys(function (array $type): array {
            return [$type['kode'] => LeaveType::create($type + [
                'perlu_persetujuan' => true,
                'aktif' => true,
            ])];
        })->all();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Employee>
     */
    private function createEmployees()
    {
        $names = collect($this->employeeNames());
        $mandorCount = (int) round($names->count() * 0.3);
        $staffCount = $names->count() - $mandorCount;

        return $names->values()->map(function (string $name, int $index) use ($staffCount): Employee {
            $jabatan = $index < $staffCount ? 'staff' : 'mandor';
            $email = $this->employeeEmail($name);

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'email_diverifikasi_pada' => now(),
                'password' => Hash::make('password'),
                'role' => 'employee',
                'aktif' => true,
            ]);

            [$firstName, $lastName] = $this->splitName($name);

            $employee = Employee::create([
                'pengguna_id' => $user->id,
                'karyawan_id' => 'EMP'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'nama_depan' => $firstName,
                'nama_belakang' => $lastName,
                'email' => $email,
                'telepon' => '08'.str_pad((string) (2100000000 + $index), 10, '0', STR_PAD_LEFT),
                'tanggal_lahir' => Carbon::create(1998 + ($index % 8), (($index % 12) + 1), (($index % 25) + 1)),
                'jenis_kelamin' => $this->guessGender($name),
                'alamat' => 'Tangerang Selatan',
                'jabatan' => $jabatan,
                'tanggal_masuk' => Carbon::create(2025, (($index % 10) + 1), (($index % 20) + 1)),
                'tarif_harian' => Employee::dailyRateFor($jabatan),
                'tipe_karyawan' => 'permanent',
                'aktif' => true,
            ]);

            RFIDCard::create([
                'karyawan_id' => $employee->id,
                'uid' => strtoupper(substr(md5($employee->karyawan_id.$name), 0, 8)),
                'label_kartu' => $name,
                'status' => 'active',
            ]);

            return $employee;
        });
    }

    private function createPayrollPeriod(string $name, Carbon $start, Carbon $end, string $status): PayrollPeriod
    {
        return PayrollPeriod::create([
            'name' => $name,
            'tanggal_mulai' => $start,
            'tanggal_selesai' => $end,
            'status' => $status,
            'tanggal_penggajian' => $end->copy()->addDay()->setTime(9, 0),
            'catatan' => $status === 'paid' ? 'Demo payroll periode sebelumnya.' : 'Demo payroll periode berjalan.',
        ]);
    }

    private function createAttendanceAndOvertime($employees, PayrollPeriod $period, User $admin, bool $fullPeriod): void
    {
        $workDates = $this->workDates($period->tanggal_mulai, $period->tanggal_selesai);
        $visibleDates = $fullPeriod ? $workDates : $workDates->take(2);

        $employees->values()->each(function (Employee $employee, int $index) use ($visibleDates, $admin): void {
            $visibleDates->each(function (Carbon $date, int $dayIndex) use ($employee, $index, $admin): void {
                $isAbsent = ($index + $dayIndex) % 17 === 0;
                $isLate = ! $isAbsent && (($index + $dayIndex) % 5 === 0);
                $hasOvertime = ! $isAbsent && (($index + $dayIndex) % 9 === 0);
                $lateMinutes = $isLate ? 10 + (($index + $dayIndex) % 4) * 5 : 0;
                $overtimeHours = $hasOvertime ? 1.5 + (($index % 2) * 0.5) : 0;

                Attendance::create([
                    'karyawan_id' => $employee->id,
                    'tanggal_absensi' => $date->toDateString(),
                    'jam_masuk' => $isAbsent ? null : $date->copy()->setTime(8, $lateMinutes > 0 ? $lateMinutes : 0)->format('H:i:s'),
                    'jam_pulang' => $isAbsent ? null : $date->copy()->setTime($hasOvertime ? 19 : 17, $hasOvertime ? 30 : 0)->format('H:i:s'),
                    'status' => $isAbsent ? 'absent' : ($isLate ? 'late' : 'present'),
                    'menit_telat' => $lateMinutes,
                    'jam_lembur' => $overtimeHours,
                    'catatan' => $isAbsent ? 'Demo tidak hadir' : 'Demo absensi RFID',
                ]);

                if ($hasOvertime) {
                    OvertimeApproval::updateOrCreate(
                        ['karyawan_id' => $employee->id, 'tanggal_lembur' => $date->toDateString()],
                        [
                            'jam_mulai' => '17:00:00',
                            'jam_selesai' => $date->copy()->setTime(17 + (int) ceil($overtimeHours), 0)->format('H:i:s'),
                            'status' => 'approved',
                            'catatan' => 'Demo lembur operasional',
                            'disetujui_oleh' => $admin->id,
                        ]
                    );
                }
            });
        });
    }

    private function createLeaves($employees, array $leaveTypes, User $admin): void
    {
        $employees->values()->take(12)->each(function (Employee $employee, int $index) use ($leaveTypes, $admin): void {
            $type = match ($index % 3) {
                0 => $leaveTypes['CT'],
                1 => $leaveTypes['IZN'],
                default => $leaveTypes['SKT'],
            };
            $start = Carbon::create(2026, 6, 3 + ($index % 7));
            $status = $index % 4 === 0 ? 'pending' : 'approved';

            Leave::create([
                'karyawan_id' => $employee->id,
                'jenis_cuti_id' => $type->id,
                'tanggal_mulai' => $start,
                'tanggal_selesai' => $start->copy()->addDay(),
                'jumlah_hari' => 2,
                'alasan' => $type->kode === 'SKT' ? 'Demo sakit' : 'Demo pengajuan cuti',
                'status' => $status,
                'disetujui_oleh' => $status === 'approved' ? $admin->id : null,
                'disetujui_pada' => $status === 'approved' ? now() : null,
                'catatan_persetujuan' => $status === 'approved' ? 'Disetujui untuk data demo.' : null,
            ]);
        });
    }

    private function createPayslips($employees, PayrollPeriod $period, bool $includeThr, string $status): void
    {
        $employees->each(function (Employee $employee) use ($period, $includeThr, $status): void {
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

            Payslip::create([
                'karyawan_id' => $employee->id,
                'periode_penggajian_id' => $period->id,
                'tanggal_penggajian' => $period->tanggal_penggajian,
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
                'status' => $status,
                'dibayar_pada' => $status === 'paid' ? $period->tanggal_penggajian : null,
                'catatan' => $includeThr ? 'Termasuk bonus THR demo.' : null,
            ]);
        });
    }

    private function workDates(Carbon $start, Carbon $end)
    {
        $dates = collect();
        $cursor = $start->copy();

        while ($cursor->lessThanOrEqualTo($end)) {
            if (! $cursor->isWeekend()) {
                $dates->push($cursor->copy());
            }

            $cursor->addDay();
        }

        return $dates;
    }

    private function employeeEmail(string $name): string
    {
        return Str::slug(str_replace('.', '', $name)).'@hris.test';
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [$parts[0], $parts[1] ?? '-'];
    }

    private function guessGender(string $name): string
    {
        $femaleHints = ['Alifa', 'Aurella', 'Chanda', 'Defi', 'Dwi', 'Elsa', 'Herdianti', 'Indah', 'Karina', 'Najwa', 'Nazala', 'Ridha', 'Roro', 'Sadzwana', 'Sadzwina', 'Siti', 'Sri', 'Tarisa', 'Ummu', 'Uyun', 'Varsha', 'Veronika'];

        foreach ($femaleHints as $hint) {
            if (Str::startsWith($name, $hint)) {
                return 'female';
            }
        }

        return 'male';
    }

    /**
     * Rajaska tidak dimasukkan ke daftar ini karena ia hanya akun admin.
     */
    private function employeeNames(): array
    {
        return [
            'Alifa Nirwana Khalussa',
            'Ammar Zahran Herlambang',
            'Aurella Levana Chantika Bintang',
            'Bima Pazhar',
            'Chanda Dewi',
            'Defi Yolanda',
            'Dwi Lintang Cahya Kirani',
            'Eka Prasetio',
            'Elsa Novita Damayanti',
            'Faisal Azriel Maksumi',
            'Faiz Aditia Herawan',
            'Farhannurhakim',
            'Firman Alamsyah',
            'Fisabilillah',
            'Franciskus Mikki',
            'Haikal Firmansyah',
            'Herdianti Rahmadhani',
            'I Komang Ardiarta',
            'Indah Amelia Putri',
            'Indah Putri Aulia',
            'Irfan Sopandi',
            'Karina Julia Ningsih',
            'Kohar Hanapi',
            'M. Rafli Hardiansyah',
            'Mohamad Alviansyah Hidayatulloh',
            'Mohammad Rizky Pratama',
            'Muhamad Akmal Syukri',
            'Muhamad Aufaa Yaafi Sulaeman',
            'Muhamad Riyan Abdulah',
            'Muhammad Alamsyah Putra',
            'Muhammad Danial',
            'Muhammad Excel Mutaki',
            'Najwa Amalia Fitriani Alfath',
            'Naunahsallsabila',
            'Nazala Nurul Latifah',
            'Nikko Suardi',
            'Raff Fadilah',
            'Ridha Athala Azahra Wahyudi',
            'Ridwan Fauzi',
            'Rifada Fiqri Ruchabi',
            'Rizky Nur Rohman',
            'Roro Jasmine Setyoningsih',
            'Sadzwana Soffy Novita',
            'Sadzwina Poppy Novita',
            'Siti Hasanah',
            'Siti Nur Allisa',
            'Sri Pebrianti Kusherawati',
            'Tarisa Uswa Hazani',
            'Thomas Lefvi Baehaqi',
            'Tommy Darmawan',
            'Ummu Habibah Al Habsiyah Azahra',
            'Uyun Romdiah',
            'Varsha Sylvia Rani',
            'Veronika Delista Manalu',
            'Yafi Syam Dhiya',
            'Yuta Gladius Taslim',
            'Zidane Airaldy',
        ];
    }
}
