<x-app-layout>
<div style="max-width: 1100px; margin: 30px auto; padding: 0 15px;">

    @php
        $user = auth()->user();
        // Pengecekan role Admin atau Waka
        $isAdminOrWaka = $user && (
            in_array(strtolower($user->role ?? ''), ['admin', 'waka', 'administrator', 'waka kurikulum']) ||
            (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'waka', 'Administrator', 'Waka Kurikulum']))
        );

        // Penentuan daftar kelas & mata pelajaran berdasarkan role
        $classList   = $isAdminOrWaka ? ($classes ?? []) : ($teacherClasses ?? []);
        $subjectList = $isAdminOrWaka ? ($subjects ?? $teacherSubjects ?? []) : ($teacherSubjects ?? []);
        
        // Map relasi Kelas => Mata Pelajaran (jika dikirim dari controller sebagai $teacherClassSubjects / $classSubjects)
        $classSubjectMap = $isAdminOrWaka ? ($classSubjects ?? []) : ($teacherClassSubjects ?? []);
    @endphp

    <!-- NAVIGASI TAB TIPE LAPORAN -->
    <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="{{ route('reports.index', array_merge(request()->query(), ['type' => 'kbm'])) }}" 
           style="padding: 10px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; {{ $type === 'kbm' ? 'background: #2563eb; color: #fff;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' }}">
            📊 Presensi KBM Kelas
        </a>
        <a href="{{ route('reports.index', array_merge(request()->query(), ['type' => 'jurnal'])) }}" 
           style="padding: 10px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; {{ $type === 'jurnal' ? 'background: #2563eb; color: #fff;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' }}">
            📖 Jurnal Mengajar Guru
        </a>
        <a href="{{ route('reports.index', array_merge(request()->query(), ['type' => 'siswa'])) }}" 
           style="padding: 10px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; {{ $type === 'siswa' ? 'background: #2563eb; color: #fff;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' }}">
            👨‍🎓 Catatan Piket Siswa
        </a>
    </div>

    <!-- BARIS AKSI (FILTER, EXPORT EXCEL, CETAK) -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <!-- Form Filter Data -->
        <form method="GET" action="{{ route('reports.index') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="hidden" name="type" value="{{ $type }}">
            
            <input type="date" name="start_date" value="{{ $startDate }}" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 0.85rem;">
            <span style="color: #64748b;">s.d.</span>
            <input type="date" name="end_date" value="{{ $endDate }}" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 0.85rem;">
            
            <!-- Select Filter Utama -->
            <select name="class_name" 
                    style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 0.85rem; {{ (!$isAdminOrWaka && empty($classList)) ? 'background-color: #f1f5f9; cursor: not-allowed;' : '' }}"
                    {{ (!$isAdminOrWaka && empty($classList)) ? 'disabled' : '' }}>
                @if($isAdminOrWaka)
                    <option value="">-- Semua Kelas --</option>
                @elseif(empty($classList))
                    <option value="">-- Tidak Ada Akses Kelas --</option>
                @else
                    <option value="">-- Semua Kelas Diajar --</option>
                @endif

                @foreach($classList as $c)
                    <option value="{{ $c }}" {{ $selectedClass == $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>

            <button type="submit" style="background-color: #2563eb; color: #ffffff; padding: 8px 16px; border-radius: 8px; border: none; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter Data
            </button>
        </form>

        <!-- Tombol Export Excel & Cetak -->
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('reports.export', ['start_date' => $startDate, 'end_date' => $endDate, 'type' => $type, 'status' => $status ?? '', 'class_name' => $selectedClass]) }}" 
               style="background-color: #10b981; color: #ffffff; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>

            <a href="{{ route('reports.print', ['start_date' => $startDate, 'end_date' => $endDate, 'type' => $type, 'status' => $status ?? '', 'class_name' => $selectedClass]) }}" 
               target="_blank"
               style="background-color: #1e293b; color: #ffffff; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak
            </a>
        </div>
    </div>

    <!-- CARD UTAMA LAPORAN -->
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 30px;">
        
        <!-- HEADER LAPORAN DINAMIS -->
        <div style="text-align: center; margin-bottom: 25px;">
            <h4 style="margin: 0 0 6px 0; font-weight: 700; color: #1e293b; letter-spacing: 0.5px; text-transform: uppercase;">
                @if($type === 'jurnal')
                    LAPORAN REKAPITULASI JURNAL MENGAJAR GURU
                @elseif($type === 'siswa')
                    LAPORAN CATATAN PIKET PRESENSI SISWA
                @elseif($type === 'guru')
                    LAPORAN CATATAN PIKET PRESENSI GURU
                @else
                    LAPORAN REKAPITULASI PRESENSI KBM KELAS
                @endif
            </h4>
            <p style="margin: 0; color: #475569; font-size: 0.9rem;">
                Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</strong> s.d. <strong>{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</strong>
            </p>
        </div>

        @if($type === 'jurnal')
            <!-- TABEL 1: JURNAL MENGAJAR GURU -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; border: 1px solid #e2e8f0;">
                    <thead>
                        <tr style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">
                            <th style="padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-align: left; width: 110px;">TANGGAL</th>
                            <th style="padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-align: left; width: 130px;">KELAS & JAM</th>
                            <th style="padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-align: left; width: 160px;">GURU MENGAJAR</th>
                            <th style="padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-align: left; width: 160px;">MATA PELAJARAN</th>
                            <th style="padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-align: left;">MATERI POKOK</th>
                            <th style="padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-align: left; width: 160px;">CATATAN / HAMBATAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($journals as $j)
                            <tr style="border-bottom: 1px solid #f1f5f9; color: #334155;">
                                <td style="padding: 12px 10px; font-weight: 600; color: #64748b; white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($j->date)->format('d M Y') }}
                                </td>
                                <td style="padding: 12px 10px;">
                                    <div style="font-weight: 700; color: #0f172a;">
                                        {{ $j->display_class ?? $j->class_name ?? '-' }}
                                    </div>
                                    @if(!empty($j->display_jam) && $j->display_jam !== '-')
                                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 2px;">
                                            Jam ke: {{ $j->display_jam }}
                                        </div>
                                    @elseif(!empty($j->jam_ke))
                                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 2px;">
                                            Jam ke: {{ $j->jam_ke }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 12px 10px; font-weight: 600; color: #1e293b;">
                                    {{ $j->display_teacher ?? $j->teacher->name ?? '-' }}
                                </td>
                                <td style="padding: 12px 10px; font-weight: 700; color: #1e293b;">
                                    {{ $j->display_subject ?? $j->subject->name ?? '-' }}
                                </td>
                                <td style="padding: 12px 10px; color: #1e293b; line-height: 1.4;">
                                    {{ $j->display_materi ?? $j->materi_pokok ?? $j->notes ?? '-' }}
                                </td>
                                <td style="padding: 12px 10px; color: #64748b;">
                                    {{ $j->display_hambatan ?? $j->hambatan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 25px; text-align: center; color: #94a3b8; font-style: italic;">
                                    Belum ada data jurnal mengajar guru untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($type === 'siswa' || $type === 'guru')
            <!-- TABEL 2: CATATAN PIKET SISWA / GURU -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; border: 1px solid #e2e8f0;">
                    <thead>
                        <tr style="background-color: #f8fafc; color: #334155; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; text-align: center; width: 100px;">TANGGAL</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; text-align: center; width: 120px;">TIPE / KELAS</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; text-align: left;">NAMA</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; text-align: center; width: 100px;">STATUS</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; text-align: left;">KETERANGAN / ALASAN</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; text-align: left; width: 140px;">PETUGAS PIKET</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $row)
                            <tr style="border-bottom: 1px solid #e2e8f0; color: #1e293b;">
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: center;">
                                    {{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: center; font-weight: 600; color: #4f46e5;">
                                    {{ ucfirst($row->type) }} - {{ $row->class_or_subject ?? '-' }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;">
                                    {{ $row->name }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: center; font-weight: 700; text-transform: uppercase;">
                                    {{ $row->status }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0;">
                                    {{ $row->notes ?? '-' }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0;">
                                    {{ $row->recorder?->name ?? 'Sistem' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 25px; text-align: center; color: #94a3b8; font-style: italic;">
                                    Belum ada data catatan piket {{ $type }} untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @else
            <!-- TABEL 3: REKAPITULASI PRESENSI KBM KELAS -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: center; border: 1px solid #e2e8f0;">
                    <thead>
                        <tr style="background-color: #f8fafc; color: #334155; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; width: 100px;">TANGGAL</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; width: 90px;">KELAS</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; text-align: left;">MATA PELAJARAN</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; text-align: left;">GURU PENGAJAR UTAMA</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; width: 130px;">JAM KE</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; width: 90px;">TOTAL BEBAN</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; width: 90px;">STATUS</th>
                            <th style="padding: 12px 10px; border: 1px solid #e2e8f0; text-align: left;">GURU PENGGANTI / CATATAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $row)
                            <tr style="border-bottom: 1px solid #e2e8f0; color: #1e293b;">
                                <td style="padding: 10px; border: 1px solid #e2e8f0;">
                                    {{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #4f46e5;">
                                    {{ $row->class_name }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: left; font-weight: 600;">
                                    {{ $row->subject_name }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: left;">
                                    {{ $row->teacher_name }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; color: #334155; font-weight: 500;">
                                    {{ $row->jam_text }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;">
                                    {{ $row->total_jp }} JP
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 700; text-transform: uppercase;">
                                    {{ $row->status }}
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: left;">
                                    <div style="color: #4f46e5; font-weight: 500;">
                                        Pengganti: {{ $row->substitute_teacher }}
                                    </div>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                        {{ $row->notes }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 25px; text-align: center; color: #94a3b8; font-style: italic;">
                                    Belum ada data rekapitulasi presensi KBM kelas untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    </div>

    <!-- CARD CETAK / UNDUH DOKUMEN RESMI PER KELAS -->
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-top: 25px;">
        <div style="margin-bottom: 20px;">
            <h5 style="margin: 0 0 4px 0; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; font-size: 1.05rem;">
                📄 Cetak / Unduh Dokumen Resmi Per Kelas
            </h5>
            <p style="margin: 0; color: #64748b; font-size: 0.85rem;">
                Pilih kelas diajar, jenis dokumen, serta format output untuk mencetak dokumen resmi (Format SMK Negeri 7 Pontianak).
            </p>
        </div>

        <form id="officialDocForm" onsubmit="handleDocSubmit(event)" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; padding: 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; align-items: end;">
                
                <!-- 1. Pilihan Kelas -->
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">
                        1. Pilih Kelas Diajar <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="single_doc_class" onchange="updateSubjectOptions()" required 
                            style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.85rem; {{ (!$isAdminOrWaka && empty($classList)) ? 'background-color: #f1f5f9; cursor: not-allowed;' : 'background-color: #ffffff;' }}"
                            {{ (!$isAdminOrWaka && empty($classList)) ? 'disabled' : '' }}>
                        @if(empty($classList))
                            <option value="">-- Tidak Ada Akses Kelas --</option>
                        @else
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classList as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- 2. Pilihan Jenis Dokumen -->
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">
                        2. Jenis Dokumen <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="single_doc_type" onchange="toggleSubjectField()" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.85rem; background-color: #ffffff;">
                        <option value="daftar-hadir">1. Daftar Hadir Siswa Bulanan</option>
                        <option value="rekap-presensi">2. Rekap Presensi Siswa</option>
                        <option value="blanko-nilai">3. Blanko Nilai Mata Pelajaran</option>
                    </select>
                </div>

                <!-- 3. Pilihan Mapel (Disesuaikan berdasarkan Kelas yang dipilih) -->
                <div id="subject_field_wrapper" style="display: none;">
                    <label style="display: block; font-weight: 600; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">
                        Mata Pelajaran <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="single_doc_subject" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.85rem; background-color: #ffffff;">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($subjectList as $sub)
                            <option value="{{ $sub }}">{{ $sub }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Format Output -->
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">
                        3. Format Output <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="single_doc_format" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.85rem; background-color: #ffffff;">
                        <option value="print">🖨️ Cetak PDF</option>
                        <option value="excel">📊 Unduh Excel</option>
                    </select>
                </div>

                <!-- Tombol Eksekusi -->
                <div>
                    <button type="submit" 
                            style="width: 100%; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; padding: 10px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; {{ (!$isAdminOrWaka && empty($classList)) ? 'opacity: 0.6; cursor: not-allowed;' : '' }}"
                            {{ (!$isAdminOrWaka && empty($classList)) ? 'disabled' : '' }}>
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Proses Dokumen
                    </button>
                </div>

            </div>
        </form>
    </div>

</div>

<!-- JAVASCRIPT HANDLER DOKUMEN RESMI DAN FILTER MAPEL DYNAMIC -->
<script>
// Data pemetaan kelas ke mata pelajaran milik guru / global
const classSubjectMap = @json($classSubjectMap ?? []);
const allTeacherSubjects = @json($subjectList ?? []);
const isAdminOrWaka = @json($isAdminOrWaka);

function updateSubjectOptions() {
    const selectedClass = document.getElementById('single_doc_class').value;
    const subjectSelect = document.getElementById('single_doc_subject');
    
    // Reset dropdown mapel
    subjectSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';

    let availableSubjects = [];

    if (selectedClass && classSubjectMap[selectedClass] && classSubjectMap[selectedClass].length > 0) {
        // Jika ada mapping khusus kelas -> mapel
        availableSubjects = classSubjectMap[selectedClass];
    } else {
        // Fallback ke seluruh list mapel jika tidak ada mapping khusus
        availableSubjects = allTeacherSubjects;
    }

    availableSubjects.forEach(subject => {
        const option = document.createElement('option');
        option.value = subject;
        option.textContent = subject;
        subjectSelect.appendChild(option);
    });
}

function toggleSubjectField() {
    const docType = document.getElementById('single_doc_type').value;
    const subjectWrapper = document.getElementById('subject_field_wrapper');
    const subjectSelect = document.getElementById('single_doc_subject');

    if (docType === 'blanko-nilai') {
        subjectWrapper.style.display = 'block';
        subjectSelect.setAttribute('required', 'required');
        updateSubjectOptions(); // Perbarui opsi saat ditampilkan
    } else {
        subjectWrapper.style.display = 'none';
        subjectSelect.removeAttribute('required');
        subjectSelect.value = '';
    }
}

function handleDocSubmit(e) {
    e.preventDefault();

    const selectedClass = document.getElementById('single_doc_class').value;
    const docType       = document.getElementById('single_doc_type').value;
    const format        = document.getElementById('single_doc_format').value;
    const subject       = document.getElementById('single_doc_subject').value;

    if (!selectedClass) {
        alert('Silakan pilih kelas terlebih dahulu.');
        return;
    }

    if (docType === 'blanko-nilai' && !subject) {
        alert('Silakan pilih mata pelajaran terlebih dahulu.');
        return;
    }

    let url = `/reports/doc/${docType}?class=${encodeURIComponent(selectedClass)}&format=${format}`;
    if (docType === 'blanko-nilai') {
        url += `&subject=${encodeURIComponent(subject)}`;
    }

    if (format === 'print') {
        window.open(url, '_blank');
    } else {
        window.location.href = url;
    }
}
</script>
</x-app-layout>