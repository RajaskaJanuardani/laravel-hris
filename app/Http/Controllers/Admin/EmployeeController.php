<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\RFIDCard;
use App\Models\ShiftTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with(['user', 'rfidCards', 'currentLeave'])->latest()->paginate(10);

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employees.create', $this->formData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $user = User::create([
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? 'password'),
            'role' => $data['role'],
        ]);

        $employee = Employee::create($data + ['user_id' => $user->id] + $this->legacyDefaults());

        if ($request->filled('rfid_uid')) {
            RFIDCard::create([
                'employee_id' => $employee->id,
                'uid' => strtoupper($request->rfid_uid),
                'card_label' => $request->card_label,
            ]);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['user', 'rfidCards', 'attendances', 'currentLeave', 'overtimeApprovals'])->findOrFail($id);

        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('admin.employees.edit', $this->formData() + ['employee' => Employee::with('rfidCards')->findOrFail($id)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);
        $data = $this->validated($request, $employee->id, $employee->user_id);
        $employee->update($data + $this->legacyDefaults());
        $employee->user->update([
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        if ($request->filled('rfid_uid')) {
            RFIDCard::updateOrCreate(
                ['employee_id' => $employee->id, 'uid' => strtoupper($request->rfid_uid)],
                ['card_label' => $request->card_label, 'status' => 'active']
            );
        }

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::with('user')->findOrFail($id);

        $employee->user?->update(['is_active' => false]);
        $employee->delete();

        return back()->with('success', 'Karyawan berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'jobRoles' => ['staff' => 'Staff', 'mandor' => 'Mandor'],
        ];
    }

    private function legacyDefaults(): array
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

        return [
            'department_id' => $department->id,
            'position_id' => $position->id,
            'shift_time_id' => $shift->id,
        ];
    }

    private function validated(Request $request, ?int $employeeId = null, ?int $userId = null): array
    {
        return $request->validate([
            'employee_id' => ['required', 'string', 'max:50', 'unique:employees,employee_id,'.$employeeId],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:employees,email,'.$employeeId, 'unique:users,email,'.$userId],
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'address' => ['nullable', 'string'],
            'job_role' => ['required', 'in:staff,mandor'],
            'hire_date' => ['required', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:hire_date'],
            'salary' => ['required', 'numeric', 'min:0'],
            'employment_type' => ['required', 'in:permanent,contract,internship'],
            'is_active' => ['sometimes', 'boolean'],
            'role' => ['required', 'in:admin,employee'],
            'password' => [$employeeId ? 'nullable' : 'required', 'string', 'min:8'],
            'rfid_uid' => ['nullable', 'string', 'max:100'],
            'card_label' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
