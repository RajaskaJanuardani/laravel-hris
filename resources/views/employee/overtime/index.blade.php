@extends('layouts.app', ['heading' => 'Lembur Saya'])
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat p-3">
            <div class="text-muted small">Lembur Disetujui Bulan Ini</div>
            <div class="h4 mb-0">{{ $approvedOvertimeThisMonth }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat p-3">
            <div class="text-muted small">Lembur Berikutnya</div>
            <div class="h4 mb-0">{{ $nextOvertime?->overtime_date?->format('d M') ?? '-' }}</div>
            <div class="small text-muted">17:00 - {{ $nextOvertime?->end_time?->format('H:i') ?? '-' }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat p-3">
            <div class="text-muted small">Status Terakhir</div>
            <div class="h4 mb-0">{{ $nextOvertime?->status ?? 'Belum ada' }}</div>
        </div>
    </div>
</div>
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Riwayat Approval Lembur</h2>
        <span class="text-muted small">Lembur yang disetujui admin akan muncul di sini.</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th>Disetujui Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($overtimeApprovals as $approval)
                    <tr>
                        <td>{{ $approval->overtime_date->format('d M Y') }}</td>
                        <td>17:00 - {{ $approval->end_time->format('H:i') }}</td>
                        <td><span class="badge text-bg-{{ $approval->status === 'approved' ? 'success' : ($approval->status === 'cancelled' ? 'secondary' : 'warning') }}">{{ $approval->status }}</span></td>
                        <td>{{ $approval->notes ?? '-' }}</td>
                        <td>{{ $approval->approvedBy?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Belum ada approval lembur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $overtimeApprovals->links() }}
</div>
@endsection
