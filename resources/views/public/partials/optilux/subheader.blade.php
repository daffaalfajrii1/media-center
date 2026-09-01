<section id="subheader" @class(['bg-color-op-1', 'kecamatan-subheader--has-thumb' => !empty($thumbnailUrl)])>
    @if (!empty($thumbnailUrl))
        <div class="kecamatan-subheader__bg-thumb" style="background-image: url('{{ $thumbnailUrl }}');" aria-hidden="true"></div>
    @endif
    <div class="container relative z-2">
        <div class="row gy-4 gx-5 align-items-center">
            <div class="col-lg-12">
                <h1 @class(['kecamatan-subheader__title' => !empty($thumbnailUrl), 'split' => empty($thumbnailUrl)])>{{ $title }}</h1>
                <ul class="crumb wow fadeInUp">
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    @foreach ($breadcrumbs ?? [] as $crumb)
                        @if ($loop->last)
                            <li class="active">{{ $crumb['label'] }}</li>
                        @else
                            <li><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                        @endif
                    @endforeach
                </ul>
                @if (!empty($thumbnailUrl))
                    <div class="kecamatan-subheader__thumb">
                        <img src="{{ $thumbnailUrl }}" alt="{{ $title }}">
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
