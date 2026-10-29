@extends('layouts.app')
@section('content')
<form method="POST" action="{{ $subject->exists?route('admin.subjects.update',$subject):route('admin.subjects.store') }}" class="g-card"><div class="g-card__body">@csrf @if($subject->exists)@method('PUT')@endif
<label class="form-label">Nama Mapel</label><input name="name" class="form-control mb-3" value="{{ old('name',$subject->name) }}" required>
<button class="btn btn-primary">Simpan</button></div></form>
@endsection
