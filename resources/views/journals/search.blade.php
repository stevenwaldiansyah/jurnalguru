@extends('layouts.app')

@section('title', 'Cari Catatan')

@section('content')
<header class="g-page-header">
    <h1 class="g-headline">Cari Catatan Mengajar</h1>
</header>

<form class="g-card mb-4" method="GET">
    <div class="g-card__body row g-3">
        <div class="col-md-3"><label class="form-label">Dari tanggal</label><input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}"></div>
        <div class="col-md-3"><label class="form-label">Sampai tanggal</label><input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}"></div>
        <div class="col-md-3"><label class="form-label">Kelas</label><select name="class_id" class="form-select"><option value="">Semua</option>@foreach($classes as $c)<option value="{{ $c->id }}" @selected(($filters['class_id'] ?? '') == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">Semester</label><select name="semester_id" class="form-select"><option value="">Semua</option>@foreach($semesters as $s)<option value="{{ $s->id }}" @selected(($filters['semester_id'] ?? '') == $s->id)>{{ $s->displayLabel() }}</option>@endforeach</select></div>
        @if($teachers->isNotEmpty())
        <div class="col-md-3"><label class="form-label">Guru</label><select name="teacher_id" class="form-select"><option value="">Semua</option>@foreach($teachers as $t)<option value="{{ $t->id }}" @selected(($filters['teacher_id'] ?? '') == $t->id)>{{ $t->name }}</option>@endforeach</select></div>
        @endif
        <div class="col-12"><button class="btn btn-primary">Terapkan Filter</button></div>
    </div>
</form>

<div class="g-card g-table-wrap">
    <table class="table">
        <thead><tr><th>Tanggal</th><th>Guru</th><th>Kelas</th><th>Mapel</th><th>Materi</th><th></th></tr></thead>
        <tbody>
        @forelse($journals as $journal)
            <tr>
                <td>{{ $journal->date->format('d/m/Y') }}</td>
                <td>{{ $journal->schedule->teacher->name }}</td>
                <td>{{ $journal->schedule->schoolClass->name }}</td>
                <td>{{ $journal->schedule->subject->name }}</td>
                <td>{{ Str::limit($journal->topic, 40) }}</td>
                <td class="text-end"><a href="{{ route('journals.show', $journal) }}" class="btn btn-outline-primary btn-sm">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="6"><div class="g-empty">Tidak ada catatan ditemukan.</div></td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $journals->links() }}</div>
@endsection
