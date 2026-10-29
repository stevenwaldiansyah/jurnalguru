@extends('layouts.app')
@section('content')
<h1 class="h4 mb-3">{{ $student->exists?'Edit':'Tambah' }} Siswa</h1>
<form method="POST" action="{{ $student->exists?route('admin.students.update',$student):route('admin.students.store') }}" class="g-card"><div class="g-card__body">@csrf @if($student->exists)@method('PUT')@endif
<div class="mb-3"><label class="form-label">NIS</label><input name="nis" class="form-control" value="{{ old('nis',$student->nis) }}" required></div>
<div class="mb-3"><label class="form-label">Nama</label><input name="name" class="form-control" value="{{ old('name',$student->name) }}" required></div>
<div class="mb-3"><label class="form-label">Kelas</label><select name="class_id" class="form-select">@foreach($classes as $c)<option value="{{ $c->id }}" @selected(old('class_id',$student->class_id)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
<button class="btn btn-primary">Simpan</button></div></form>
@endsection
