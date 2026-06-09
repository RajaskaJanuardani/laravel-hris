@extends('layouts.app', ['heading' => 'Cuti & Izin'])
@section('content')
<div class="card overflow-hidden">
    <div class="p-4 border-bottom"><h2 class="h5 mb-0">Daftar Pengajuan</h2></div>
    <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Karyawan</th><th>Tipe</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>@forelse($leaves as $leave)@php($status = \App\Support\DisplayLabel::status($leave->status))<tr><td>@include('shared._employee_table_cell', ['employee' => $leave->employee])</td><td>{{ $leave->leaveType->name }}</td><td><span class="ta-code-chip">{{ $leave->tanggal_mulai->format('d M') }} - {{ $leave->tanggal_selesai->format('d M Y') }}</span></td><td><span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td><td>@if($leave->status==='pending')<div class="ta-action-group"><form class="d-inline" method="POST" action="{{ route('admin.leaves.approve',$leave) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-success">Setujui</button></form><form class="d-inline" method="POST" action="{{ route('admin.leaves.reject',$leave) }}">@csrf @method('PATCH')<input type="hidden" name="catatan_persetujuan" value="Ditolak oleh admin"><button class="btn btn-sm btn-danger">Tolak</button></form></div>@endif</td></tr>@empty<tr><td colspan="5" class="ta-table-empty">Belum ada pengajuan.</td></tr>@endforelse</tbody></table>
    </div>
    @include('shared._pagination', ['paginator' => $leaves, 'label' => 'pengajuan'])
</div>
@endsection
