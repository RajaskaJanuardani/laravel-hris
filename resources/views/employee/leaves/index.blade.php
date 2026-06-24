@extends('layouts.app', ['heading' => 'Cuti Saya'])
@section('content')
@php($today = now()->toDateString())
<div class="row g-4">
    <div class="col-lg-4">
        <form class="card p-4" method="POST" action="{{ route('employee.leaves.store') }}">
            @csrf
            <h2 class="h5">Ajukan Cuti/Izin</h2>
            <select class="form-select mb-3" name="jenis_cuti_id">
                @foreach($leaveTypes as $type)
                    <option value="{{ $type->id }}" @selected(old('jenis_cuti_id') == $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
            <input class="form-control mb-3" type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" min="{{ $today }}" required data-leave-start>
            <input class="form-control mb-3" type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" min="{{ $today }}" required data-leave-end>
            <textarea class="form-control mb-3" name="alasan" placeholder="Alasan">{{ old('alasan') }}</textarea>
            <button class="btn btn-primary w-100">Kirim</button>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="card overflow-hidden">
            <div class="p-4 border-bottom"><h2 class="h5 mb-0">Riwayat Pengajuan</h2></div>
            <div class="table-responsive">
                <table class="table align-middle"><thead><tr><th>Tipe</th><th>Tanggal</th><th>Status</th></tr></thead><tbody>@forelse($leaves as $leave)@php($status = \App\Support\DisplayLabel::status($leave->status))<tr><td>{{ $leave->leaveType->name }}</td><td><span class="ta-code-chip">{{ $leave->tanggal_mulai->format('d M') }} - {{ $leave->tanggal_selesai->format('d M Y') }}</span></td><td><span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td></tr>@empty<tr><td colspan="3" class="ta-table-empty">Belum ada data.</td></tr>@endforelse</tbody></table>
            </div>
            @if(method_exists($leaves, 'links'))
                @include('shared._pagination', ['paginator' => $leaves, 'label' => 'pengajuan'])
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const startInput = document.querySelector('[data-leave-start]');
        const endInput = document.querySelector('[data-leave-end]');
        if (!startInput || !endInput) return;

        const today = startInput.min;
        const syncEndMinimum = () => {
            const minimumDate = startInput.value && startInput.value > today ? startInput.value : today;
            endInput.min = minimumDate;

            if (endInput.value && endInput.value < minimumDate) {
                endInput.value = '';
            }
        };

        startInput.addEventListener('change', syncEndMinimum);
        syncEndMinimum();
    })();
</script>
@endpush
