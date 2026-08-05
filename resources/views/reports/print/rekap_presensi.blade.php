<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran Siswa</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; }
        .header { text-align: center; font-weight: bold; border-bottom: 2px solid #000; padding-bottom: 6px; margin-bottom: 12px; }
        .header h3 { font-size: 12px; margin: 2px 0; text-transform: uppercase; }
        .header h4 { font-size: 11px; margin: 2px 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #000; padding: 6px 4px; text-align: center; }
        th { background-color: #f2f2f2; font-size: 10px; }
        .text-left { text-align: left; padding-left: 6px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>DINAS PENDIDIKAN PROVINSI KALIMANTAN BARAT</h3>
        <h3>SMK NEGERI 7 PONTIANAK</h3>
        <h4>REKAP KEHADIRAN SISWA KELAS {{ strtoupper($class ?? '...') }}</h4>
        <h4>{{ $periodLabel }}</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th>NAMA SISWA</th>
                <th style="width: 70px;">SAKIT (S)</th>
                <th style="width: 70px;">IZIN (I)</th>
                <th style="width: 70px;">ALPA (A)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                @php
                    $userLogs = $attendances->get($student->id) ?? collect();
                    $sakit = $userLogs->where('status', 'sakit')->count();
                    $izin  = $userLogs->where('status', 'izin')->count();
                    $alpa  = $userLogs->where('status', 'alpa')->count();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $student->name }}</td>
                    <td>{{ $sakit > 0 ? $sakit : '-' }}</td>
                    <td>{{ $izin > 0 ? $izin : '-' }}</td>
                    <td>{{ $alpa > 0 ? $alpa : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Data siswa tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>