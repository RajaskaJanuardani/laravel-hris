<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\RFIDCard;
use App\Models\ShiftTime;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::firstOrCreate(
            ['code' => 'GENERAL'],
            ['name' => 'Umum', 'description' => 'Default internal', 'is_active' => true]
        );

        $position = Position::firstOrCreate(
            ['code' => 'EMP'],
            ['name' => 'Karyawan', 'description' => 'Default internal', 'is_active' => true]
        );

        $shift = ShiftTime::firstOrCreate(
            ['name' => 'Reguler'],
            ['start_time' => '08:00:00', 'end_time' => '17:00:00', 'working_hours' => 8, 'late_tolerance_minutes' => 10]
        );

        foreach ([
            ['name' => 'Cuti Tahunan', 'code' => 'CT', 'quota_per_year' => 12],
            ['name' => 'Izin', 'code' => 'IZN', 'quota_per_year' => 6],
            ['name' => 'Sakit', 'code' => 'SKT', 'quota_per_year' => 12],
        ] as $type) {
            LeaveType::firstOrCreate(['code' => $type['code']], $type);
        }

        $accounts = [
            ['name' => 'Admin Absensi', 'email' => 'admin@hris.test', 'role' => 'admin', 'job_role' => 'mandor', 'uid' => 'A1B2C3D4', 'salary' => 8000000],
            ['name' => 'Rajasaka Januardani', 'email' => 'employee@hris.test', 'role' => 'employee', 'job_role' => 'staff', 'uid' => 'D1E2F3A4', 'salary' => 5000000],
            ['name' => 'Fisabilillah 11', 'email' => 'fisabilillah@hris.test', 'role' => 'employee', 'job_role' => 'staff', 'uid' => 'B1C2D3E4', 'salary' => 4800000],
            ['name' => 'Mandor Demo', 'email' => 'mandor@hris.test', 'role' => 'employee', 'job_role' => 'mandor', 'uid' => 'C1D2E3F4', 'salary' => 6000000],
        ];

        foreach ($accounts as $index => $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('password'),
                    'role' => $account['role'],
                    'is_active' => true,
                ]
            );

            [$firstName, $lastName] = array_pad(explode(' ', $account['name'], 2), 2, '');

            $existingEmployee = Employee::where('user_id', $user->id)
                ->orWhere('email', $account['email'])
                ->first();

            $employee = Employee::updateOrCreate(
                ['id' => $existingEmployee?->id],
                [
                    'user_id' => $user->id,
                    'employee_id' => $existingEmployee?->employee_id ?? $this->nextEmployeeId(),
                    'first_name' => $firstName,
                    'last_name' => $lastName ?: 'Demo',
                    'email' => $account['email'],
                    'phone' => '08'.random_int(1111111111, 9999999999),
                    'gender' => $index % 2 ? 'female' : 'male',
                    'address' => 'Jakarta',
                    'department_id' => $department->id,
                    'position_id' => $position->id,
                    'shift_time_id' => $shift->id,
                    'job_role' => $account['job_role'],
                    'hire_date' => now()->subMonths(8),
                    'salary' => $account['salary'],
                    'employment_type' => 'permanent',
                    'is_active' => true,
                ]
            );

            RFIDCard::updateOrCreate(
                ['uid' => $account['uid']],
                ['employee_id' => $employee->id, 'card_label' => $account['name'], 'status' => 'active']
            );
        }
    }

    private function nextEmployeeId(): string
    {
        $next = 1;

        do {
            $employeeId = 'EMP'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (Employee::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }
}
