@extends('layouts.app', ['heading' => 'Tipe Cuti'])
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <form class="card p-4" method="POST" action="{{ route('admin.settings.leave-types.store') }}">
            @csrf
            <h2 class="h5">Tambah Tipe</h2>
            <input class="form-control mb-2" name="name" placeholder="Nama" required>
            <input class="form-control mb-2" name="code" placeholder="Kode" required>
            <input class="form-control mb-2" type="number" name="kuota_per_tahun" value="12">
            <textarea class="form-control mb-2" name="description" placeholder="Deskripsi"></textarea>
            <button class="btn btn-primary w-100">Simpan</button>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="card overflow-hidden">
            <div class="p-4 border-bottom">
                <h2 class="h5 mb-1">Daftar Tipe Cuti</h2>
                <div class="text-muted small">Kode dan kuota tahunan yang dipakai saat pengajuan cuti.</div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Kuota</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($leaveTypes as $type)
                        <tr>
                            <td><div class="fw-semibold">{{ $type->name }}</div></td>
                            <td><span class="ta-code-chip">{{ $type->code }}</span></td>
                            <td>{{ $type->kuota_per_tahun }} hari</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="ta-table-empty">Belum ada tipe cuti.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
