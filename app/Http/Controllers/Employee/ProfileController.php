<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateEmployeeProfileRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $employee = auth()->user()->employee?->load(['user', 'currentLeave']);

        abort_unless($employee, 404, 'Profil karyawan belum tersedia.');

        return view('employee.profile.edit', compact('employee'));
    }

    public function update(UpdateEmployeeProfileRequest $request)
    {
        $employee = $request->user()->employee;

        abort_unless($employee, 404, 'Profil karyawan belum tersedia.');

        $data = $request->validated();
        $photoPath = $employee->profile_photo_path;

        if ($request->hasFile('profile_photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $employee->update([
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'profile_photo_path' => $photoPath,
        ]);

        return redirect()
            ->route('employee.profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
