@extends('layouts.app')
@section('content')
<h1 class="h4 mb-3">{{ $class->exists?'Edit':'Tambah' }} Kelas</h1>
<form method="POST" action="{{ $class->exists?route('admin.classes.update',$class):route('admin.classes.store') }}" class="g-card"><div class="g-card__body">@csrf @if($class->exists)@method('PUT')@endif
<div class="mb-3"><label class="form-label">Nama Kelas</label><input name="name" class="form-control" value="{{ old('name',$class->name) }}" required></div>
<div class="mb-3"><label class="form-label">Jurusan</label><input name="major" class="form-control" value="{{ old('major',$class->major) }}" required></div>
<button class="btn btn-primary">Simpan</button></div></form>
@endsection
