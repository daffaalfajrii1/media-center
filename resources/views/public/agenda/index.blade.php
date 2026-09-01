@extends('public.layouts.binduz')

@section('title', 'Agenda')

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', ['items' => [['label' => 'Agenda']]])
@endsection

@section('content')
    <section class="binduz-er-page-content">
        <div class="container">
            <div class="binduz-er-section-title mb-30">
                <h3>Agenda Kegiatan</h3>
            </div>
            <div class="mb-4">
                <a href="{{ route('public.agenda.index', ['filter' => 'upcoming']) }}" class="binduz-er-main-btn me-2 {{ $filter === 'upcoming' ? '' : 'btn-outline' }}">Mendatang</a>
                <a href="{{ route('public.agenda.index', ['filter' => 'past']) }}" class="binduz-er-main-btn {{ $filter === 'past' ? '' : 'btn-outline' }}">Selesai</a>
            </div>
            <div class="row">
                @forelse ($agendas as $agenda)
                    <div class="col-lg-4 col-md-6 mb-30">
                        <div class="binduz-er-layanan-card h-100">
                            @if ($agenda->thumbnailUrl())
                                <img src="{{ $agenda->thumbnailUrl() }}" alt="{{ $agenda->title }}" class="w-100 mb-3">
                            @endif
                            <div class="fw-bold text-danger mb-2">{{ $agenda->formattedEventDate() }}</div>
                            @if ($agenda->formattedTimeRange())
                                <div class="small text-muted mb-2"><i class="fal fa-clock"></i> {{ $agenda->formattedTimeRange() }}</div>
                            @endif
                            <h4><a href="{{ route('public.agenda.show', $agenda->slug) }}">{{ $agenda->title }}</a></h4>
                            <p><i class="fal fa-map-marker-alt"></i> {{ $agenda->locationDisplay() }}</p>
                            <span class="badge bg-primary">{{ $agenda->eventTypeLabel() }}</span>
                            <p class="mt-3 mb-0">{{ $agenda->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($agenda->content), 120) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="binduz-er-layanan-card text-center">
                            <p class="mb-0">Tidak ada agenda {{ $filter === 'past' ? 'yang telah selesai' : 'mendatang' }}.</p>
                        </div>
                    </div>
                @endforelse
            </div>
            @if ($agendas->hasPages())
                <div class="binduz-er-pagination">{{ $agendas->links() }}</div>
            @endif
        </div>
    </section>
@endsection
