@php
    $crumbs = $crumbs ?? [];
    $actions = $actions ?? null;
@endphp

<div class="page-header">
    <div class="page-header-left d-flex align-items-center flex-wrap gap-2">
        <div class="page-header-title">
            <h5 class="m-b-10 mb-0">{{ $title }}</h5>
        </div>
        @if (count($crumbs) > 0)
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Beranda</a>
                </li>
                @foreach ($crumbs as $crumb)
                    <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}" @if($loop->last) aria-current="page" @endif>
                        @if (! empty($crumb['url']) && ! $loop->last)
                            <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                        @else
                            {{ $crumb['label'] }}
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    @if (! empty($actions))
        <div class="page-header-right ms-auto">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                {!! $actions !!}
            </div>
        </div>
    @endif
</div>
