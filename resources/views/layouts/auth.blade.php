<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'WFO.id' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/web-logo-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('tailadmin-nextjs-1.0.0/laravel-tailadmin.css') }}" rel="stylesheet">
</head>
<body class="ta-auth-page">
    <main class="ta-auth-shell">
        <section class="ta-auth-brand">
            <a class="ta-logo mb-4" href="{{ route('login') }}">
                <span class="ta-logo-mark">
                    <img
                        src="{{ asset('images/logo/web-logo.png') }}"
                        alt="WFO.id"
                        onerror="this.onerror=null;this.src='{{ asset('tailadmin-nextjs-1.0.0/public/images/logo/logo-icon.svg') }}';"
                    >
                </span>
                <span>
                    <span class="ta-logo-title d-block">WFO.id</span>
                    <span class="ta-logo-subtitle">Absensi & payroll</span>
                </span>
            </a>
            <h1 class="ta-auth-title mb-3">WFO.id</h1>
            <p class="ta-auth-copy mb-0">Sistem absensi RFID, cuti, lembur, dan payroll karyawan dalam satu dashboard yang rapi.</p>
        </section>
        <section class="ta-auth-panel">
            <div class="ta-auth-card">
                <div class="mb-4">
                    <div class="fw-semibold h4 mb-1">Masuk ke dashboard</div>
                    <div class="text-muted">Gunakan akun HRIS yang sudah terdaftar.</div>
                </div>
                @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
