@extends('layouts.app')

@section('title', 'Rekap Semester')

@section('content')
<header class="g-page-header">
    <h1 class="g-headline">Rekap Semester</h1>
</header>

<form method="GET" class="g-card mb-4"><div class="g-card__body row g-3 align-items-end">
    <div class="col-md-4"><label class="form-label">Semester</label><select name="semester_id" class="form-select" required>@foreach($semesters as $s)<option value="{{ $s->id }}" @selected($semesterId == $s->id)>{{ $s->displayLabel() }}</option>@endforeach</select></div>
    @if($teachers->isNotEmpty())
    <div class="col-md-4"><label class="form-label">Guru</label><select name="teacher_id" class="form-select" required>@foreach($teachers as $t)<option value="{{ $t->id }}" @selected($teacher && $teacher->id == $t->id)>{{ $t->name }}</option>@endforeach</select></div>
    @endif
    <div class="col-md-4 d-flex flex-wrap gap-2">
        <button class="btn btn-primary">Tampilkan</button>
        @if($teacher && $semesterId)
            <a class="btn btn-outline-primary" href="{{ route('recap.pdf', ['semester_id' => $semesterId, 'teacher_id' => $teacher->id]) }}">Unduh PDF</a>
        @endif
    </div>
</div></form>

@if(!$teacher)
    <div class="g-alert g-alert--info">Pilih semester{{ auth()->user()->canViewAllJournals() ? ' dan guru' : '' }} untuk menampilkan rekap.</div>
@elseif($schedules->isEmpty())
    <div class="g-card"><div class="g-empty">Tidak ada jadwal untuk filter ini.</div></div>
@else
    <p class="g-text-muted mb-4">Guru: <strong>{{ $teacher->name }}</strong></p>
    @foreach($schedules as $schedule)
        <div class="g-card mb-4">
            <div class="g-card__header">{{ $schedule->subject->name }} — {{ $schedule->schoolClass->name }} ({{ $schedule->day }}, {{ $schedule->timeRangeLabel() }})</div>
            <div class="g-table-wrap">
                <table class="table g-text-small">
                    <thead><tr><th>Tanggal</th><th>Materi</th><th>Absensi (H/S/I/A)</th><th>Nilai</th></tr></thead>
                    <tbody>
                    @forelse($journalsBySchedule[$schedule->id] ?? [] as $j)
                        @php $c = $j->attendances->countBy(fn($a)=>$a->status->value); @endphp
                        <tr>
                            <td>{{ $j->date->format('d/m/Y') }}</td>
                            <td>{{ Str::limit($j->topic, 50) }}</td>
                            <td class="g-mono">{{ $c['hadir']??0 }}/{{ $c['sakit']??0 }}/{{ $c['izin']??0 }}/{{ $c['alpa']??0 }}</td>
                            <td>{{ $j->scores->count() }} entri</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="g-text-muted">Belum ada jurnal.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endif
@endsection
