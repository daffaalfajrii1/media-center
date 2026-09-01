@if ($websiteCarousels->isNotEmpty())
    <section class="kecamatan-home-carousel">
        <div id="kecamatanHomeCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="{{ $websiteSettings->carouselDurationMs() }}">
            @if ($websiteCarousels->count() > 1)
                <div class="carousel-indicators">
                    @foreach ($websiteCarousels as $index => $slide)
                        <button
                            type="button"
                            data-bs-target="#kecamatanHomeCarousel"
                            data-bs-slide-to="{{ $index }}"
                            @class(['active' => $index === 0])
                            @if ($index === 0) aria-current="true" @endif
                            aria-label="Slide {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            @endif

            <div class="carousel-inner">
                @foreach ($websiteCarousels as $index => $slide)
                    <div @class(['carousel-item', 'active' => $index === 0])>
                        @if ($slide->link_url)
                            <a href="{{ $slide->link_url }}" class="kecamatan-home-carousel__link" target="_blank" rel="noopener">
                                <img src="{{ $slide->imageUrl() }}" class="d-block w-100 kecamatan-home-carousel__img" alt="{{ $slide->title ?: 'Carousel '.$loop->iteration }}">
                            </a>
                        @else
                            <img src="{{ $slide->imageUrl() }}" class="d-block w-100 kecamatan-home-carousel__img" alt="{{ $slide->title ?: 'Carousel '.$loop->iteration }}">
                        @endif
                        @if ($slide->title || $slide->caption)
                            <div class="carousel-caption kecamatan-home-carousel__caption">
                                @if ($slide->title)
                                    <h5 class="mb-1">{{ $slide->title }}</h5>
                                @endif
                                @if ($slide->caption)
                                    <p class="mb-0">{{ $slide->caption }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($websiteCarousels->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#kecamatanHomeCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Sebelumnya</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#kecamatanHomeCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Berikutnya</span>
                </button>
            @endif
        </div>
    </section>
@endif
