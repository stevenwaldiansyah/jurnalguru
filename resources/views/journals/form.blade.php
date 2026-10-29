@extends('layouts.app')

@section('title', $journal ? 'Ubah Catatan' : 'Isi Catatan')

@section('content')
<div class="g-toolbar mb-2">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h1 class="g-headline mb-0">{{ $journal ? 'Ubah Catatan & Absensi' : 'Isi Catatan & Absensi' }}</h1>
</div>

<div class="g-card mb-4"><div class="g-card__body">
    <div class="row g-3 g-text-small">
        <div class="col-md-3"><span class="g-overline d-block">Tanggal</span>{{ $date->locale('id')->translatedFormat('l, d M Y') }}</div>
        <div class="col-md-3"><span class="g-overline d-block">Kelas</span>{{ $schedule->schoolClass->name }}</div>
        <div class="col-md-3"><span class="g-overline d-block">Mapel</span>{{ $schedule->subject->name }}</div>
        <div class="col-md-3"><span class="g-overline d-block">Jam</span><span class="g-mono">{{ $schedule->timeRangeLabel() }}</span></div>
    </div>
</div></div>

<form method="POST" action="{{ $journal ? route('journals.update', $journal) : route('journals.store', $schedule) }}">
    @csrf
    @if($journal) @method('PUT') @else<input type="hidden" name="date" value="{{ $date->toDateString() }}">@endif

    <div class="g-card mb-4"><div class="g-card__body">
        <label class="form-label">Materi / Topik Pembelajaran <span class="text-danger">*</span></label>
        <input type="text" name="topic" class="form-control @error('topic') is-invalid @enderror" value="{{ old('topic', $journal?->topic) }}" required>
        <label class="form-label mt-3">Catatan / Kendala Mengajar</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $journal?->notes) }}</textarea>
    </div></div>

    <div class="g-card mb-4">
        <div class="g-card__header">Data Absensi Siswa</div>
        <div class="g-table-wrap">
            <table class="table"><thead><tr><th>#</th><th>Nama Siswa</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($students as $i => $student)
                @php $current = old('attendance.'.$student->id, $journal?->attendances->firstWhere('student_id', $student->id)?->status->value ?? 'hadir'); @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $student->name }} <span class="g-text-caption">({{ $student->nis }})</span></td>
                    <td><select name="attendance[{{ $student->id }}]" class="form-select form-select-sm attendance-select">@foreach($attendanceStatuses as $status)<option value="{{ $status->value }}" @selected($current === $status->value)>{{ $status->label() }}</option>@endforeach</select></td>
                </tr>
            @endforeach
            </tbody></table>
        </div>
        <div class="g-card__footer"><span id="attendance-summary" class="g-text-small g-text-muted"></span></div>
    </div>

    <div class="g-card mb-4">
        <div class="g-card__header">Nilai Harian (Opsional)</div>
        <div class="g-card__body" id="scores-wrap">
            @php $oldScores = old('scores', $journal?->scores->map(fn($s)=>['student_id'=>$s->student_id,'type'=>$s->type,'score'=>$s->score])->values()->all() ?? [['student_id'=>'','type'=>'Tugas','score'=>'']]); @endphp
            @foreach($oldScores as $idx => $row)
            <div class="row g-2 mb-2 score-row">
                <div class="col-md-5"><select name="scores[{{ $idx }}][student_id]" class="form-select form-select-sm"><option value="">— Pilih siswa —</option>@foreach($students as $student)<option value="{{ $student->id }}" @selected((string)$row['student_id'] === (string)$student->id)>{{ $student->name }}</option>@endforeach</select></div>
                <div class="col-md-3"><input type="text" name="scores[{{ $idx }}][type]" class="form-control form-control-sm" value="{{ $row['type'] ?? 'Tugas' }}" placeholder="Jenis"></div>
                <div class="col-md-3"><input type="number" step="0.01" min="0" max="100" name="scores[{{ $idx }}][score]" class="form-control form-control-sm" value="{{ $row['score'] ?? '' }}" placeholder="0-100"></div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Catatan</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function updateSummary() {
    const counts = { hadir: 0, sakit: 0, izin: 0, alpa: 0 };
    document.querySelectorAll('.attendance-select').forEach(el => { if (counts[el.value] !== undefined) counts[el.value]++; });
    document.getElementById('attendance-summary').textContent =
        `Ringkasan: Hadir ${counts.hadir} | Sakit ${counts.sakit} | Izin ${counts.izin} | Alpa ${counts.alpa}`;
}
document.querySelectorAll('.attendance-select').forEach(el => el.addEventListener('change', updateSummary));
updateSummary();
</script>
@endpush
