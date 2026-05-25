<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'HRIS RFID' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/e-absensi.png') }}">
    <script>
        // Apply theme before first paint to avoid flicker.
        (() => {
            try {
                const stored = localStorage.getItem('ta-theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = stored || (prefersDark ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.documentElement.style.colorScheme = theme;
            } catch (e) {}
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('tailadmin-nextjs-1.0.0/laravel-tailadmin.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="ta-shell">
    <aside class="ta-sidebar">
        <a class="ta-logo" href="{{ route('dashboard') }}">
            <span class="ta-logo-mark">
                <img
                    src="{{ asset('images/logo/e-absensi.png') }}"
                    alt="E-Absensi"
                    onerror="this.onerror=null;this.src='{{ asset('tailadmin-nextjs-1.0.0/public/images/logo/logo-icon.svg') }}';"
                >
            </span>
            <span>
                <span class="ta-logo-title d-block">HRIS RFID</span>
                <span class="ta-logo-subtitle">Employee attendance</span>
            </span>
        </a>
        <div class="ta-menu-label">Menu</div>
        <nav class="nav flex-column">
            @if(auth()->user()?->isAdmin())
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/grid.svg') }}" alt="">Dashboard</a>
                <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/group.svg') }}" alt="">Karyawan</a>
                <a class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/time.svg') }}" alt="">Absensi RFID</a>
                <a class="nav-link {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}" href="{{ route('admin.leaves.index') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/calender-line.svg') }}" alt="">Cuti & Izin</a>
                <a class="nav-link {{ request()->routeIs('admin.overtime.*') ? 'active' : '' }}" href="{{ route('admin.overtime.index') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/task.svg') }}" alt="">Approval Lembur</a>
                <a class="nav-link {{ request()->routeIs('admin.payroll.*') ? 'active' : '' }}" href="{{ route('admin.payroll.index') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/dollar-line.svg') }}" alt="">Payroll</a>
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/docs.svg') }}" alt="">Laporan</a>
                <a class="nav-link {{ request()->routeIs('admin.settings.leave-types') ? 'active' : '' }}" href="{{ route('admin.settings.leave-types') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/list.svg') }}" alt="">Tipe Cuti</a>
            @else
                <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/grid.svg') }}" alt="">Dashboard Saya</a>
                <a class="nav-link {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}" href="{{ route('employee.profile.edit') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/user-circle.svg') }}" alt="">Profil Saya</a>
                <a class="nav-link {{ request()->routeIs('employee.attendance.*') ? 'active' : '' }}" href="{{ route('employee.attendance.history') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/time.svg') }}" alt="">Riwayat Absensi</a>
                <a class="nav-link {{ request()->routeIs('employee.overtime.*') ? 'active' : '' }}" href="{{ route('employee.overtime.index') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/task.svg') }}" alt="">Lembur Saya</a>
                <a class="nav-link {{ request()->routeIs('employee.leaves.*') ? 'active' : '' }}" href="{{ route('employee.leaves.index') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/calender-line.svg') }}" alt="">Cuti Saya</a>
                <a class="nav-link {{ request()->routeIs('employee.payslip.*') ? 'active' : '' }}" href="{{ route('employee.payslip.index') }}"><img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/dollar-line.svg') }}" alt="">Slip Gaji</a>
            @endif
        </nav>
    </aside>
    <main class="ta-main">
        <div class="ta-header">
            <div class="d-flex align-items-center gap-3">
                <button class="ta-mobile-toggle" type="button" data-sidebar-toggle aria-label="Toggle sidebar">
                    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1H17M1 7H17M1 13H17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                </button>
                <div>
                    <h1 class="ta-header-title">{{ $heading ?? 'Dashboard' }}</h1>
                    <div class="ta-header-meta">{{ now()->translatedFormat('l, d F Y') }}</div>
                </div>
            </div>
            <div class="ta-header-actions">
                <span class="badge rounded-pill badge-soft">{{ str_replace('_', ' ', auth()->user()->role ?? '') }}</span>
                <button type="button" class="btn btn-outline-secondary btn-sm ta-theme-toggle" data-theme-toggle aria-label="Toggle theme">
                    <span class="ta-theme-icon" aria-hidden="true"></span>
                    <span class="ta-theme-label d-none d-md-inline">Mode Gelap</span>
                </button>
                <div class="ta-user">
                    @php
                        $employeeAvatar = auth()->user()?->employee?->profile_photo_url ?? asset('tailadmin-nextjs-1.0.0/public/images/user/owner.jpg');
                    @endphp
                    <img class="ta-avatar" src="{{ $employeeAvatar }}" alt="User">
                    <div class="text-end">
                        <div class="fw-semibold">{{ auth()->user()->name ?? 'Guest' }}</div>
                        <div class="small text-muted">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-secondary btn-sm">Logout</button></form>
            </div>
        </div>
        <section class="ta-content">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            @yield('content')
        </section>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('tailadmin-nextjs-1.0.0/laravel-tailadmin.js') }}"></script>
<script>
    (() => {
        const btn = document.querySelector('[data-theme-toggle]');
        if (!btn) return;

        const icon = btn.querySelector('.ta-theme-icon');
        const label = btn.querySelector('.ta-theme-label');

        const setChartTheme = (theme) => {
            if (!window.Chart) return;
            const dark = theme === 'dark';
            Chart.defaults.color = dark ? '#c8d4e8' : '#475467';
            Chart.defaults.borderColor = dark ? 'rgba(159,176,207,.25)' : '#e4e7ec';
        };

        const setUi = (theme) => {
            document.documentElement.style.colorScheme = theme;
            setChartTheme(theme);
            if (!icon) return;
            const isDark = theme === 'dark';
            icon.innerHTML = isDark
                ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.8 6.8 0 0 0 9.8 9.8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z" stroke="currentColor" stroke-width="1.8"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';

            if (label) label.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
        };

        const getTheme = () => document.documentElement.classList.contains('dark') ? 'dark' : 'light';

        setUi(getTheme());

        btn.addEventListener('click', () => {
            const next = getTheme() === 'dark' ? 'light' : 'dark';
            document.documentElement.classList.toggle('dark', next === 'dark');
            try { localStorage.setItem('ta-theme', next); } catch (e) {}
            setUi(next);
        });
    })();
</script>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-danger bg-gradient text-white px-4 py-3">
                <div>
                    <h5 class="modal-title mb-1">Konfirmasi Hapus</h5>
                    <div class="small opacity-75">Tindakan ini tidak bisa dibatalkan.</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 9v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M12 17.2h.01" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/><path d="M10.3 4.5 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 4.5a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-2" id="confirmDeleteTitle">Hapus data ini?</h6>
                        <p class="text-muted mb-0" id="confirmDeleteMessage">Aksi ini akan menghapus data karyawan dan menonaktifkan akun login terkait.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 ta-surface-soft px-4 py-3">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteSubmit">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
<script>
    (() => {
        const modalEl = document.getElementById('confirmDeleteModal');
        if (!modalEl) return;

        const modal = new bootstrap.Modal(modalEl);
        const titleEl = document.getElementById('confirmDeleteTitle');
        const messageEl = document.getElementById('confirmDeleteMessage');
        const submitEl = document.getElementById('confirmDeleteSubmit');
        let activeForm = null;

        document.querySelectorAll('[data-confirm-delete]').forEach((trigger) => {
            trigger.addEventListener('click', (event) => {
                event.preventDefault();
                activeForm = document.querySelector(trigger.getAttribute('data-form-target'));
                titleEl.textContent = trigger.getAttribute('data-confirm-title') || 'Hapus data ini?';
                messageEl.textContent = trigger.getAttribute('data-confirm-message') || 'Aksi ini tidak bisa dibatalkan.';
                modal.show();
            });
        });

        submitEl.addEventListener('click', () => {
            if (activeForm) activeForm.submit();
        });
    })();
</script>
@stack('scripts')
</body>
</html>
