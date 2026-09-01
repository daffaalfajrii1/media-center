@props(['items' => []])
@php
    $trail = $items;
    $current = null;
    if (! empty($items)) {
        $current = array_pop($trail);
    }
@endphp
<div class="binduz-er-breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="binduz-er-breadcrumb-box">
                    <nav aria-label="breadcrumb" class="binduz-er-breadcrumb-nav">
                        <ol class="breadcrumb binduz-er-breadcrumb-trail mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                            @foreach ($trail as $item)
                                <li class="breadcrumb-item">
                                    @if (! empty($item['url']))
                                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                                    @else
                                        {{ $item['label'] }}
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                        @if ($current)
                            <div class="binduz-er-breadcrumb-current" aria-current="page">{{ $current['label'] }}</div>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
