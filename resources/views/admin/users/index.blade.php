@extends('layouts.app')
@section('title','Pengguna')
@section('content')
@include('admin._toolbar', ['title' => 'Pengguna', 'createRoute' => route('admin.users.create')])
<div class="g-card g-table-wrap"><table class="table"><thead><tr><th>Nama</th><th>Username</th><th>Peran</th><th></th></tr></thead>
<tbody>@foreach($users as $u)<tr><td>{{ $u->name }}</td><td class="g-mono">{{ $u->username }}</td><td><span class="g-chip g-chip--neutral">{{ $u->role->label() }}</span></td><td class="text-end"><a href="{{ route('admin.users.edit',$u) }}" class="btn btn-sm btn-outline-primary">Edit</a>
<form class="d-inline" method="POST" action="{{ route('admin.users.destroy',$u) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@endforeach</tbody></table></div>
{{ $users->links() }}
@endsection
