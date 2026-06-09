<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropLegacyForeignKeys();
        $this->renameTablesToIndonesian();
        $this->renameColumnsToIndonesian();
        $this->addPayrollDailyWageColumns();
        $this->normalizeDailyRates();
        $this->addIndonesianForeignKeys();
    }

    public function down(): void
    {
        $this->dropIndonesianForeignKeys();
        $this->dropPayrollDailyWageColumns();
        $this->renameColumnsToEnglish();
        $this->renameTablesToEnglish();
    }

    private function dropLegacyForeignKeys(): void
    {
        foreach ([
            ['employees', 'employees_user_id_foreign'],
            ['rfid_cards', 'rfid_cards_employee_id_foreign'],
            ['attendances', 'attendances_employee_id_foreign'],
            ['leaves', 'leaves_employee_id_foreign'],
            ['leaves', 'leaves_leave_type_id_foreign'],
            ['leaves', 'leaves_approved_by_foreign'],
            ['payslips', 'payslips_employee_id_foreign'],
            ['payslips', 'payslips_payroll_period_id_foreign'],
            ['salaries', 'salaries_employee_id_foreign'],
            ['attendance_logs', 'attendance_logs_employee_id_foreign'],
            ['attendance_logs', 'attendance_logs_rfid_card_id_foreign'],
            ['overtime_approvals', 'overtime_approvals_employee_id_foreign'],
            ['overtime_approvals', 'overtime_approvals_approved_by_foreign'],
        ] as [$table, $foreignKey]) {
            $this->dropForeignIfExists($table, $foreignKey);
        }
    }

    private function renameTablesToIndonesian(): void
    {
        foreach ([
            'users' => 'pengguna',
            'employees' => 'karyawan',
            'rfid_cards' => 'kartu_rfid',
            'attendances' => 'absensi',
            'leave_types' => 'jenis_cuti',
            'leaves' => 'cuti',
            'holidays' => 'hari_libur',
            'payroll_periods' => 'periode_penggajian',
            'payslips' => 'slip_gaji',
            'salaries' => 'gaji',
            'salary_components' => 'komponen_gaji',
            'system_settings' => 'pengaturan_sistem',
            'attendance_logs' => 'log_absensi',
            'overtime_approvals' => 'persetujuan_lembur',
        ] as $old => $new) {
            $this->renameTableIfNeeded($old, $new);
        }
    }

    private function renameColumnsToIndonesian(): void
    {
        $this->renameColumns('pengguna', [
            'name' => 'nama',
            'email_verified_at' => 'email_diverifikasi_pada',
            'password' => 'kata_sandi',
            'role' => 'peran',
            'is_active' => 'aktif',
            'remember_token' => 'token_ingat',
        ]);

        $this->renameColumns('karyawan', [
            'user_id' => 'pengguna_id',
            'employee_id' => 'nomor_karyawan',
            'first_name' => 'nama_depan',
            'last_name' => 'nama_belakang',
            'phone' => 'telepon',
            'date_of_birth' => 'tanggal_lahir',
            'gender' => 'jenis_kelamin',
            'address' => 'alamat',
            'profile_photo_path' => 'path_foto_profil',
            'job_role' => 'jabatan',
            'hire_date' => 'tanggal_masuk',
            'contract_end_date' => 'tanggal_selesai_kontrak',
            'salary' => 'tarif_harian',
            'employment_type' => 'tipe_karyawan',
            'is_active' => 'aktif',
        ]);

        $this->renameColumns('kartu_rfid', [
            'employee_id' => 'karyawan_id',
            'card_label' => 'label_kartu',
            'issued_at' => 'diterbitkan_pada',
            'expired_at' => 'kedaluwarsa_pada',
            'notes' => 'catatan',
        ]);

        $this->renameColumns('absensi', [
            'employee_id' => 'karyawan_id',
            'attendance_date' => 'tanggal_absensi',
            'check_in_time' => 'jam_masuk',
            'check_out_time' => 'jam_pulang',
            'late_minutes' => 'menit_telat',
            'overtime_hours' => 'jam_lembur',
            'notes' => 'catatan',
        ]);

        $this->renameColumns('jenis_cuti', [
            'name' => 'nama',
            'code' => 'kode',
            'quota_per_year' => 'kuota_per_tahun',
            'requires_approval' => 'perlu_persetujuan',
            'description' => 'deskripsi',
            'is_active' => 'aktif',
        ]);

        $this->renameColumns('cuti', [
            'employee_id' => 'karyawan_id',
            'leave_type_id' => 'jenis_cuti_id',
            'start_date' => 'tanggal_mulai',
            'end_date' => 'tanggal_selesai',
            'number_of_days' => 'jumlah_hari',
            'reason' => 'alasan',
            'approved_by' => 'disetujui_oleh',
            'approved_at' => 'disetujui_pada',
            'approval_notes' => 'catatan_persetujuan',
        ]);

        $this->renameColumns('hari_libur', [
            'name' => 'nama',
            'date' => 'tanggal',
            'description' => 'deskripsi',
            'is_annual' => 'tahunan',
        ]);

        $this->renameColumns('periode_penggajian', [
            'name' => 'nama',
            'start_date' => 'tanggal_mulai',
            'end_date' => 'tanggal_selesai',
            'payroll_date' => 'tanggal_penggajian',
            'notes' => 'catatan',
        ]);

        $this->renameColumns('slip_gaji', [
            'employee_id' => 'karyawan_id',
            'payroll_period_id' => 'periode_penggajian_id',
            'payroll_date' => 'tanggal_penggajian',
            'working_days' => 'hari_kerja',
            'absent_days' => 'hari_tidak_hadir',
            'late_count' => 'jumlah_telat',
            'late_deduction' => 'potongan_telat',
            'overtime_hours' => 'jam_lembur',
            'overtime_amount' => 'upah_lembur',
            'base_salary' => 'gaji_pokok',
            'total_allowance' => 'total_tunjangan',
            'total_deduction' => 'total_potongan',
            'net_salary' => 'gaji_bersih',
            'paid_at' => 'dibayar_pada',
            'notes' => 'catatan',
        ]);

        $this->renameColumns('gaji', [
            'employee_id' => 'karyawan_id',
            'base_salary' => 'gaji_pokok',
            'total_income' => 'total_pendapatan',
            'total_deduction' => 'total_potongan',
            'net_salary' => 'gaji_bersih',
            'effective_date' => 'tanggal_berlaku',
            'end_date' => 'tanggal_selesai',
            'is_active' => 'aktif',
        ]);

        $this->renameColumns('komponen_gaji', [
            'name' => 'nama',
            'code' => 'kode',
            'type' => 'tipe',
            'calculation_type' => 'tipe_perhitungan',
            'default_amount' => 'nominal_default',
            'display_order' => 'urutan_tampil',
            'is_active' => 'aktif',
            'description' => 'deskripsi',
        ]);

        $this->renameColumns('pengaturan_sistem', [
            'key' => 'kunci',
            'value' => 'nilai',
            'type' => 'tipe',
            'description' => 'deskripsi',
        ]);

        $this->renameColumns('log_absensi', [
            'employee_id' => 'karyawan_id',
            'rfid_card_id' => 'kartu_rfid_id',
            'source' => 'sumber',
            'device_name' => 'nama_perangkat',
            'ip_address' => 'alamat_ip',
            'scan_type' => 'tipe_scan',
            'message' => 'pesan',
            'scanned_at' => 'dipindai_pada',
            'payload' => 'data_payload',
        ]);

        $this->renameColumns('persetujuan_lembur', [
            'employee_id' => 'karyawan_id',
            'overtime_date' => 'tanggal_lembur',
            'start_time' => 'jam_mulai',
            'end_time' => 'jam_selesai',
            'notes' => 'catatan',
            'approved_by' => 'disetujui_oleh',
        ]);
    }

    private function addPayrollDailyWageColumns(): void
    {
        if (Schema::hasTable('slip_gaji')) {
            Schema::table('slip_gaji', function (Blueprint $table) {
                if (! Schema::hasColumn('slip_gaji', 'total_menit_telat')) {
                    $table->unsignedInteger('total_menit_telat')->default(0)->after('jumlah_telat');
                }

                if (! Schema::hasColumn('slip_gaji', 'tarif_harian')) {
                    $table->decimal('tarif_harian', 12, 2)->default(0)->after('upah_lembur');
                }

                if (! Schema::hasColumn('slip_gaji', 'bonus_thr')) {
                    $table->decimal('bonus_thr', 12, 2)->default(0)->after('total_tunjangan');
                }
            });
        }
    }

    private function normalizeDailyRates(): void
    {
        if (Schema::hasTable('karyawan') && Schema::hasColumn('karyawan', 'jabatan') && Schema::hasColumn('karyawan', 'tarif_harian')) {
            foreach (Employee::DAILY_RATES as $jabatan => $tarif) {
                DB::table('karyawan')->where('jabatan', $jabatan)->update(['tarif_harian' => $tarif]);
            }
        }
    }

    private function addIndonesianForeignKeys(): void
    {
        $this->addForeignIfMissing('karyawan', 'karyawan_pengguna_id_foreign', 'pengguna_id', 'pengguna', 'cascade');
        $this->addForeignIfMissing('kartu_rfid', 'kartu_rfid_karyawan_id_foreign', 'karyawan_id', 'karyawan', 'cascade');
        $this->addForeignIfMissing('absensi', 'absensi_karyawan_id_foreign', 'karyawan_id', 'karyawan', 'cascade');
        $this->addForeignIfMissing('cuti', 'cuti_karyawan_id_foreign', 'karyawan_id', 'karyawan', 'cascade');
        $this->addForeignIfMissing('cuti', 'cuti_jenis_cuti_id_foreign', 'jenis_cuti_id', 'jenis_cuti', 'restrict');
        $this->addForeignIfMissing('cuti', 'cuti_disetujui_oleh_foreign', 'disetujui_oleh', 'pengguna', 'set null');
        $this->addForeignIfMissing('slip_gaji', 'slip_gaji_karyawan_id_foreign', 'karyawan_id', 'karyawan', 'cascade');
        $this->addForeignIfMissing('slip_gaji', 'slip_gaji_periode_penggajian_id_foreign', 'periode_penggajian_id', 'periode_penggajian', 'restrict');
        $this->addForeignIfMissing('gaji', 'gaji_karyawan_id_foreign', 'karyawan_id', 'karyawan', 'cascade');
        $this->addForeignIfMissing('log_absensi', 'log_absensi_karyawan_id_foreign', 'karyawan_id', 'karyawan', 'set null');
        $this->addForeignIfMissing('log_absensi', 'log_absensi_kartu_rfid_id_foreign', 'kartu_rfid_id', 'kartu_rfid', 'set null');
        $this->addForeignIfMissing('persetujuan_lembur', 'persetujuan_lembur_karyawan_id_foreign', 'karyawan_id', 'karyawan', 'cascade');
        $this->addForeignIfMissing('persetujuan_lembur', 'persetujuan_lembur_disetujui_oleh_foreign', 'disetujui_oleh', 'pengguna', 'set null');
    }

    private function dropIndonesianForeignKeys(): void
    {
        foreach ([
            ['karyawan', 'karyawan_pengguna_id_foreign'],
            ['kartu_rfid', 'kartu_rfid_karyawan_id_foreign'],
            ['absensi', 'absensi_karyawan_id_foreign'],
            ['cuti', 'cuti_karyawan_id_foreign'],
            ['cuti', 'cuti_jenis_cuti_id_foreign'],
            ['cuti', 'cuti_disetujui_oleh_foreign'],
            ['slip_gaji', 'slip_gaji_karyawan_id_foreign'],
            ['slip_gaji', 'slip_gaji_periode_penggajian_id_foreign'],
            ['gaji', 'gaji_karyawan_id_foreign'],
            ['log_absensi', 'log_absensi_karyawan_id_foreign'],
            ['log_absensi', 'log_absensi_kartu_rfid_id_foreign'],
            ['persetujuan_lembur', 'persetujuan_lembur_karyawan_id_foreign'],
            ['persetujuan_lembur', 'persetujuan_lembur_disetujui_oleh_foreign'],
        ] as [$table, $foreignKey]) {
            $this->dropForeignIfExists($table, $foreignKey);
        }
    }

    private function dropPayrollDailyWageColumns(): void
    {
        if (Schema::hasTable('slip_gaji')) {
            $columns = array_values(array_filter(
                ['total_menit_telat', 'tarif_harian', 'bonus_thr'],
                fn (string $column): bool => Schema::hasColumn('slip_gaji', $column)
            ));

            if ($columns !== []) {
                Schema::table('slip_gaji', function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }
    }

    private function renameColumnsToEnglish(): void
    {
        $this->renameColumns('pengguna', [
            'nama' => 'name',
            'email_diverifikasi_pada' => 'email_verified_at',
            'kata_sandi' => 'password',
            'peran' => 'role',
            'aktif' => 'is_active',
            'token_ingat' => 'remember_token',
        ]);

        $this->renameColumns('karyawan', [
            'pengguna_id' => 'user_id',
            'nomor_karyawan' => 'employee_id',
            'nama_depan' => 'first_name',
            'nama_belakang' => 'last_name',
            'telepon' => 'phone',
            'tanggal_lahir' => 'date_of_birth',
            'jenis_kelamin' => 'gender',
            'alamat' => 'address',
            'path_foto_profil' => 'profile_photo_path',
            'jabatan' => 'job_role',
            'tanggal_masuk' => 'hire_date',
            'tanggal_selesai_kontrak' => 'contract_end_date',
            'tarif_harian' => 'salary',
            'tipe_karyawan' => 'employment_type',
            'aktif' => 'is_active',
        ]);

        $this->renameColumns('kartu_rfid', [
            'karyawan_id' => 'employee_id',
            'label_kartu' => 'card_label',
            'diterbitkan_pada' => 'issued_at',
            'kedaluwarsa_pada' => 'expired_at',
            'catatan' => 'notes',
        ]);

        $this->renameColumns('absensi', [
            'karyawan_id' => 'employee_id',
            'tanggal_absensi' => 'attendance_date',
            'jam_masuk' => 'check_in_time',
            'jam_pulang' => 'check_out_time',
            'menit_telat' => 'late_minutes',
            'jam_lembur' => 'overtime_hours',
            'catatan' => 'notes',
        ]);

        $this->renameColumns('jenis_cuti', [
            'nama' => 'name',
            'kode' => 'code',
            'kuota_per_tahun' => 'quota_per_year',
            'perlu_persetujuan' => 'requires_approval',
            'deskripsi' => 'description',
            'aktif' => 'is_active',
        ]);

        $this->renameColumns('cuti', [
            'karyawan_id' => 'employee_id',
            'jenis_cuti_id' => 'leave_type_id',
            'tanggal_mulai' => 'start_date',
            'tanggal_selesai' => 'end_date',
            'jumlah_hari' => 'number_of_days',
            'alasan' => 'reason',
            'disetujui_oleh' => 'approved_by',
            'disetujui_pada' => 'approved_at',
            'catatan_persetujuan' => 'approval_notes',
        ]);

        $this->renameColumns('hari_libur', [
            'nama' => 'name',
            'tanggal' => 'date',
            'deskripsi' => 'description',
            'tahunan' => 'is_annual',
        ]);

        $this->renameColumns('periode_penggajian', [
            'nama' => 'name',
            'tanggal_mulai' => 'start_date',
            'tanggal_selesai' => 'end_date',
            'tanggal_penggajian' => 'payroll_date',
            'catatan' => 'notes',
        ]);

        $this->renameColumns('slip_gaji', [
            'karyawan_id' => 'employee_id',
            'periode_penggajian_id' => 'payroll_period_id',
            'tanggal_penggajian' => 'payroll_date',
            'hari_kerja' => 'working_days',
            'hari_tidak_hadir' => 'absent_days',
            'jumlah_telat' => 'late_count',
            'potongan_telat' => 'late_deduction',
            'jam_lembur' => 'overtime_hours',
            'upah_lembur' => 'overtime_amount',
            'gaji_pokok' => 'base_salary',
            'total_tunjangan' => 'total_allowance',
            'total_potongan' => 'total_deduction',
            'gaji_bersih' => 'net_salary',
            'dibayar_pada' => 'paid_at',
            'catatan' => 'notes',
        ]);

        $this->renameColumns('gaji', [
            'karyawan_id' => 'employee_id',
            'gaji_pokok' => 'base_salary',
            'total_pendapatan' => 'total_income',
            'total_potongan' => 'total_deduction',
            'gaji_bersih' => 'net_salary',
            'tanggal_berlaku' => 'effective_date',
            'tanggal_selesai' => 'end_date',
            'aktif' => 'is_active',
        ]);

        $this->renameColumns('komponen_gaji', [
            'nama' => 'name',
            'kode' => 'code',
            'tipe' => 'type',
            'tipe_perhitungan' => 'calculation_type',
            'nominal_default' => 'default_amount',
            'urutan_tampil' => 'display_order',
            'aktif' => 'is_active',
            'deskripsi' => 'description',
        ]);

        $this->renameColumns('pengaturan_sistem', [
            'kunci' => 'key',
            'nilai' => 'value',
            'tipe' => 'type',
            'deskripsi' => 'description',
        ]);

        $this->renameColumns('log_absensi', [
            'karyawan_id' => 'employee_id',
            'kartu_rfid_id' => 'rfid_card_id',
            'sumber' => 'source',
            'nama_perangkat' => 'device_name',
            'alamat_ip' => 'ip_address',
            'tipe_scan' => 'scan_type',
            'pesan' => 'message',
            'dipindai_pada' => 'scanned_at',
            'data_payload' => 'payload',
        ]);

        $this->renameColumns('persetujuan_lembur', [
            'karyawan_id' => 'employee_id',
            'tanggal_lembur' => 'overtime_date',
            'jam_mulai' => 'start_time',
            'jam_selesai' => 'end_time',
            'catatan' => 'notes',
            'disetujui_oleh' => 'approved_by',
        ]);
    }

    private function renameTablesToEnglish(): void
    {
        foreach ([
            'persetujuan_lembur' => 'overtime_approvals',
            'log_absensi' => 'attendance_logs',
            'pengaturan_sistem' => 'system_settings',
            'komponen_gaji' => 'salary_components',
            'gaji' => 'salaries',
            'slip_gaji' => 'payslips',
            'periode_penggajian' => 'payroll_periods',
            'hari_libur' => 'holidays',
            'cuti' => 'leaves',
            'jenis_cuti' => 'leave_types',
            'absensi' => 'attendances',
            'kartu_rfid' => 'rfid_cards',
            'karyawan' => 'employees',
            'pengguna' => 'users',
        ] as $old => $new) {
            $this->renameTableIfNeeded($old, $new);
        }
    }

    private function renameTableIfNeeded(string $old, string $new): void
    {
        if (Schema::hasTable($old) && ! Schema::hasTable($new)) {
            Schema::rename($old, $new);
        }
    }

    private function renameColumns(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $old => $new) {
            if (Schema::hasColumn($table, $old) && ! Schema::hasColumn($table, $new)) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($old, $new) {
                    $tableBlueprint->renameColumn($old, $new);
                });
            }
        }
    }

    private function addForeignIfMissing(string $table, string $foreignKey, string $column, string $references, string $onDelete): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasTable($references) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        if ($this->hasForeignKey($table, $foreignKey)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($column, $references, $foreignKey, $onDelete) {
            $tableBlueprint->foreign($column, $foreignKey)
                ->references('id')
                ->on($references)
                ->onDelete($onDelete);
        });
    }

    private function dropForeignIfExists(string $table, string $foreignKey): void
    {
        if (! Schema::hasTable($table) || ! $this->hasForeignKey($table, $foreignKey)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($foreignKey) {
            $tableBlueprint->dropForeign($foreignKey);
        });
    }

    private function hasForeignKey(string $table, string $foreignKey): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return true;
        }

        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', DB::connection()->getDatabaseName())
            ->where('table_name', $table)
            ->where('constraint_name', $foreignKey)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
