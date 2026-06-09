@extends('layouts.app', ['heading' => 'Absensi RFID'])
@section('content')
<div class="card overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-4 border-bottom">
        <div>
            <h2 class="h5 mb-1">Data Absensi</h2>
            <div class="text-muted small">Filter absensi berdasarkan tanggal, status, dan data karyawan.</div>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.attendance.monitoring') }}">Monitoring RFID</a>
    </div>
    <div class="p-4 border-bottom">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('admin.attendance.index') }}">
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label">Tanggal</label>
                <input class="form-control" type="date" name="date" value="{{ $selectedDate }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="hadir" @selected($selectedStatus === 'hadir' || $selectedStatus === 'present')>Hadir</option>
                    <option value="telat" @selected($selectedStatus === 'telat' || $selectedStatus === 'late')>Telat</option>
                    <option value="tidak_hadir" @selected($selectedStatus === 'tidak_hadir' || $selectedStatus === 'absent')>Tidak Hadir</option>
                </select>
            </div>
            <div class="col-12 col-lg-4 col-xl-3">
                <label class="form-label">Cari Karyawan</label>
                <input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Nama, NIK, email, atau RFID">
            </div>
            <div class="col-12 col-sm-auto d-flex gap-2">
                <button class="btn btn-primary px-4" type="submit">Filter</button>
                @if(request()->hasAny(['date', 'status', 'q']))
                    <a class="btn btn-outline-secondary" href="{{ route('admin.attendance.index') }}">Reset</a>
                @endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Telat</th>
                    <th>Lembur</th>
                    <th>Status</th>
                    <th>Edit Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensi as $attendance)
                    @php
                        $status = \App\Support\DisplayLabel::status($attendance->status);
                        $modalId = 'editStatusModal-'.$attendance->employee->id.'-'.$attendance->tanggal_absensi->format('Ymd');
                    @endphp
                    <tr>
                        <td><span class="ta-code-chip">{{ $attendance->tanggal_absensi->format('d M Y') }}</span></td>
                        <td>@include('shared._employee_table_cell', ['employee' => $attendance->employee])</td>
                        <td><span class="ta-time-pill">{{ $attendance->jam_masuk?->format('H:i') ?? '-' }}</span></td>
                        <td><span class="ta-time-pill">{{ $attendance->jam_pulang?->format('H:i') ?? '-' }}</span></td>
                        <td>{{ $attendance->menit_telat }} menit</td>
                        <td>{{ number_format($attendance->jam_lembur, 2) }} jam</td>
                        <td><span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                Edit Status
                            </button>
                        </td>
                    </tr>
                    @push('scripts')
                    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                <form method="POST" action="{{ route('admin.attendance.manual-status') }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="karyawan_id" value="{{ $attendance->employee->id }}">
                                    <input type="hidden" name="tanggal_absensi" value="{{ $attendance->tanggal_absensi->format('Y-m-d') }}">
                                    <div class="modal-header border-0 px-4 py-3">
                                        <div>
                                            <h5 class="modal-title mb-1">Edit Status Absensi</h5>
                                            <div class="small text-muted">{{ $attendance->employee->full_name }} - {{ $attendance->tanggal_absensi->format('d M Y') }}</div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-4">
                                        <div class="mb-3">
                                            <label class="form-label">Status Koreksi</label>
                                            <select class="form-select" name="status">
                                                <option value="present" @selected(in_array($attendance->status, ['present', 'late']))>Hadir</option>
                                                <option value="absent" @selected($attendance->status === 'absent')>Tidak Hadir</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jam Masuk Manual</label>
                                            <input class="form-control" type="time" name="jam_masuk_manual" value="{{ $attendance->jam_masuk?->format('H:i') }}">
                                            <div class="form-text">Wajib diisi jika status koreksi adalah Hadir. Jika lewat toleransi, sistem otomatis mencatat Telat.</div>
                                        </div>
                                        <div>
                                            <label class="form-label">Catatan Koreksi</label>
                                            <textarea class="form-control" name="catatan" rows="3" maxlength="500" placeholder="Contoh: Karyawan mengaku lupa scan masuk." required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 ta-surface-soft px-4 py-3">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endpush
                @empty
                    <tr><td colspan="8" class="ta-table-empty">Tidak ada data sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('shared._pagination', ['paginator' => $absensi, 'label' => 'absensi'])
</div>
@endsection
