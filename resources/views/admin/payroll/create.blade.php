@extends('layouts.app', ['heading' => 'Generate Payroll'])
@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <form class="card p-4" method="POST" action="{{ route('admin.payroll.store') }}">
            @csrf
            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h2 class="h5 mb-1">Generate Payroll 2 Mingguan</h2>
                    <div class="text-muted small">Periode maksimal 14 hari.</div>
                </div>
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.payroll.index') }}">Kembali</a>
            </div>

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Nama Periode</label>
                    <input class="form-control" name="name" value="{{ 'Payroll '.now()->format('d M').' - '.now()->copy()->addDays(13)->format('d M Y') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Mulai</label>
                    <input class="form-control" type="date" name="tanggal_mulai" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Selesai</label>
                    <input class="form-control" type="date" name="tanggal_selesai" value="{{ now()->copy()->addDays(13)->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-12">
                    <div class="border rounded-3 p-3">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="include_thr" value="1" id="include_thr">
                            <label class="form-check-label fw-semibold" for="include_thr">Tambahkan THR periode ini</label>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary mt-4">Generate Payroll</button>
        </form>
    </div>

    <div class="col-xl-4">
        <div class="card p-4 h-100">
            <h2 class="h5 mb-3">Rumus Aktif</h2>
            <div class="d-flex justify-content-between border-bottom py-2">
                <span class="text-muted">Staff</span>
                <strong>Rp 110.000/hari</strong>
            </div>
            <div class="d-flex justify-content-between border-bottom py-2">
                <span class="text-muted">Mandor</span>
                <strong>Rp 250.000/hari</strong>
            </div>
            <div class="d-flex justify-content-between border-bottom py-2">
                <span class="text-muted">Potongan telat</span>
                <strong>Per menit</strong>
            </div>
            <div class="d-flex justify-content-between border-bottom py-2">
                <span class="text-muted">Lembur</span>
                <strong>1.5x per jam</strong>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">THR</span>
                <strong>26 hari kerja</strong>
            </div>
        </div>
    </div>
</div>
@endsection
