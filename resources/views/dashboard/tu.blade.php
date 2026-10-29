@extends('layouts.app')

@section('title', 'Beranda Tata Usaha')

@section('content')
<header class="g-page-header">
    <p class="g-overline mb-2">Staf Tata Usaha</p>
    <h1 class="g-headline">Selamat datang, {{ $user->name }}</h1>
    <p class="g-text-muted">Akses baca untuk administrasi: pencarian catatan dan rekap semester.</p>
</header>
<div class="row g-4">
    <div class="col-md-6"><a href="{{ route('journals.search') }}" class="g-link-card g-card g-card--hover g-card__body"><h2 class="g-subhead">Cari Catatan</h2></a></div>
    <div class="col-md-6"><a href="{{ route('recap.index') }}" class="g-link-card g-card g-card--hover g-card__body"><h2 class="g-subhead">Rekap Semester</h2></a></div>
</div>
@endsection
