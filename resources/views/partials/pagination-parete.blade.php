@if ($paginator->hasPages())
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding-top:16px;margin-top:4px;font-size:13px;color:var(--gray-500);">
  <span>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}</span>
  <nav style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;" aria-label="Pagination">
    @if ($paginator->onFirstPage())
      <span class="btn btn-outline btn-sm" style="opacity:0.45;pointer-events:none;">Sebelumnya</span>
    @else
      <a class="btn btn-outline btn-sm" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
    @endif

    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="btn btn-outline btn-sm" style="opacity:0.55;cursor:default;pointer-events:none;">{{ $element }}</span>
      @endif
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="btn btn-blue btn-sm" style="min-width:38px;text-align:center;">{{ $page }}</span>
          @else
            <a class="btn btn-outline btn-sm" style="min-width:38px;text-align:center;" href="{{ $url }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    @if ($paginator->hasMorePages())
      <a class="btn btn-outline btn-sm" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
    @else
      <span class="btn btn-outline btn-sm" style="opacity:0.45;pointer-events:none;">Berikutnya</span>
    @endif
  </nav>
</div>
@endif
