@extends('layouts.app')

@section('title', 'Beranda Admin')

@section('content')
<header class="g-page-header">
    <p class="g-overline mb-2">Pengelola Sistem</p>
    <h1 class="g-headline">{{ $user->name }}</h1>
    <p class="g-text-muted">Kelola data master, jadwal, dan backup. Admin tidak mengisi jurnal atas nama guru.</p>
</header>
<div class="row g-4">
    @foreach([
        ['admin.users.index', 'Pengguna', 'Kelola akun dan peran'],
        ['admin.schedules.index', 'Jadwal Mengajar', 'Guru, kelas, mapel, semester'],
        ['admin.backups.index', 'Backup & Restore', 'Cadangan database harian'],
    ] as [$route, $title, $desc])
    <div class="col-md-4">
        <a href="{{ route($route) }}" class="g-link-card g-card g-card--hover g-card__body h-100">
            <h2 class="g-subhead mb-2">{{ $title }}</h2>
            <p class="g-text-muted g-text-small mb-0">{{ $desc }}</p>
        </a>
    </div>
    @endforeach
</div>
@endsection
