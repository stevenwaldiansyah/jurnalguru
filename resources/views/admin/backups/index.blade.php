@extends('layouts.app')
@section('title','Backup & Restore')
@section('content')
<header class="g-page-header">
    <h1 class="g-headline">Backup & Restore Database</h1>
    @if($lastSuccess)
        <p class="g-text-muted g-text-small mb-0">Backup terakhir berhasil: {{ $lastSuccess->back_time->format('d/m/Y H:i') }} — <code class="g-mono">{{ $lastSuccess->file_name }}</code></p>
    @else
        <p class="g-text-muted g-text-small mb-0">Belum ada backup berhasil tercatat.</p>
    @endif
</header>

<form method="POST" action="{{ route('admin.backups.store') }}" class="mb-4">@csrf<button class="btn btn-primary">Jalankan Backup Manual</button></form>

<h2 class="g-section-title">Pulihkan dari File</h2>
<form method="POST" action="{{ route('admin.backups.restore') }}" class="g-card mb-4" onsubmit="return confirm('Restore akan menimpa data database. Lanjutkan?')">@csrf
<div class="g-card__body row g-3 align-items-end">
<div class="col-md-6"><label class="form-label">File backup</label><select name="file_name" class="form-select" required><option value="">Pilih file backup</option>@foreach($files as $f)<option value="{{ $f }}">{{ $f }}</option>@endforeach</select></div>
<div class="col-md-6 form-check"><input class="form-check-input" type="checkbox" name="confirm" value="1" id="confirm"><label class="form-check-label" for="confirm">Saya mengerti data akan diganti</label></div>
<div class="col-12"><button class="btn btn-outline-danger">Restore</button></div>
</div></form>

<h2 class="g-section-title">Riwayat Backup</h2>
<div class="g-card g-table-wrap">
<table class="table"><thead><tr><th>Waktu</th><th>Status</th><th>File</th></tr></thead>
<tbody>@forelse($logs as $log)<tr><td>{{ $log->back_time->format('d/m/Y H:i') }}</td><td>@if($log->status==='berhasil')<span class="g-chip g-chip--success">Berhasil</span>@else<span class="g-chip" style="background:rgba(239,68,68,.12);color:#B91C1C;">Gagal</span>@endif</td><td class="g-mono g-text-small">{{ $log->file_name ?? '—' }}</td></tr>@empty<tr><td colspan="3"><div class="g-empty">Belum ada riwayat.</div></td></tr>@endforelse</tbody></table>
</div>
{{ $logs->links() }}
<div class="g-alert g-alert--info g-text-small mt-4 mb-0">Backup otomatis harian pukul 02:00.</div>
@endsection
