@php
    $author = $berita->author;
    $roleLabel = $berita->authorRoleLabel() ?? 'Penulis';
@endphp
<div class="binduz-er-archived-sidebar-about">
    <div class="binduz-er-user">
        @if ($berita->authorAvatarUrl())
            <img src="{{ $berita->authorAvatarUrl() }}" alt="{{ $berita->authorName() }}">
        @else
            <span class="binduz-er-user-initial">{{ $berita->authorInitial() }}</span>
        @endif
        <div class="binduz-er-icon">
            <i class="fal fa-newspaper"></i>
        </div>
    </div>
    <span>{{ $roleLabel }}</span>
    <h4 class="binduz-er-title">{{ $berita->authorName() }}</h4>
    @if ($websiteSettings->social_facebook || $websiteSettings->social_instagram || $websiteSettings->social_youtube)
        <ul>
            @if ($websiteSettings->social_facebook)
                <li><a href="{{ $websiteSettings->social_facebook }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a></li>
            @endif
            @if ($websiteSettings->social_instagram)
                <li><a href="{{ $websiteSettings->social_instagram }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a></li>
            @endif
            @if ($websiteSettings->social_youtube)
                <li><a href="{{ $websiteSettings->social_youtube }}" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a></li>
            @endif
        </ul>
    @endif
</div>
