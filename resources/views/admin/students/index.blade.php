@extends('layouts.app')
@section('content')
@include('admin._toolbar', ['title' => 'Siswa', 'createRoute' => route('admin.students.create')])
<div class="g-card g-table-wrap"><table class="table"><thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th></th></tr></thead>
<tbody>@foreach($students as $s)<tr><td>{{ $s->nis }}</td><td>{{ $s->name }}</td><td>{{ $s->schoolClass->name }}</td><td class="text-end"><a href="{{ route('admin.students.edit',$s) }}" class="btn btn-sm btn-outline-primary">Edit</a>
<form class="d-inline" method="POST" action="{{ route('admin.students.destroy',$s) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@endforeach</tbody></table></div>
{{ $students->links() }}
@endsection
