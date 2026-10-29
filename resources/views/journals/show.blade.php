@extends('layouts.app')

@section('title', 'Detail Jurnal')

@section('content')
<div class="g-toolbar">
    <div>
        <h1 class="g-headline mb-1">{{ $journal->schedule->subject->name }}</h1>
        <p class="g-text-muted g-text-small mb-0">{{ $journal->date->locale('id')->translatedFormat('l, d F Y') }} · {{ $journal->schedule->schoolClass->name }} · <span class="g-mono">{{ $journal->schedule->timeRangeLabel() }}</span></p>
    </div>
    @can('update', $journal)
        <div class="d-flex gap-2">
            <a href="{{ route('journals.edit', $journal) }}" class="btn btn-outline-primary btn-sm">Ubah</a>
            <form method="POST" action="{{ route('journals.destroy', $journal) }}" onsubmit="return confirm('Hapus catatan ini?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Hapus</button></form>
        </div>
    @endcan
</div>

<div class="g-card mb-4"><div class="g-card__body">
    <p class="g-overline mb-2">Materi</p>
    <p class="mb-4">{{ $journal->topic }}</p>
    <p class="g-overline mb-2">Catatan</p>
    <p class="mb-0 g-text-muted">{{ $journal->notes ?: '—' }}</p>
</div></div>

@php $summary = $journal->attendances->countBy(fn($a) => $a->status->value); @endphp
<div class="g-card mb-4">
    <div class="g-card__header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span>Absensi</span>
        <span class="g-text-small g-text-muted">H: {{ $summary['hadir'] ?? 0 }} · S: {{ $summary['sakit'] ?? 0 }} · I: {{ $summary['izin'] ?? 0 }} · A: {{ $summary['alpa'] ?? 0 }}</span>
    </div>
    <div class="g-table-wrap">
        <table class="table"><thead><tr><th>Siswa</th><th>Status</th></tr></thead>
        <tbody>@foreach($journal->attendances->sortBy(fn($a)=>$a->student->name) as $a)<tr><td>{{ $a->student->name }}</td><td><span class="g-chip g-chip--neutral">{{ $a->status->label() }}</span></td></tr>@endforeach</tbody></table>
    </div>
</div>

@if($journal->scores->isNotEmpty())
<div class="g-card">
    <div class="g-card__header">Nilai Harian</div>
    <div class="g-table-wrap">
        <table class="table"><thead><tr><th>Siswa</th><th>Jenis</th><th>Nilai</th></tr></thead>
        <tbody>@foreach($journal->scores as $s)<tr><td>{{ $s->student->name }}</td><td>{{ $s->type }}</td><td class="g-mono">{{ $s->score }}</td></tr>@endforeach</tbody></table>
    </div>
</div>
@endif
@endsection
