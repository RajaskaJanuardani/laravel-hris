@php
    $routeName = $routeName ?? request()->route()->getName();
@endphp
<form class="row g-3 align-items-end" method="GET" action="{{ route($routeName) }}">
    {{ $slot }}
    <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary">Terapkan</button>
        <a class="btn btn-outline-secondary" href="{{ route($routeName) }}">Reset</a>
    </div>
</form>

