@if ($paginator->hasPages())
    <nav class="binduz-er-pagination-nav" aria-label="Navigasi halaman">
        <div class="binduz-er-pagination-nav__mobile d-md-none">
            @if ($paginator->onFirstPage())
                <span class="binduz-er-pagination-nav__btn is-disabled">Sebelumnya</span>
            @else
                <a class="binduz-er-pagination-nav__btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
            @endif

            <span class="binduz-er-pagination-nav__status">
                Hal {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a class="binduz-er-pagination-nav__btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
            @else
                <span class="binduz-er-pagination-nav__btn is-disabled">Berikutnya</span>
            @endif
        </div>

        <div class="binduz-er-pagination-nav__desktop d-none d-md-flex">
            <p class="binduz-er-pagination-nav__summary mb-0">
                Menampilkan
                <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
                dari <strong>{{ $paginator->total() }}</strong>
            </p>

            <ul class="pagination mb-0">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">&lsaquo;</a>
                    </li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">&rsaquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
