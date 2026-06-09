@extends('layouts.app', ['heading' => 'Detail Karyawan'])
@section('content')
@php($activeCard = $employee->getActiveRFIDCard())
<div class="card p-4">
    <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
        <img src="{{ $employee->profile_photo_url }}" alt="{{ $employee->full_name }}" class="rounded-circle" style="width:88px;height:88px;object-fit:cover;">
        <div>
            <h2 class="h4 mb-1">{{ $employee->full_name }}</h2>
            <p class="text-muted mb-2">{{ $employee->karyawan_id }} - {{ \App\Support\DisplayLabel::jobRole($employee->jabatan) }} - {{ \App\Support\DisplayLabel::role($employee->user->role) }}</p>
            @php($status = $employee->current_status)
            <span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4"><div class="border rounded p-3">Email<br><strong>{{ $employee->email }}</strong></div></div>
        <div class="col-md-4"><div class="border rounded p-3">Telepon<br><strong>{{ $employee->telepon ?? '-' }}</strong></div></div>
        <div class="col-md-4"><div class="border rounded p-3">Alamat<br><strong>{{ $employee->alamat ?? '-' }}</strong></div></div>
        <div class="col-md-3"><div class="border rounded p-3">Jam Kerja<br><strong>08:00 - 17:00</strong></div></div>
        <div class="col-md-3"><div class="border rounded p-3">RFID<br><strong>{{ $activeCard?->uid ?? '-' }}</strong></div></div>
        <div class="col-md-3"><div class="border rounded p-3">Gaji<br><strong>Rp {{ number_format($employee->tarif_harian,0,',','.') }}</strong></div></div>
        <div class="col-md-3"><div class="border rounded p-3">Riwayat Lembur<br><strong>{{ $employee->overtimeApprovals->count() }}</strong></div></div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('admin.karyawan.edit', $employee) }}" class="btn btn-outline-secondary">Edit</a>
        <form id="delete-employee-detail-{{ $employee->id }}" method="POST" action="{{ route('admin.karyawan.destroy', $employee) }}">
            @csrf
            @method('DELETE')
            <button
                class="btn btn-outline-danger"
                type="button"
                data-confirm-delete
                data-form-target="#delete-employee-detail-{{ $employee->id }}"
                data-confirm-title="Hapus {{ $employee->full_name }}?"
                data-confirm-message="Data karyawan ini akan dihapus dan akun login terkait akan dinonaktifkan."
            >
                Hapus Karyawan
            </button>
        </form>
    </div>
</div>
@endsection
