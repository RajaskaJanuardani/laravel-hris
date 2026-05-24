@extends('layouts.app', ['heading' => 'Cuti Saya'])
@section('content')
<div class="row g-4">
    <div class="col-lg-4"><form class="card p-4" method="POST" action="{{ route('employee.leaves.store') }}">@csrf<h2 class="h5">Ajukan Cuti/Izin</h2><select class="form-select mb-3" name="leave_type_id">@foreach($leaveTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select><input class="form-control mb-3" type="date" name="start_date" required><input class="form-control mb-3" type="date" name="end_date" required><textarea class="form-control mb-3" name="reason" placeholder="Alasan"></textarea><button class="btn btn-primary w-100">Kirim</button></form></div>
    <div class="col-lg-8"><div class="card p-4"><h2 class="h5">Riwayat Pengajuan</h2><table class="table"><thead><tr><th>Tipe</th><th>Tanggal</th><th>Status</th></tr></thead><tbody>@forelse($leaves as $leave)<tr><td>{{ $leave->leaveType->name }}</td><td>{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M Y') }}</td><td><span class="badge text-bg-primary">{{ $leave->status }}</span></td></tr>@empty<tr><td colspan="3" class="text-muted">Belum ada data.</td></tr>@endforelse</tbody></table>{{ method_exists($leaves,'links') ? $leaves->links() : '' }}</div></div>
</div>
@endsection
