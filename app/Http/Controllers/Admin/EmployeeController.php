<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\RFIDCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));

        $karyawan = Employee::with(['user', 'rfidCards', 'currentLeave'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nomor_karyawan', 'like', "%{$search}%")
                        ->orWhere('nama_depan', 'like', "%{$search}%")
                        ->orWhere('nama_belakang', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('jabatan', 'like', "%{$search}%")
                        ->orWhereHas('rfidCards', fn ($rfidQuery) => $rfidQuery->where('uid', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.employees.index', compact('karyawan', 'search'));
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
        $data['tarif_harian'] = Employee::dailyRateFor($data['jabatan']);

        $user = User::create([
            'name' => $data['nama_depan'].' '.$data['nama_belakang'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? 'password'),
            'role' => $data['role'],
        ]);

        $employee = Employee::create($data + ['pengguna_id' => $user->id]);

        if ($request->filled('rfid_uid')) {
            RFIDCard::create([
                'karyawan_id' => $employee->id,
                'uid' => strtoupper($request->rfid_uid),
                'label_kartu' => $request->label_kartu,
            ]);
        }

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['user', 'rfidCards', 'absensi', 'currentLeave', 'overtimeApprovals'])->findOrFail($id);

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
        $currentCard = $employee->getActiveRFIDCard() ?? $employee->rfidCards()->latest('id')->first();
        $data = $this->validated($request, $employee->id, $employee->pengguna_id, $currentCard?->id);
        $data['tarif_harian'] = Employee::dailyRateFor($data['jabatan']);

        $employee->update($data);
        $employee->user->update([
            'name' => $data['nama_depan'].' '.$data['nama_belakang'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        $normalizedUid = strtoupper(trim((string) $request->input('rfid_uid')));
        if ($normalizedUid !== '') {
            $card = $currentCard;

            if ($card) {
                $card->update([
                    'uid' => $normalizedUid,
                    'label_kartu' => $request->label_kartu ?: $card->label_kartu,
                    'status' => 'active',
                ]);
            } else {
                $card = RFIDCard::create([
                    'karyawan_id' => $employee->id,
                    'uid' => $normalizedUid,
                    'label_kartu' => $request->label_kartu,
                    'status' => 'active',
                ]);
            }

            RFIDCard::where('karyawan_id', $employee->id)
                ->where('id', '!=', $card->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::with('user')->findOrFail($id);

        $employee->user?->update(['aktif' => false]);
        $employee->delete();

        return back()->with('success', 'Karyawan berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'jobRoles' => ['staff' => 'Staff', 'mandor' => 'Mandor'],
        ];
    }

    private function validated(Request $request, ?int $employeeId = null, ?int $userId = null, ?int $rfidCardId = null): array
    {
        return $request->validate([
            'karyawan_id' => ['required', 'string', 'max:50', 'unique:karyawan,nomor_karyawan,'.$employeeId],
            'nama_depan' => ['required', 'string', 'max:100'],
            'nama_belakang' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:karyawan,email,'.$employeeId, 'unique:pengguna,email,'.$userId],
            'telepon' => ['nullable', 'string', 'max:30'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:male,female'],
            'alamat' => ['nullable', 'string'],
            'jabatan' => ['required', 'in:staff,mandor'],
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_selesai_kontrak' => ['nullable', 'date', 'after_or_equal:tanggal_masuk'],
            'tarif_harian' => ['nullable', 'numeric', 'min:0'],
            'tipe_karyawan' => ['required', 'in:permanent,contract,internship'],
            'aktif' => ['sometimes', 'boolean'],
            'role' => ['required', 'in:admin,employee'],
            'password' => [$employeeId ? 'nullable' : 'required', 'string', 'min:8'],
            'rfid_uid' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('kartu_rfid', 'uid')
                    ->ignore($rfidCardId)
                    ->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'label_kartu' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
