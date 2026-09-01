<article class="binduz-er-agenda-card-item binduz-er-agenda-card-item--catchy h-100">
    <div class="binduz-er-agenda-card-item__datebox" aria-hidden="true">
        <span class="binduz-er-agenda-card-item__day">{{ $agenda->event_date->format('d') }}</span>
        <span class="binduz-er-agenda-card-item__month">{{ $agenda->event_date->translatedFormat('M') }}</span>
    </div>

    <div class="binduz-er-agenda-card-item__body">
        <div class="binduz-er-agenda-card-item__meta">
            <span class="binduz-er-agenda-card-item__badge">{{ $agenda->eventTypeLabel() }}</span>
            @if ($agenda->is_online)
                <span class="binduz-er-agenda-card-item__badge binduz-er-agenda-card-item__badge--online">Daring</span>
            @endif
        </div>

        <h4 class="binduz-er-agenda-card-item__title">
            <a href="{{ route('public.agenda.show', $agenda->slug) }}" title="{{ $agenda->title }}">{{ $agenda->title }}</a>
        </h4>

        <ul class="binduz-er-agenda-card-item__details">
            <li>
                <i class="fal fa-calendar-alt"></i>
                <span>{{ $agenda->formattedEventDate() }}</span>
            </li>
            @if ($agenda->start_time)
                <li>
                    <i class="fal fa-clock"></i>
                    <span>{{ $agenda->formattedTimeRange() }}</span>
                </li>
            @endif
            <li>
                <i class="fal fa-map-marker-alt"></i>
                <span>{{ $agenda->locationDisplay() }}</span>
            </li>
        </ul>

        @if ($agenda->excerpt || $agenda->content)
            <p class="binduz-er-agenda-card-item__excerpt mb-0">
                {{ $agenda->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($agenda->content), 110) }}
            </p>
        @endif
    </div>

    <a href="{{ route('public.agenda.show', $agenda->slug) }}" class="binduz-er-agenda-card-item__cta" aria-label="Lihat detail {{ $agenda->title }}">
        <i class="fal fa-arrow-right"></i>
    </a>
</article>
