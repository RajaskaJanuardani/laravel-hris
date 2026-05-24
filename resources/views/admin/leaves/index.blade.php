@extends('layouts.app', ['heading' => 'Cuti & Izin'])
@section('content')
<div class="card p-4">
    <table class="table align-middle"><thead><tr><th>Karyawan</th><th>Tipe</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>@forelse($leaves as $leave)<tr><td>{{ $leave->employee->full_name }}</td><td>{{ $leave->leaveType->name }}</td><td>{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M Y') }}</td><td><span class="badge text-bg-warning">{{ $leave->status }}</span></td><td>@if($leave->status==='pending')<form class="d-inline" method="POST" action="{{ route('admin.leaves.approve',$leave) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-success">Approve</button></form><form class="d-inline" method="POST" action="{{ route('admin.leaves.reject',$leave) }}">@csrf @method('PATCH')<input type="hidden" name="approval_notes" value="Ditolak oleh admin"><button class="btn btn-sm btn-danger">Reject</button></form>@endif</td></tr>@empty<tr><td colspan="5" class="text-muted">Belum ada pengajuan.</td></tr>@endforelse</tbody></table>
    {{ $leaves->links() }}
</div>
@endsection
