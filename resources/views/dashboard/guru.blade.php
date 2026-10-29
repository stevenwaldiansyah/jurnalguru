@extends('layouts.app')

@section('title', 'Beranda Guru')

@section('content')
<header class="g-page-header">
    <p class="g-overline mb-2">Beranda Guru</p>
    <h1 class="g-headline">Halo, {{ $user->name }}</h1>
    <p class="g-text-muted mb-0">{{ $today }}</p>
</header>

<div class="g-stat-grid">
    <div class="g-card g-stat">
        <div class="g-stat__label">Jurnal terisi hari ini</div>
        <div class="g-stat__value g-stat__value--success">{{ $filledCount }}</div>
    </div>
    <div class="g-card g-stat">
        <div class="g-stat__label">Belum diisi hari ini</div>
        <div class="g-stat__value g-stat__value--warning">{{ $pendingToday }}</div>
    </div>
    <div class="g-card g-stat">
        <div class="g-stat__label">Tunggakan ≤ 7 hari</div>
        <div class="g-stat__value g-stat__value--error">{{ $reminders->count() }}</div>
    </div>
</div>

@if($reminders->isNotEmpty())
<div class="g-alert g-alert--warning mb-4">
    <strong><i class="bi bi-exclamation-triangle"></i> Perhatian:</strong> Anda memiliki jadwal mengajar yang belum diisi catatannya.
</div>
@endif

<h2 class="g-section-title">Jadwal Mengajar Hari Ini</h2>
@if($schedules->isEmpty())
    <div class="g-card"><div class="g-empty">Tidak ada jadwal mengajar hari ini.</div></div>
@else
<div class="g-card g-table-wrap mb-4">
    <table class="table">
        <thead><tr><th>Jam</th><th>Kelas</th><th>Mata Pelajaran</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach($schedules as $schedule)
            @php $filled = $journalsToday->has($schedule->id); @endphp
            <tr>
                <td><span class="g-mono">{{ $schedule->timeRangeLabel() }}</span></td>
                <td>{{ $schedule->schoolClass->name }}</td>
                <td>{{ $schedule->subject->name }}</td>
                <td>@if($filled)<span class="g-chip g-chip--success">Terisi</span>@else<span class="g-chip g-chip--neutral">Belum Diisi</span>@endif</td>
                <td class="text-end">
                    @if($filled)
                        <a href="{{ route('journals.show', $journalsToday[$schedule->id]) }}" class="btn btn-outline-primary btn-sm">Lihat</a>
                    @else
                        <a href="{{ route('journals.create', $schedule) }}" class="btn btn-primary btn-sm">Isi Catatan</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if($reminders->isNotEmpty())
<h2 class="g-section-title">Belum Diisi (≤ 7 Hari)</h2>
<div class="g-card">
    <ul class="g-list">
    @foreach($reminders as $item)
        <li class="g-list__item">
            <span>{{ $item['date']->locale('id')->translatedFormat('D, d M Y') }} · <strong>{{ $item['schedule']->schoolClass->name }}</strong> ({{ $item['schedule']->subject->name }})</span>
            <a href="{{ route('journals.create', ['schedule' => $item['schedule'], 'date' => $item['date']->toDateString()]) }}" class="btn btn-outline-primary btn-sm">Isi</a>
        </li>
    @endforeach
    </ul>
</div>
@endif
@endsection
