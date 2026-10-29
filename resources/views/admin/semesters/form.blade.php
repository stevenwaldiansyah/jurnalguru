@extends('layouts.app')
@section('content')
<form method="POST" action="{{ $semester->exists?route('admin.semesters.update',$semester):route('admin.semesters.store') }}" class="g-card"><div class="g-card__body">@csrf @if($semester->exists)@method('PUT')@endif
<div class="mb-3"><label class="form-label">Nama Semester</label><input name="name" class="form-control" value="{{ old('name',$semester->name) }}" required></div>
<div class="mb-3"><label class="form-label">Tahun Ajaran</label><input name="academic_year" class="form-control" value="{{ old('academic_year',$semester->academic_year) }}" required></div>
<button class="btn btn-primary">Simpan</button></div></form>
@endsection
