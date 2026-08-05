<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Blanko Daftar Hadir Siswa Bulanan</title>
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 8px; color: #000; margin: 0; }
        .header { text-align: center; font-weight: bold; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
        .header h3 { font-size: 11px; margin: 2px 0; text-transform: uppercase; }
        .header h4 { font-size: 10px; margin: 2px 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 3px 1px; text-align: center; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-size: 7.5px; }
        .text-left { text-align: left; padding-left: 3px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>DINAS PENDIDIKAN PROVINSI KALIMANTAN BARAT</h3>
        <h3>SMK NEGERI 7 PONTIANAK</h3>
        <h4>DAFTAR HADIR SISWA KELAS {{ strtoupper($class ?? '...') }} - BULAN {{ strtoupper($monthName) }}</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">NO</th>
                <th rowspan="2" style="width: 120px;">NAMA SISWA</th>
                <th colspan="{{ count($effectiveDates) }}">TANGGAL (HARI EFEKTIF SENIN - JUMAT)</th>
                <th colspan="3" style="width: 45px;">JUMLAH</th>
            </tr>
            <tr>
                @foreach($effectiveDates as $d)
                    <th>{{ $d }}</th>
                @endforeach
                <th style="width: 15px;">S</th>
                <th style="width: 15px;">I</th>
                <th style="width: 15px;">A</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $student->name }}</td>
                    @foreach($effectiveDates as $d) <td></td> @endforeach
                    <td></td><td></td><td></td>
                </tr>
            @empty
                <tr><td colspan="{{ count($effectiveDates) + 5 }}">Data siswa tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>