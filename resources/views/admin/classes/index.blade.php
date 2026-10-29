@extends('layouts.app')
@section('title','Kelas')
@section('content')
@include('admin._toolbar', ['title' => 'Kelas', 'createRoute' => route('admin.classes.create')])
<div class="g-card g-table-wrap"><table class="table"><thead><tr><th>Nama</th><th>Jurusan</th><th>Siswa</th><th></th></tr></thead>
<tbody>@foreach($classes as $c)<tr><td>{{ $c->name }}</td><td>{{ $c->major }}</td><td>{{ $c->students_count }}</td><td class="text-end"><a href="{{ route('admin.classes.edit',$c) }}" class="btn btn-sm btn-outline-primary">Edit</a>
<form class="d-inline" method="POST" action="{{ route('admin.classes.destroy',$c) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@endforeach</tbody></table></div>
{{ $classes->links() }}
@endsection
