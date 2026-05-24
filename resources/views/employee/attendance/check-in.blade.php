@extends('layouts.app', ['heading' => 'Kartu RFID Saya'])
@section('content')
<div class="card p-4"><h2 class="h5">{{ $employee->full_name ?? 'Profil belum tersedia' }}</h2><p class="text-muted">Scan dilakukan melalui alat RFID atau simulator admin.</p><div class="h4">{{ $employee?->getActiveRFIDCard()?->uid ?? 'Kartu belum terdaftar' }}</div></div>
@endsection
