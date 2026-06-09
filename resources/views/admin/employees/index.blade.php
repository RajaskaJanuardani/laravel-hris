@extends('layouts.app', ['heading' => 'Manajemen Karyawan'])
@section('content')
<div class="card overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-4 border-bottom">
        <div>
            <h2 class="h5 mb-1">Data Karyawan</h2>
            <div class="text-muted small">Cari berdasarkan nama, NIK, email, jabatan, atau UID RFID.</div>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.karyawan.create') }}">Tambah Karyawan</a>
    </div>
    <div class="p-4 border-bottom">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('admin.karyawan.index') }}">
            <div class="col-12 col-lg-6 col-xl-5">
                <label class="form-label">Cari Karyawan</label>
                <input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Contoh: Zidane, EMP0057, mandor, D1728D29">
            </div>
            <div class="col-12 col-sm-auto d-flex gap-2">
                <button class="btn btn-primary px-4" type="submit">Cari</button>
                @if($search !== '')
                    <a class="btn btn-outline-secondary" href="{{ route('admin.karyawan.index') }}">Reset</a>
                @endif
            </div>
        </form>
    </div>
    <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>NIK</th><th>Nama</th><th>Peran</th><th>Jabatan</th><th>RFID</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($karyawan as $employee)
            @php
                $activeCard = $employee->getActiveRFIDCard();
                $initials = collect(explode(' ', $employee->full_name))
                    ->filter()
                    ->take(2)
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->implode('');
            @endphp
            <tr>
                <td><span class="ta-code-chip">{{ $employee->karyawan_id }}</span></td>
                <td>
                    <div class="ta-row-person">
                        <span class="ta-row-avatar">{{ $initials }}</span>
                        <div>
                            <div class="fw-semibold">{{ $employee->full_name }}</div>
                            <div class="small text-muted">{{ $employee->email }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ \App\Support\DisplayLabel::role($employee->user->role) }}</td>
                <td>{{ \App\Support\DisplayLabel::jobRole($employee->jabatan) }}</td>
                <td><span class="ta-code-chip">{{ $activeCard?->uid ?? '-' }}</span></td>
                <td>
                    @php($status = $employee->current_status)
                    <span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span>
                </td>
                <td class="text-end">
                    <div class="ta-action-group">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.karyawan.show',$employee) }}">Detail</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.karyawan.edit',$employee) }}">Edit</a>
                        <form class="d-inline" id="delete-employee-{{ $employee->id }}" method="POST" action="{{ route('admin.karyawan.destroy', $employee) }}">
                            @csrf
                            @method('DELETE')
                            <button
                                class="btn btn-sm btn-outline-danger"
                                type="button"
                                data-confirm-delete
                                data-form-target="#delete-employee-{{ $employee->id }}"
                                data-confirm-title="Hapus {{ $employee->full_name }}?"
                                data-confirm-message="Data karyawan ini akan dihapus dan akun login terkait akan dinonaktifkan."
                            >
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="ta-table-empty">Tidak ada karyawan yang cocok.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @include('shared._pagination', ['paginator' => $karyawan, 'label' => 'karyawan'])
</div>
@endsection
