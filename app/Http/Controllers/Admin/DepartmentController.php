<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use App\Models\ShiftTime;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.settings.department', [
            'departments' => Department::latest()->get(),
            'positions' => Position::latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Department::create($request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:departments,name'],
            'code' => ['nullable', 'string', 'max:20', 'unique:departments,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]));

        return back()->with('success', 'Divisi berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);
        $department->update($request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:departments,name,'.$department->id],
            'code' => ['nullable', 'string', 'max:20', 'unique:departments,code,'.$department->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]));

        return back()->with('success', 'Divisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Department::findOrFail($id)->delete();

        return back()->with('success', 'Divisi berhasil dihapus.');
    }

    public function positions()
    {
        return view('admin.settings.department', [
            'departments' => Department::latest()->get(),
            'positions' => Position::latest()->get(),
        ]);
    }

    public function storePosition(Request $request)
    {
        Position::create($request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:positions,name'],
            'code' => ['nullable', 'string', 'max:20', 'unique:positions,code'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]));

        return back()->with('success', 'Jabatan berhasil disimpan.');
    }

    public function shift()
    {
        return view('admin.settings.shift', ['shiftTimes' => ShiftTime::latest()->get()]);
    }

    public function storeShift(Request $request)
    {
        ShiftTime::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'working_hours' => ['required', 'integer', 'min:1'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]));

        return back()->with('success', 'Shift berhasil disimpan.');
    }

    public function leaveTypes()
    {
        return view('admin.settings.leave-types', ['leaveTypes' => LeaveType::latest()->get()]);
    }

    public function storeLeaveType(Request $request)
    {
        LeaveType::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:leave_types,code'],
            'quota_per_year' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]));

        return back()->with('success', 'Tipe cuti berhasil disimpan.');
    }
}
