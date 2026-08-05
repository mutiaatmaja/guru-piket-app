<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Blanko Nilai Siswa</title>
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 8px; color: #000; margin: 0; }
        .header { text-align: center; font-weight: bold; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
        .header h3 { font-size: 11px; margin: 2px 0; text-transform: uppercase; }
        .header h4 { font-size: 10px; margin: 2px 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 3px 1px; text-align: center; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-size: 7px; }
        .text-left { text-align: left; padding-left: 3px; }
        .ttd-box { float: right; text-align: center; width: 200px; margin-top: 15px; font-size: 9px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>DINAS PENDIDIKAN PROVINSI KALIMANTAN BARAT</h3>
        <h3>SMK NEGERI 7 PONTIANAK</h3>
        <h4>DAFTAR NILAI MATA PELAJARAN {{ strtoupper($subject) }} SISWA KELAS {{ strtoupper($class ?? '...') }}</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 18px;">NO</th>
                <th rowspan="2" style="width: 110px;">NAMA SISWA</th>
                <th colspan="8">ULANGAN HARIAN (UH)</th>
                <th rowspan="2" style="width: 24px;">RATA UH</th>
                <th colspan="8">TUGAS (T)</th>
                <th rowspan="2" style="width: 24px;">RATA TUGAS</th>
                <th rowspan="2" style="width: 26px;">NILAI AKHIR</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 8; $i++) <th style="width: 15px;">UH{{ $i }}</th> @endfor
                @for($i = 1; $i <= 8; $i++) <th style="width: 15px;">T{{ $i }}</th> @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $student->name }}</td>
                    @for($i = 1; $i <= 8; $i++) <td></td> @endfor
                    <td></td>
                    @for($i = 1; $i <= 8; $i++) <td></td> @endfor
                    <td></td><td></td>
                </tr>
            @empty
                <tr><td colspan="21">Data siswa tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="width: 100%; overflow: hidden;">
        <div class="ttd-box">
            <p>Pontianak, {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('D MMMM YYYY') }}</p>
            <p>Guru Mata Pelajaran,</p>
            <br><br><br>
            <p><strong><u>{{ $teacherName }}</u></strong></p>
            <p>NIP. {{ $teacherNip }}</p>
        </div>
    </div>
</body>
</html>