@extends('layouts.app')
@section('content')
@include('admin._toolbar', ['title' => 'Semester', 'createRoute' => route('admin.semesters.create')])
<div class="g-card g-table-wrap"><table class="table"><thead><tr><th>Semester</th><th>Tahun Ajaran</th><th></th></tr></thead>
<tbody>@foreach($semesters as $s)<tr><td>{{ $s->name }}</td><td>{{ $s->academic_year }}</td><td class="text-end"><a href="{{ route('admin.semesters.edit',$s) }}" class="btn btn-sm btn-outline-primary">Edit</a>
<form class="d-inline" method="POST" action="{{ route('admin.semesters.destroy',$s) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@endforeach</tbody></table></div>
{{ $semesters->links() }}
@endsection
