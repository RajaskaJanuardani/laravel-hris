@php
    $displayEmployee = $employee ?? null;
    $displayName = $displayEmployee?->full_name ?? $name ?? '-';
    $displayMeta = $displayEmployee?->karyawan_id ?? $meta ?? null;
    $initials = collect(explode(' ', $displayName))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
@endphp

<div class="ta-row-person">
    <span class="ta-row-avatar">{{ $initials ?: '-' }}</span>
    <div>
        <div class="fw-semibold">{{ $displayName }}</div>
        @if($displayMeta)
            <div class="small text-muted">{{ $displayMeta }}</div>
        @endif
    </div>
</div>
