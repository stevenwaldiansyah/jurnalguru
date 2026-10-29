<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') — JurnalGuru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/genesis.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="g-genesis">
@auth
@php
    $initials = collect(explode(' ', auth()->user()->name))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $navLinks = [
        ['route' => 'dashboard', 'label' => 'Beranda', 'icon' => 'bi-house', 'active' => request()->routeIs('dashboard'), 'show' => true],
        ['route' => 'journals.search', 'label' => 'Cari Catatan', 'icon' => 'bi-search', 'active' => request()->routeIs('journals.search'), 'show' => auth()->user()->isGuru() || auth()->user()->canViewAllJournals()],
        ['route' => 'recap.index', 'label' => 'Rekap', 'icon' => 'bi-file-earmark-text', 'active' => request()->routeIs('recap.*'), 'show' => auth()->user()->isGuru() || auth()->user()->canViewAllJournals()],
    ];
    if (auth()->user()->isAdmin()) {
        foreach ([
            ['admin.users.index', 'Pengguna', 'admin.users.*'],
            ['admin.classes.index', 'Kelas', 'admin.classes.*'],
            ['admin.students.index', 'Siswa', 'admin.students.*'],
            ['admin.subjects.index', 'Mapel', 'admin.subjects.*'],
            ['admin.semesters.index', 'Semester', 'admin.semesters.*'],
            ['admin.schedules.index', 'Jadwal', 'admin.schedules.*'],
            ['admin.backups.index', 'Backup', 'admin.backups.*'],
        ] as [$r, $l, $p]) {
            $navLinks[] = ['route' => $r, 'label' => $l, 'icon' => 'bi-dot', 'active' => request()->routeIs($p), 'show' => true, 'admin' => true];
        }
    }
@endphp

<header class="g-nav">
    <div class="g-nav__inner">
        <a href="{{ route('dashboard') }}" class="g-nav__brand">
            <span class="g-nav__logo"><i class="bi bi-journal-bookmark-fill"></i></span>
            <span class="g-nav__brand-text">
                <strong>JurnalGuru</strong>
                <span>SMK Muhammadiyah 04 Boyolali</span>
            </span>
        </a>

        <nav class="g-nav__links" aria-label="Navigasi utama">
            @foreach($navLinks as $link)
                @if($link['show'])
                    <a href="{{ route($link['route']) }}" class="g-nav__link @if($link['active']) is-active @endif @if(!empty($link['admin'])) g-text-small @endif">
                        @if(($link['icon'] ?? '') !== 'bi-dot')<i class="bi {{ $link['icon'] }}"></i>@endif
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="g-nav__actions">
            <div class="g-nav__user">
                <span class="g-avatar" title="{{ auth()->user()->name }}">{{ $initials }}</span>
                <div>
                    <div style="font-weight:500;color:var(--color-text);font-size:14px;">{{ auth()->user()->name }}</div>
                    <span class="g-chip g-chip--neutral">{{ auth()->user()->role->label() }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm d-none d-md-inline-flex align-items-center gap-1"><i class="bi bi-box-arrow-right"></i> Keluar</button>
            </form>
            <button type="button" class="g-nav__menu-btn" id="g-menu-open" aria-label="Buka menu"><i class="bi bi-list"></i></button>
        </div>
    </div>
</header>

<div class="g-drawer-backdrop" id="g-drawer-backdrop"></div>
<aside class="g-drawer" id="g-drawer" aria-hidden="true">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <span class="g-overline">Menu</span>
        <button type="button" class="g-nav__menu-btn" id="g-menu-close" aria-label="Tutup menu"><i class="bi bi-x-lg"></i></button>
    </div>
    @foreach($navLinks as $link)
        @if($link['show'])
            <a href="{{ route($link['route']) }}" class="g-nav__link @if($link['active']) is-active @endif">{{ $link['label'] }}</a>
        @endif
    @endforeach
    <hr style="border-color:var(--color-border);margin:16px 0;">
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-outline-danger w-100">Keluar</button></form>
</aside>

<main class="g-main">
    <div class="g-container">
        @include('layouts.flash')
        @yield('content')
    </div>
</main>

@else
    @yield('content')
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const open = document.getElementById('g-menu-open');
    const close = document.getElementById('g-menu-close');
    const drawer = document.getElementById('g-drawer');
    const backdrop = document.getElementById('g-drawer-backdrop');
    if (!open || !drawer) return;
    function setOpen(on) {
        drawer.classList.toggle('is-open', on);
        backdrop.classList.toggle('is-open', on);
        drawer.setAttribute('aria-hidden', on ? 'false' : 'true');
        document.body.style.overflow = on ? 'hidden' : '';
    }
    open.addEventListener('click', () => setOpen(true));
    close?.addEventListener('click', () => setOpen(false));
    backdrop?.addEventListener('click', () => setOpen(false));
})();
</script>
@stack('scripts')
</body>
</html>
