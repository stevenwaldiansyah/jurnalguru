@extends('layouts.app')
@section('content')
@include('admin._toolbar', ['title' => 'Jadwal Mengajar', 'createRoute' => route('admin.schedules.create')])
<div class="g-card g-table-wrap"><table class="table g-text-small"><thead><tr><th>Guru</th><th>Hari</th><th>Jam</th><th>Kelas</th><th>Mapel</th><th>Semester</th><th></th></tr></thead>
<tbody>@foreach($schedules as $s)<tr><td>{{ $s->teacher->name }}</td><td>{{ $s->day }}</td><td class="g-mono">{{ $s->timeRangeLabel() }}</td><td>{{ $s->schoolClass->name }}</td><td>{{ $s->subject->name }}</td><td>{{ $s->semester->displayLabel() }}</td><td class="text-end"><a href="{{ route('admin.schedules.edit',$s) }}" class="btn btn-sm btn-outline-primary">Edit</a>
<form class="d-inline" method="POST" action="{{ route('admin.schedules.destroy',$s) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@endforeach</tbody></table></div>
{{ $schedules->links() }}
@endsection
