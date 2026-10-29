@extends('layouts.app')
@section('title', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('content')
<h1 class="h4 mb-3">{{ $user->exists ? 'Edit' : 'Tambah' }} Pengguna</h1>
<form method="POST" action="{{ $user->exists ? route('admin.users.update',$user) : route('admin.users.store') }}" class="g-card"><div class="g-card__body">
@csrf @if($user->exists) @method('PUT') @endif
<div class="mb-3"><label class="form-label">Nama</label><input name="name" class="form-control" value="{{ old('name',$user->name) }}" required></div>
<div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control" value="{{ old('username',$user->username) }}" required></div>
<div class="mb-3"><label class="form-label">Peran</label><select name="role" class="form-select">@foreach($roles as $r)<option value="{{ $r->value }}" @selected(old('role',$user->role?->value)===$r->value)>{{ $r->label() }}</option>@endforeach</select></div>
<div class="mb-3"><label class="form-label">Kata sandi{{ $user->exists ? ' (kosongkan jika tidak diubah)' : '' }}</label><input type="password" name="password" class="form-control" {{ $user->exists ? '' : 'required' }}></div>
<div class="mb-3"><label class="form-label">Konfirmasi sandi</label><input type="password" name="password_confirmation" class="form-control"></div>
<button class="btn btn-primary">Simpan</button> <a href="{{ route('admin.users.index') }}" class="btn btn-link">Batal</a>
</div></form>
@endsection
