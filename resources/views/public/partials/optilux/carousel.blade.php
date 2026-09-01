@if ($websiteCarousels->isNotEmpty())
    <section class="kecamatan-home-carousel relative">
        <div id="kecamatanHomeCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="{{ $websiteSettings->carouselDurationMs() }}">
            <div class="carousel-inner">
                @foreach ($websiteCarousels as $index => $slide)
                    <div @class(['carousel-item', 'active' => $index === 0])>
                        <div class="kecamatan-carousel-slide">
                            @if ($slide->link_url)
                                <a href="{{ $slide->link_url }}" target="_blank" rel="noopener" class="d-block">
                                    <img src="{{ $slide->imageUrl() }}" alt="{{ $slide->title ?: 'Carousel '.$loop->iteration }}">
                                </a>
                            @else
                                <img src="{{ $slide->imageUrl() }}" alt="{{ $slide->title ?: 'Carousel '.$loop->iteration }}">
                            @endif

                            @if ($slide->title || $slide->caption)
                                <div class="kecamatan-carousel-caption">
                                    @if ($slide->title)
                                        <h2 class="kecamatan-carousel-caption__title">{{ $slide->title }}</h2>
                                    @endif
                                    @if ($slide->caption)
                                        <p class="kecamatan-carousel-caption__text mb-0">{{ $slide->caption }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($websiteCarousels->count() > 1)
                <button class="kecamatan-carousel-nav kecamatan-carousel-nav--prev" type="button" data-bs-target="#kecamatanHomeCarousel" data-bs-slide="prev" aria-label="Slide sebelumnya">
                    <span aria-hidden="true"></span>
                </button>
                <button class="kecamatan-carousel-nav kecamatan-carousel-nav--next" type="button" data-bs-target="#kecamatanHomeCarousel" data-bs-slide="next" aria-label="Slide berikutnya">
                    <span aria-hidden="true"></span>
                </button>

                <div class="kecamatan-carousel-dots">
                    @foreach ($websiteCarousels as $index => $slide)
                        <button
                            type="button"
                            data-bs-target="#kecamatanHomeCarousel"
                            data-bs-slide-to="{{ $index }}"
                            @class(['kecamatan-carousel-dot', 'active' => $index === 0])
                            @if ($index === 0) aria-current="true" @endif
                            aria-label="Slide {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif
