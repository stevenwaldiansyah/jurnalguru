@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="g-auth-page">
    <div class="g-auth-card">
        <div class="g-card">
            <div class="g-card__body">
                <div class="g-auth-brand">
                    <div class="g-nav__logo"><i class="bi bi-journal-bookmark-fill"></i></div>
                    <h1 class="g-headline" style="font-size:1.5rem;">JurnalGuru</h1>
                    <p class="g-text-muted g-text-small mb-1">Sistem Catatan Mengajar Guru</p>
                    <p class="g-overline mb-0">SMK Muhammadiyah 04 Boyolali</p>
                </div>
                @include('layouts.flash')
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus autocomplete="username">
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="min-height:44px;">Masuk</button>
                </form>
                <p class="text-center g-text-caption mt-4 mb-0">Lupa sandi? Hubungi Admin</p>
            </div>
        </div>
    </div>
</div>
@endsection
