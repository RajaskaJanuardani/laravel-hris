@extends('layouts.auth')
@section('content')
<form method="POST" action="{{ route('login') }}" class="vstack gap-3">
    @csrf
    <div>
        <label class="form-label">Email</label>
        <input class="form-control form-control-lg" type="email" name="email" value="{{ old('email') }}" required autofocus>
    </div>
    <div>
        <label class="form-label">Password</label>
        <input class="form-control form-control-lg" type="password" name="password" required>
    </div>
    <label class="form-check">
        <input class="form-check-input" type="checkbox" name="remember">
        <span class="form-check-label">Ingat saya</span>
    </label>
    <button class="btn btn-primary btn-lg">Masuk</button>
    <div class="small text-muted">Dummy: admin@hris.test / password</div>
</form>
@endsection
