@extends('layouts.app', ['heading' => 'Profil Saya'])
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-4 text-center">
            <img src="{{ $employee->profile_photo_url }}" alt="{{ $employee->full_name }}" class="rounded-circle mx-auto mb-3" style="width:140px;height:140px;object-fit:cover;">
            <h2 class="h5 mb-1">{{ $employee->full_name }}</h2>
            <div class="text-muted small mb-3">{{ $employee->employee_id }}</div>
            @php($status = $employee->current_status)
            <span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span>
        </div>
    </div>
    <div class="col-lg-8">
        <form class="card p-4" method="POST" action="{{ route('employee.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Edit Profil</h2>
                <small class="text-muted">Email dikelola admin HR jika ada perubahan.</small>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" value="{{ $employee->email }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Telepon</label>
                    <input class="form-control" type="text" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="08xxxxxxxxxx">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" name="address" rows="4" placeholder="Alamat lengkap">{{ old('address', $employee->address) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Foto Profil</label>
                    <input class="form-control" type="file" name="profile_photo" accept="image/*">
                    <div class="form-text">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
