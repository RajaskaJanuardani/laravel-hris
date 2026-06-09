@if($paginator->hasPages() || $paginator->total())
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 px-4 py-3 border-top">
        <div class="small text-muted">
            @if($paginator->total())
                Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari {{ $paginator->total() }} {{ $label ?? 'data' }}
            @else
                Tidak ada {{ $label ?? 'data' }} yang ditampilkan
            @endif
        </div>
        @if($paginator->hasPages())
            <div>
                {{ $paginator->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endif
