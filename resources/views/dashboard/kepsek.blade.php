@extends('layouts.app')

@section('title', 'Beranda Kepala Sekolah')

@section('content')
<header class="g-page-header">
    <p class="g-overline mb-2">Kepala Sekolah</p>
    <h1 class="g-headline">Selamat datang, {{ $user->name }}</h1>
    <p class="g-text-muted">Cari catatan seluruh guru dan unduh rekap semester.</p>
</header>
<div class="row g-4">
    <div class="col-md-6">
        <a href="{{ route('journals.search') }}" class="g-link-card g-card g-card--hover g-card__body">
            <h2 class="g-subhead mb-2"><i class="bi bi-search text-primary"></i> Cari Catatan</h2>
            <p class="g-text-muted g-text-small mb-0">Filter tanggal, kelas, dan guru.</p>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('recap.index') }}" class="g-link-card g-card g-card--hover g-card__body">
            <h2 class="g-subhead mb-2"><i class="bi bi-file-earmark-text text-primary"></i> Rekap Semester</h2>
            <p class="g-text-muted g-text-small mb-0">Lihat di layar dan unduh PDF.</p>
        </a>
    </div>
</div>
@endsection
