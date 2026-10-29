<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        h2 { font-size: 13px; margin: 16px 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        th { background: #eee; }
        .meta { margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>Rekap Catatan Mengajar Guru</h1>
    <div class="meta">
        <div>SMK Muhammadiyah 04 Boyolali — JurnalGuru</div>
        <div>Guru: {{ $teacher->name }} ({{ $teacher->username }})</div>
        <div>Semester: {{ $semester->displayLabel() }}</div>
        <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
    @foreach($schedules as $schedule)
        <h2>{{ $schedule->subject->name }} — {{ $schedule->schoolClass->name }} ({{ $schedule->day }})</h2>
        <table>
            <thead><tr><th>Tanggal</th><th>Materi</th><th>Catatan</th><th>H</th><th>S</th><th>I</th><th>A</th></tr></thead>
            <tbody>
            @forelse($journalsBySchedule[$schedule->id] ?? [] as $j)
                @php $c = $j->attendances->countBy(fn($a)=>$a->status->value); @endphp
                <tr>
                    <td>{{ $j->date->format('d/m/Y') }}</td>
                    <td>{{ $j->topic }}</td>
                    <td>{{ Str::limit($j->notes ?? '-', 80) }}</td>
                    <td>{{ $c['hadir']??0 }}</td>
                    <td>{{ $c['sakit']??0 }}</td>
                    <td>{{ $c['izin']??0 }}</td>
                    <td>{{ $c['alpa']??0 }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada data jurnal.</td></tr>
            @endforelse
            </tbody>
        </table>
    @endforeach
</body>
</html>
