@extends('layouts.app')
@section('content')
<form method="POST" action="{{ $schedule->exists?route('admin.schedules.update',$schedule):route('admin.schedules.store') }}" class="g-card"><div class="g-card__body row g-2">@csrf @if($schedule->exists)@method('PUT')@endif
<div class="col-md-6"><label class="form-label">Guru</label><select name="teacher_id" class="form-select">@foreach($teachers as $t)<option value="{{ $t->id }}" @selected(old('teacher_id',$schedule->teacher_id)==$t->id)>{{ $t->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Kelas</label><select name="class_id" class="form-select">@foreach($classes as $c)<option value="{{ $c->id }}" @selected(old('class_id',$schedule->class_id)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Mapel</label><select name="subject_id" class="form-select">@foreach($subjects as $sub)<option value="{{ $sub->id }}" @selected(old('subject_id',$schedule->subject_id)==$sub->id)>{{ $sub->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Semester</label><select name="semester_id" class="form-select">@foreach($semesters as $sem)<option value="{{ $sem->id }}" @selected(old('semester_id',$schedule->semester_id)==$sem->id)>{{ $sem->displayLabel() }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Hari</label><select name="day" class="form-select">@foreach($days as $d)<option value="{{ $d }}" @selected(old('day',$schedule->day)===$d)>{{ $d }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Mulai</label><input type="time" name="start_time" class="form-control" value="{{ old('start_time', substr((string)$schedule->start_time,0,5)) }}" required></div>
<div class="col-md-4"><label class="form-label">Selesai</label><input type="time" name="end_time" class="form-control" value="{{ old('end_time', substr((string)$schedule->end_time,0,5)) }}" required></div>
<div class="col-12"><button class="btn btn-primary">Simpan</button></div>
</div></form>
@endsection
