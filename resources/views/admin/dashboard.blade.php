@extends('layouts.app', ['heading' => 'Dashboard Admin'])
@section('content')
<div class="row g-3 mb-4">
    @foreach([
        ['Total Karyawan',$totalEmployees],
        ['Hadir Hari Ini',$presentToday],
        ['Terlambat',$lateToday],
        ['Cuti Aktif',$leaveToday],
        ['Cuti Pending',$pendingLeaves],
        ['Lembur Hari Ini',$overtimeToday],
    ] as $stat)
        <div class="col-6 col-lg">
            <div class="card stat p-3"><div class="text-muted small">{{ $stat[0] }}</div><div class="h3 mb-0">{{ $stat[1] }}</div></div>
        </div>
    @endforeach
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <h2 class="h5">Grafik Absensi Bulan Ini</h2>
            <canvas id="attendanceChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h2 class="h5">Scan RFID Terbaru</h2>
            <div class="ta-activity-list">
                @forelse($recentLogs as $log)
                    <div class="ta-activity-item">
                        <div class="ta-activity-copy">
                            <div class="ta-activity-title">{{ $log->employee->full_name ?? $log->uid }}</div>
                            <div class="ta-activity-message">{{ $log->message }}</div>
                        </div>
                        <span class="ta-status-pill {{ $log->status === 'success' ? 'is-success' : 'is-danger' }}">{{ $log->scan_type }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada scan.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card p-4">
            <h2 class="h5">Komposisi Jabatan</h2>
            <div class="row g-3">
                @forelse($jobRoleStats as $role => $total)
                    <div class="col-md-3"><div class="border rounded p-3 bg-light"><div class="fw-semibold">{{ ucfirst($role) }}</div><div class="text-muted small">{{ $total }} karyawan</div></div></div>
                @empty
                    <div class="col-12 text-muted">Belum ada data jabatan.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
new Chart(document.getElementById('attendanceChart'), {
    type: 'line',
    data: {
        labels: @json($monthlyAttendance->keys()),
        datasets: [{label:'Absensi', data:@json($monthlyAttendance->values()), borderColor:'#1f5eff', backgroundColor:'rgba(31,94,255,.12)', tension:.35, fill:true}]
    }
});
</script>
@endpush
@endsection
