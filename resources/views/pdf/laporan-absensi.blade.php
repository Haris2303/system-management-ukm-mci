<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitle { color: #6b7280; margin-bottom: 18px; }
        h2 { font-size: 13px; margin-top: 22px; margin-bottom: 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 5px 8px; border: 1px solid #e5e7eb; text-align: left; }
        th { background-color: #f3f4f6; }
        .text-center { text-align: center; }
        .badge-hadir { color: #15803d; font-weight: bold; }
        .badge-izin { color: #b45309; font-weight: bold; }
        .badge-absen { color: #b91c1c; font-weight: bold; }
        .empty { color: #6b7280; font-style: italic; }
    </style>
</head>
<body>
    <h1>Laporan Absensi</h1>
    <div class="subtitle">Dicetak pada {{ $periode }}</div>

    <h2>Rekap Kehadiran Anggota</h2>
    @if ($anggota->isEmpty())
        <p class="empty">Belum ada data anggota.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Nama Anggota</th>
                    <th>Divisi</th>
                    <th class="text-center">Hadir</th>
                    <th class="text-center">Izin</th>
                    <th class="text-center">Absen</th>
                    <th class="text-center">Total Agenda</th>
                    <th class="text-center">% Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($anggota as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->divisi?->nama ?? '—' }}</td>
                        <td class="text-center badge-hadir">{{ $item->hadir_count }}</td>
                        <td class="text-center badge-izin">{{ $item->izin_count }}</td>
                        <td class="text-center badge-absen">{{ $item->absen_count }}</td>
                        <td class="text-center">{{ $item->total_presensi }}</td>
                        <td class="text-center">
                            {{ $item->total_presensi === 0 ? '—' : round(($item->hadir_count / $item->total_presensi) * 100) . '%' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
