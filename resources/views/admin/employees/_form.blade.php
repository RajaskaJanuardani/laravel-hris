<form class="card p-4" method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="row g-3">
        <div class="col-md-3"><label class="form-label">NIK</label><input class="form-control" name="karyawan_id" value="{{ old('karyawan_id',$employee->karyawan_id ?? 'EMP'.random_int(1000,9999)) }}" required></div>
        <div class="col-md-3"><label class="form-label">Nama Depan</label><input class="form-control" name="nama_depan" value="{{ old('nama_depan',$employee->nama_depan ?? '') }}" required></div>
        <div class="col-md-3"><label class="form-label">Nama Belakang</label><input class="form-control" name="nama_belakang" value="{{ old('nama_belakang',$employee->nama_belakang ?? '') }}" required></div>
        <div class="col-md-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email',$employee->email ?? '') }}" required></div>
        <div class="col-md-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" placeholder="{{ $employee ? 'Kosongkan' : 'Minimal 8 karakter' }}"></div>
        <div class="col-md-3"><label class="form-label">Role Login</label><select class="form-select" name="role">@foreach(['employee' => 'Karyawan', 'admin' => 'Admin'] as $role => $label)<option value="{{ $role }}" @selected(old('role',$employee->user->role ?? 'employee')===$role)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">Jabatan</label><select class="form-select" name="jabatan">@foreach($jobRoles as $value => $label)<option value="{{ $value }}" @selected(old('jabatan',$employee->jabatan ?? 'staff')===$value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">HP</label><input class="form-control" name="telepon" value="{{ old('telepon',$employee->telepon ?? '') }}"></div>
        <div class="col-md-3"><label class="form-label">Gender</label><select class="form-select" name="jenis_kelamin"><option value="">-</option><option value="male" @selected(old('jenis_kelamin',$employee->jenis_kelamin ?? '')==='male')>Laki-laki</option><option value="female" @selected(old('jenis_kelamin',$employee->jenis_kelamin ?? '')==='female')>Perempuan</option></select></div>
        <div class="col-md-3"><label class="form-label">Tanggal Masuk</label><input class="form-control" type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk',isset($employee)?$employee->tanggal_masuk->format('Y-m-d'):now()->format('Y-m-d')) }}" required></div>
        <div class="col-md-3"><label class="form-label">Tarif Harian</label><input class="form-control" type="number" name="tarif_harian" value="{{ old('tarif_harian',$employee->tarif_harian ?? 110000) }}" readonly></div>
        <div class="col-md-3"><label class="form-label">Tipe</label><select class="form-select" name="tipe_karyawan"><option value="permanent" @selected(old('tipe_karyawan',$employee->tipe_karyawan ?? 'permanent')==='permanent')>Tetap</option><option value="contract" @selected(old('tipe_karyawan',$employee->tipe_karyawan ?? '')==='contract')>Kontrak</option><option value="internship" @selected(old('tipe_karyawan',$employee->tipe_karyawan ?? '')==='internship')>Magang</option></select></div>
        <div class="col-md-3"><label class="form-label">UID RFID</label><input class="form-control" name="rfid_uid" value="{{ old('rfid_uid',$employee?->getActiveRFIDCard()?->uid ?? $employee?->rfidCards->first()?->uid ?? '') }}" placeholder="A1B2C3D4"></div>
        <div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="alamat">{{ old('alamat',$employee->alamat ?? '') }}</textarea></div>
    </div>
    <div class="mt-4"><button class="btn btn-primary">Simpan</button><a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline-secondary">Batal</a></div>
</form>
