<form class="card p-4" method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="row g-3">
        <div class="col-md-3"><label class="form-label">NIK</label><input class="form-control" name="employee_id" value="{{ old('employee_id',$employee->employee_id ?? 'EMP'.random_int(1000,9999)) }}" required></div>
        <div class="col-md-3"><label class="form-label">Nama Depan</label><input class="form-control" name="first_name" value="{{ old('first_name',$employee->first_name ?? '') }}" required></div>
        <div class="col-md-3"><label class="form-label">Nama Belakang</label><input class="form-control" name="last_name" value="{{ old('last_name',$employee->last_name ?? '') }}" required></div>
        <div class="col-md-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email',$employee->email ?? '') }}" required></div>
        <div class="col-md-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" placeholder="{{ $employee ? 'Kosongkan' : 'Minimal 8 karakter' }}"></div>
        <div class="col-md-3"><label class="form-label">Role Login</label><select class="form-select" name="role">@foreach(['employee' => 'Karyawan', 'admin' => 'Admin'] as $role => $label)<option value="{{ $role }}" @selected(old('role',$employee->user->role ?? 'employee')===$role)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">Jabatan</label><select class="form-select" name="job_role">@foreach($jobRoles as $value => $label)<option value="{{ $value }}" @selected(old('job_role',$employee->job_role ?? 'staff')===$value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">HP</label><input class="form-control" name="phone" value="{{ old('phone',$employee->phone ?? '') }}"></div>
        <div class="col-md-3"><label class="form-label">Gender</label><select class="form-select" name="gender"><option value="">-</option><option value="male" @selected(old('gender',$employee->gender ?? '')==='male')>Laki-laki</option><option value="female" @selected(old('gender',$employee->gender ?? '')==='female')>Perempuan</option></select></div>
        <div class="col-md-3"><label class="form-label">Tanggal Masuk</label><input class="form-control" type="date" name="hire_date" value="{{ old('hire_date',isset($employee)?$employee->hire_date->format('Y-m-d'):now()->format('Y-m-d')) }}" required></div>
        <div class="col-md-3"><label class="form-label">Gaji</label><input class="form-control" type="number" name="salary" value="{{ old('salary',$employee->salary ?? 5000000) }}" required></div>
        <div class="col-md-3"><label class="form-label">Tipe</label><select class="form-select" name="employment_type"><option value="permanent" @selected(old('employment_type',$employee->employment_type ?? 'permanent')==='permanent')>Permanent</option><option value="contract" @selected(old('employment_type',$employee->employment_type ?? '')==='contract')>Contract</option><option value="internship" @selected(old('employment_type',$employee->employment_type ?? '')==='internship')>Internship</option></select></div>
        <div class="col-md-3"><label class="form-label">UID RFID</label><input class="form-control" name="rfid_uid" value="{{ old('rfid_uid',$employee?->rfidCards->first()->uid ?? '') }}" placeholder="A1B2C3D4"></div>
        <div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="address">{{ old('address',$employee->address ?? '') }}</textarea></div>
    </div>
    <div class="mt-4"><button class="btn btn-primary">Simpan</button><a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">Batal</a></div>
</form>
