@extends('layouts.app', ['heading' => 'Laporan Absensi'])
@section('content')
<div class="card p-4">
    <form class="row g-2 mb-3">
        <div class="col-md-3"><input class="form-control" type="date" name="from" value="{{ request('from') }}"></div>
        <div class="col-md-3"><input class="form-control" type="date" name="to" value="{{ request('to') }}"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
    </form>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th>Telat</th>
                    <th>Lembur</th>
                </tr>
            </thead>
            <tbody>
            @forelse($absensi as $attendance)
                @php($status = \App\Support\DisplayLabel::status($attendance->status))
                <tr>
                    <td><span class="ta-code-chip">{{ $attendance->tanggal_absensi->format('d M Y') }}</span></td>
                    <td>@include('shared._employee_table_cell', ['employee' => $attendance->employee])</td>
                    <td>{{ \App\Support\DisplayLabel::jobRole($attendance->employee->jabatan) }}</td>
                    <td><span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td>
                    <td>{{ $attendance->menit_telat }} menit</td>
                    <td>{{ number_format($attendance->jam_lembur, 2) }} jam</td>
                </tr>
            @empty
                <tr><td colspan="6" class="ta-table-empty">Belum ada absensi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
