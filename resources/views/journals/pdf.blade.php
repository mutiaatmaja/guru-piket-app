<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jurnal Mengajar Guru</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11pt; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; text-transform: uppercase; font-size: 14pt; }
        .header p { margin: 5px 0 0 0; font-size: 10pt; color: #444; }
        .info { margin-bottom: 15px; font-size: 10pt; }
        .info table { width: 100%; border: none; }
        .info td { padding: 3px 0; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #333; padding: 8px; text-align: left; }
        table.data th { background-color: #f2f2f2; font-size: 10pt; text-align: center; }
        table.data td { font-size: 9.5pt; }
        .text-center { text-align: center; }
        
        .signature-container {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
            font-size: 10pt;
        }
        .signature-space {
            height: 70px;
        }
        .clear {
            clear: both;
        }

        @media print {
            @page { size: A4 portrait; margin: 15mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>LAPORAN JURNAL MENGAJAR GURU</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="18%"><strong>Nama Guru</strong></td>
                <td width="2%">:</td>
                <td>{{ $teacher->name ?? $teacher->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>NIP / NUPTK</strong></td>
                <td>:</td>
                <td>{{ $teacher->nip ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="10%">Kelas</th>
                <th width="20%">Mapel</th>
                <th width="10%">Jam Ke</th>
                <th width="25%">Materi / Uraian KBM</th>
                <th width="18%">Hambatan / Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($journals as $index => $journal)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($journal->date)->translatedFormat('d/m/Y') }}</td>
                    <td class="text-center">{{ $journal->schedule->class_name ?? '-' }}</td>
                    <td>{{ $journal->schedule->subject->name ?? $journal->schedule->subject->nama_mapel ?? '-' }}</td>
                    <td class="text-center">{{ $journal->formatted_jam_ke }}</td>
                    <td>{{ $journal->material ?? '-' }}</td>
                    <td>{{ $journal->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data jurnal mengajar pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN GURU --}}
    <div class="signature-container">
        <div class="signature-box">
            <p>Pontianak, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Guru Mata Pelajaran,</p>
            <div class="signature-space"></div>
            <p><strong><u>{{ $teacher->name ?? $teacher->nama ?? '-' }}</u></strong><br>
            NIP. {{ $teacher->nip ?? '............................................' }}</p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>