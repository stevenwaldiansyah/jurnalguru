@extends('layouts.app')
@section('content')
@include('admin._toolbar', ['title' => 'Mata Pelajaran', 'createRoute' => route('admin.subjects.create')])
<div class="g-card g-table-wrap"><table class="table"><thead><tr><th>Nama</th><th></th></tr></thead>
<tbody>@foreach($subjects as $s)<tr><td>{{ $s->name }}</td><td class="text-end"><a href="{{ route('admin.subjects.edit',$s) }}" class="btn btn-sm btn-outline-primary">Edit</a>
<form class="d-inline" method="POST" action="{{ route('admin.subjects.destroy',$s) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@endforeach</tbody></table></div>
{{ $subjects->links() }}
@endsection
