@extends('layouts.app', ['heading' => 'Penugasan Lembur'])
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <form class="card p-4" method="POST" action="{{ route('admin.overtime.store') }}">
            @csrf
            <h2 class="h5 mb-1">Tetapkan Lembur</h2>
            <p class="text-muted small mb-3">Admin menentukan jadwal lembur. Karyawan hanya menerima informasi jadwal di akun masing-masing.</p>
            <label class="form-label">Karyawan</label>
            <select class="form-select mb-3" name="karyawan_id" required>
                @foreach($karyawan as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->full_name }} - {{ \App\Support\DisplayLabel::jobRole($employee->jabatan) }}</option>
                @endforeach
            </select>
            <label class="form-label">Tanggal</label>
            <input class="form-control mb-3" type="date" name="tanggal_lembur" value="{{ now()->format('Y-m-d') }}" required>
            <label class="form-label">Jam Selesai</label>
            <input class="form-control mb-3" type="time" name="jam_selesai" value="22:00" max="22:00" required>
            <label class="form-label">Catatan</label>
            <textarea class="form-control mb-3" name="catatan" rows="3" placeholder="Contoh: produksi tambahan"></textarea>
            <button class="btn btn-primary w-100">Simpan Lembur</button>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="card overflow-hidden">
            <div class="p-4 border-bottom">
                <h2 class="h5 mb-0">Daftar Lembur</h2>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Tanggal</th><th>Karyawan</th><th>Jam</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($approvals as $approval)
                            <tr>
                                <td><span class="ta-code-chip">{{ $approval->tanggal_lembur->format('d M Y') }}</span></td>
                                <td>@include('shared._employee_table_cell', ['employee' => $approval->employee])</td>
                                <td><span class="ta-time-pill">17:00 - {{ $approval->jam_selesai->format('H:i') }}</span></td>
                                @php($status = \App\Support\DisplayLabel::overtimeStatus($approval->status))
                                <td><span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td>
                                <td class="text-end">
                                    @if($approval->status === 'approved')
                                        <form method="POST" action="{{ route('admin.overtime.destroy', $approval) }}">
                                            @csrf @method('DELETE')
                                            <div class="ta-action-group justify-content-end">
                                                <button class="btn btn-sm btn-outline-secondary">Batalkan</button>
                                            </div>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="ta-table-empty">Belum ada data lembur.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('shared._pagination', ['paginator' => $approvals, 'label' => 'lembur'])
        </div>
    </div>
</div>
@endsection
