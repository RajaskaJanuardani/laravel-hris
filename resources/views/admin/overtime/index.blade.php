@extends('layouts.app', ['heading' => 'Approval Lembur'])
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <form class="card p-4" method="POST" action="{{ route('admin.overtime.store') }}">
            @csrf
            <h2 class="h5 mb-3">Setujui Lembur</h2>
            <label class="form-label">Karyawan</label>
            <select class="form-select mb-3" name="employee_id" required>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->full_name }} - {{ ucfirst($employee->job_role) }}</option>
                @endforeach
            </select>
            <label class="form-label">Tanggal</label>
            <input class="form-control mb-3" type="date" name="overtime_date" value="{{ now()->format('Y-m-d') }}" required>
            <label class="form-label">Maksimal Sampai</label>
            <input class="form-control mb-3" type="time" name="end_time" value="22:00" max="22:00" required>
            <label class="form-label">Catatan</label>
            <textarea class="form-control mb-3" name="notes" rows="3" placeholder="Contoh: produksi tambahan"></textarea>
            <button class="btn btn-primary w-100">Simpan Approval</button>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="card p-4">
            <h2 class="h5 mb-3">Daftar Approval</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Tanggal</th><th>Karyawan</th><th>Jam</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($approvals as $approval)
                            <tr>
                                <td>{{ $approval->overtime_date->format('d M Y') }}</td>
                                <td>{{ $approval->employee->full_name }}</td>
                                <td>17:00 - {{ $approval->end_time->format('H:i') }}</td>
                                <td><span class="badge {{ $approval->status === 'approved' ? 'text-bg-success' : 'text-bg-danger' }}">{{ $approval->status }}</span></td>
                                <td class="text-end">
                                    @if($approval->status === 'approved')
                                        <form method="POST" action="{{ route('admin.overtime.destroy', $approval) }}">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-secondary">Batalkan</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">Belum ada approval lembur.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $approvals->links() }}
        </div>
    </div>
</div>
@endsection
