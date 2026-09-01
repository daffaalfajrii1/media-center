@extends('public.layouts.binduz')

@section('title', $page->title)
@section('meta_description', $page->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160))

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', [
        'items' => [
            ['label' => 'Profil', 'url' => $profilPages->first() ? route('public.profil.show', $profilPages->first()->slug) : route('home')],
            ['label' => $page->title],
        ],
    ])
@endsection

@section('content')
    <section class="binduz-er-page-content">
        <div class="container">
            <div class="row">
                @if ($profilPages->count() > 1)
                    <div class="col-lg-3 mb-30">
                        @foreach ($profilPages as $profilPage)
                            <a href="{{ route('public.profil.show', $profilPage->slug) }}"
                               class="binduz-er-layanan-card d-block mb-2 {{ $profilPage->id === $page->id ? 'border border-danger' : '' }}">
                                <h5 class="mb-0">{{ $profilPage->title }}</h5>
                            </a>
                        @endforeach
                    </div>
                    <div class="col-lg-9">
                        @if ($page->excerpt)
                            <p class="lead">{{ $page->excerpt }}</p>
                        @endif
                        <div class="kecamatan-content">@safeHtml($page->content)</div>
                    </div>
                @else
                    <div class="col-lg-12">
                        @if ($page->excerpt)
                            <p class="lead">{{ $page->excerpt }}</p>
                        @endif
                        <div class="kecamatan-content">@safeHtml($page->content)</div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
