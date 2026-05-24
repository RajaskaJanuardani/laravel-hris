@extends('layouts.auth')
@section('content')
<form method="POST" action="{{ route('register') }}" class="vstack gap-3">
    @csrf
    <input class="form-control form-control-lg" name="name" placeholder="Nama lengkap" required>
    <input class="form-control form-control-lg" type="email" name="email" placeholder="Email" required>
    <input class="form-control form-control-lg" type="password" name="password" placeholder="Password" required>
    <input class="form-control form-control-lg" type="password" name="password_confirmation" placeholder="Konfirmasi password" required>
    <button class="btn btn-primary btn-lg">Daftar</button>
    <a href="{{ route('login') }}" class="text-decoration-none">Sudah punya akun?</a>
</form>
@endsection
