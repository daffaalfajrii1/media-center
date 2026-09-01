<div class="kecamatan-berita-author">
    <div class="kecamatan-berita-author__avatar">
        @if ($berita->authorAvatarUrl())
            <img src="{{ $berita->authorAvatarUrl() }}" alt="{{ $berita->authorName() }}">
        @else
            <span>{{ $berita->authorInitial() }}</span>
        @endif
    </div>
    <div class="kecamatan-berita-author__info">
        <div class="kecamatan-berita-author__label">Ditulis oleh</div>
        <div class="kecamatan-berita-author__name">{{ $berita->authorName() }}</div>
        @if ($berita->authorRoleLabel())
            <div class="kecamatan-berita-author__role">{{ $berita->authorRoleLabel() }}</div>
        @endif
    </div>
</div>
