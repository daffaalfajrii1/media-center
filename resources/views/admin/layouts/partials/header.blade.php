@php $user = auth()->user(); @endphp
<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left d-flex align-items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="kecamatan-admin-header-brand d-none d-md-flex align-items-center gap-2 text-decoration-none">
                @if ($websiteSettings->logoUrl())
                    <img src="{{ $websiteSettings->logoUrl() }}" alt="{{ $websiteSettings->siteName() }}" class="kecamatan-admin-header-brand__logo">
                @endif
                <span class="kecamatan-admin-header-brand__name">{{ $websiteSettings->siteName() }}</span>
            </a>
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button"><i class="feather-align-left"></i></a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none"><i class="feather-arrow-right"></i></a>
            </div>
        </div>

        <div class="header-right ms-auto kecamatan-header-profile">
            <div class="kecamatan-profile-wrap">
                <a href="javascript:void(0);" class="d-flex align-items-center gap-2 text-decoration-none kecamatan-profile-toggle">
                    <span class="d-none d-md-inline text-muted fs-12 text-truncate">{{ $user->name }}</span>
                    @include('admin.partials.user-avatar', ['user' => $user, 'size' => 'md', 'class' => 'flex-shrink-0'])
                </a>

                <div class="kecamatan-profile-menu shadow">
                    <div class="kecamatan-profile-menu-header">
                        <div class="fw-bold text-truncate">{{ $user->name }}</div>
                        <div class="fs-12 text-muted text-truncate">{{ $user->email }}</div>
                        @if ($user->roles->isNotEmpty())
                            <div class="fs-11 text-primary mt-1">{{ $user->getRoleNames()->join(', ') }}</div>
                        @endif
                    </div>
                    <a href="{{ route('profile.edit') }}" class="kecamatan-profile-menu-item">
                        <i class="feather-edit-3 me-2"></i> Edit Profil
                    </a>
                    <div class="kecamatan-profile-menu-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="kecamatan-profile-menu-item kecamatan-profile-logout w-100 border-0 bg-transparent text-start">
                            <i class="feather-log-out me-2"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
