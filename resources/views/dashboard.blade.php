<x-app-layout>
<div style="max-width: 1100px; margin: 30px auto; padding: 0 15px;">
    
    <!-- Welcome Banner -->
    <div style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);">
        <h4 style="margin: 0 0 8px 0; font-weight: 700;">Selamat Datang, {{ Auth::user()->name }}! 👋</h4>
        <p style="margin: 0; opacity: 0.9; font-size: 0.95rem;">
            Sistem Informasi Piket Sekolah (SIPIKET) — Catat, pantau, dan kelola aktivitas piket harian, jadwal, serta jurnal mengajar dengan mudah.
        </p>
    </div>

    @if(session('error'))
        <div style="padding: 12px 15px; background-color: #f8d7da; color: #842029; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div style="padding: 12px 15px; background-color: #d1e7dd; color: #0f5132; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Quick Action Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px;">
        
        <!-- Card 1: Catat Presensi Siswa -->
        <a href="{{ Route::has('piket.create') ? route('piket.create') : '#' }}" style="text-decoration: none; color: inherit;">
            <div style="background: #ffffff; border-radius: 10px; padding: 18px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); cursor: pointer;">
                <div style="font-size: 1.8rem; margin-bottom: 8px;">📋</div>
                <h6 style="margin: 0 0 4px 0; font-weight: 600; font-size: 1rem; color: #1e293b;">Catat Presensi Siswa</h6>
                <p style="margin: 0; color: #64748b; font-size: 0.8rem;">Input keterlambatan, izin, sakit, atau alpa siswa.</p>
            </div>
        </a>

        <!-- Card 2: Catat Proses KBM Guru di Kelas -->
        <a href="{{ Route::has('piket.kbm') ? route('piket.kbm') : '#' }}" style="text-decoration: none; color: inherit;">
            <div style="background: #ffffff; border-radius: 10px; padding: 18px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); cursor: pointer;">
                <div style="font-size: 1.8rem; margin-bottom: 8px;">🏫</div>
                <h6 style="margin: 0 0 4px 0; font-weight: 600; font-size: 1rem; color: #1e293b;">Catat KBM Guru</h6>
                <p style="margin: 0; color: #64748b; font-size: 0.8rem;">Rekap kehadiran guru, tugas, & aktivitas kelas.</p>
            </div>
        </a>

        <!-- Card 3: Jadwal Pelajaran -->
        <a href="{{ Route::has('schedules.index') ? route('schedules.index') : '#' }}" style="text-decoration: none; color: inherit;">
            <div style="background: #ffffff; border-radius: 10px; padding: 18px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); cursor: pointer;">
                <div style="font-size: 1.8rem; margin-bottom: 8px;">📅</div>
                <h6 style="margin: 0 0 4px 0; font-weight: 600; font-size: 1rem; color: #1e293b;">Jadwal Pelajaran</h6>
                <p style="margin: 0; color: #64748b; font-size: 0.8rem;">Lihat & kelola jadwal mata pelajaran.</p>
            </div>
        </a>

        <!-- Card 4: Jurnal Mengajar -->
        <a href="{{ Route::has('journals.index') ? route('journals.index') : '#' }}" style="text-decoration: none; color: inherit;">
            <div style="background: #ffffff; border-radius: 10px; padding: 18px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); cursor: pointer;">
                <div style="font-size: 1.8rem; margin-bottom: 8px;">📖</div>
                <h6 style="margin: 0 0 4px 0; font-weight: 600; font-size: 1rem; color: #1e293b;">Jurnal Mengajar</h6>
                <p style="margin: 0; color: #64748b; font-size: 0.8rem;">Isi & pantau jurnal materi mengajar.</p>
            </div>
        </a>

        <!-- Card 5: Data Pengajar -->
        <a href="{{ Route::has('teachers.index') ? route('teachers.index') : '#' }}" style="text-decoration: none; color: inherit;">
            <div style="background: #ffffff; border-radius: 10px; padding: 18px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); cursor: pointer;">
                <div style="font-size: 1.8rem; margin-bottom: 8px;">👨‍🏫</div>
                <h6 style="margin: 0 0 4px 0; font-weight: 600; font-size: 1rem; color: #1e293b;">Data Pengajar</h6>
                <p style="margin: 0; color: #64748b; font-size: 0.8rem;">Kelola daftar guru dan status piket.</p>
            </div>
        </a>

        <!-- Card 6: Laporan Piket -->
        <a href="{{ Route::has('reports.index') ? route('reports.index') : '#' }}" style="text-decoration: none; color: inherit;">
            <div style="background: #ffffff; border-radius: 10px; padding: 18px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); cursor: pointer;">
                <div style="font-size: 1.8rem; margin-bottom: 8px;">📊</div>
                <h6 style="margin: 0 0 4px 0; font-weight: 600; font-size: 1rem; color: #1e293b;">Laporan Piket</h6>
                <p style="margin: 0; color: #64748b; font-size: 0.8rem;">Rekapitulasi catatan piket harian.</p>
            </div>
        </a>

    </div>

    <!-- Ringkasan Info / Pengumuman & Petugas Piket -->
    <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 30px;">
        <div style="background-color: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0;">
            <h6 style="margin: 0; font-weight: 600; color: #334155;">📌 Informasi Petugas Piket Hari Ini</h6>
        </div>
        <div style="padding: 20px; color: #475569; font-size: 0.95rem; line-height: 1.6;">
            <p style="margin: 0 0 12px 0;">Pastikan setiap catatan kedatangan keterlambatan atau izin dicatat secara aktual. Gunakan menu <strong>Catat Presensi Siswa</strong> atau <strong>Catat KBM Guru</strong> untuk menambah entri baru.</p>
            
            <div style="margin-bottom: 15px;">
                <span style="display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                    Hari: {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>

            <!-- Daftar Nama Petugas Piket Hari Ini -->
            <div style="background-color: #f8fafc; padding: 12px 15px; border-radius: 8px; border-left: 4px solid #0d6efd;">
                <div style="font-weight: 600; color: #1e293b; margin-bottom: 6px; font-size: 0.9rem;">
                    👮 Petugas Piket Bertugas Hari Ini:
                </div>
                @if(isset($todaySchedules) && $todaySchedules->count() > 0)
                    <ul style="margin: 0; padding-left: 20px; color: #334155; font-size: 0.9rem;">
                        @foreach($todaySchedules as $schedule)
                            <li style="margin-bottom: 3px;">
                                <strong>{{ $schedule->teacher->name ?? $schedule->name ?? 'Petugas Piket' }}</strong>
                                @if(!empty($schedule->notes))
                                    <span style="color: #64748b; font-size: 0.8rem;">({{ $schedule->notes }})</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span style="color: #94a3b8; font-size: 0.85rem; font-style: italic;">
                        Belum ada jadwal petugas piket yang dikonfigurasi untuk hari ini.
                    </span>
                @endif
            </div>

        </div>
    </div>

    <!-- STATISTIK CATATAN PIKET HARI INI -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 30px;">
        
        <!-- Statistik Piket Guru -->
        <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="background-color: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0;">
                <h6 style="margin: 0; font-weight: 600; color: #334155;">👨‍🏫 Statistik Piket Guru Hari Ini</h6>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; text-align: center;">
                    <div style="background: #fffbeb; border: 1px solid #fef3c7; padding: 12px 8px; border-radius: 8px;">
                        <div style="font-size: 1.4rem; font-weight: 700; color: #d97706;">{{ $teacherStats['terlambat'] ?? 0 }}</div>
                        <div style="font-size: 0.75rem; color: #92400e; font-weight: 600; margin-top: 4px;">Terlambat</div>
                    </div>
                    <div style="background: #eff6ff; border: 1px solid #dbeafe; padding: 12px 8px; border-radius: 8px;">
                        <div style="font-size: 1.4rem; font-weight: 700; color: #2563eb;">{{ $teacherStats['izin'] ?? 0 }}</div>
                        <div style="font-size: 0.75rem; color: #1e40af; font-weight: 600; margin-top: 4px;">Izin</div>
                    </div>
                    <div style="background: #f0fdf4; border: 1px solid #dcfce7; padding: 12px 8px; border-radius: 8px;">
                        <div style="font-size: 1.4rem; font-weight: 700; color: #16a34a;">{{ $teacherStats['sakit'] ?? 0 }}</div>
                        <div style="font-size: 0.75rem; color: #166534; font-weight: 600; margin-top: 4px;">Sakit</div>
                    </div>
                    <div style="background: #fef2f2; border: 1px solid #fee2e2; padding: 12px 8px; border-radius: 8px;">
                        <div style="font-size: 1.4rem; font-weight: 700; color: #dc2626;">{{ $teacherStats['alpa'] ?? 0 }}</div>
                        <div style="font-size: 0.75rem; color: #991b1b; font-weight: 600; margin-top: 4px;">Alpa</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Piket Siswa -->
        <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="background-color: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0;">
                <h6 style="margin: 0; font-weight: 600; color: #334155;">🎓 Statistik Piket Siswa Hari Ini</h6>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; text-align: center;">
                    <div style="background: #fffbeb; border: 1px solid #fef3c7; padding: 12px 8px; border-radius: 8px;">
                        <div style="font-size: 1.4rem; font-weight: 700; color: #d97706;">{{ $studentStats['terlambat'] ?? 0 }}</div>
                        <div style="font-size: 0.75rem; color: #92400e; font-weight: 600; margin-top: 4px;">Terlambat</div>
                    </div>
                    <div style="background: #eff6ff; border: 1px solid #dbeafe; padding: 12px 8px; border-radius: 8px;">
                        <div style="font-size: 1.4rem; font-weight: 700; color: #2563eb;">{{ $studentStats['izin'] ?? 0 }}</div>
                        <div style="font-size: 0.75rem; color: #1e40af; font-weight: 600; margin-top: 4px;">Izin</div>
                    </div>
                    <div style="background: #f0fdf4; border: 1px solid #dcfce7; padding: 12px 8px; border-radius: 8px;">
                        <div style="font-size: 1.4rem; font-weight: 700; color: #16a34a;">{{ $studentStats['sakit'] ?? 0 }}</div>
                        <div style="font-size: 0.75rem; color: #166534; font-weight: 600; margin-top: 4px;">Sakit</div>
                    </div>
                    <div style="background: #fef2f2; border: 1px solid #fee2e2; padding: 12px 8px; border-radius: 8px;">
                        <div style="font-size: 1.4rem; font-weight: 700; color: #dc2626;">{{ $studentStats['alpa'] ?? 0 }}</div>
                        <div style="font-size: 0.75rem; color: #991b1b; font-weight: 600; margin-top: 4px;">Alpa</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- PANEL TAMPILAN JADWAL PELAJARAN HARI INI & JURNAL MENGAJAR TERBARU -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 30px;">
        
        <!-- Tabel Ringkas Jadwal Pelajaran Hari Ini -->
        <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="background-color: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h6 style="margin: 0; font-weight: 600; color: #334155;">📅 Jadwal Pelajaran Hari Ini</h6>
                <a href="{{ Route::has('schedules.index') ? route('schedules.index') : '#' }}" style="font-size: 0.8rem; color: #0d6efd; text-decoration: none; font-weight: 600;">Selengkapnya &rarr;</a>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                    <thead>
                        <tr style="background-color: #f1f5f9; color: #475569;">
                            <th style="padding: 10px 12px;">Jam</th>
                            <th style="padding: 10px 12px;">Kelas</th>
                            <th style="padding: 10px 12px;">Mata Pelajaran</th>
                            <th style="padding: 10px 12px;">Pengajar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($todayLessonSchedules) && $todayLessonSchedules->count() > 0)
                            @foreach($todayLessonSchedules as $schedule)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px 12px; font-weight: 600;">Ke-{{ $schedule->time_slot }}</td>
                                    <td style="padding: 10px 12px; color: #0d6efd; font-weight: 600;">{{ $schedule->class_name }}</td>
                                    <td style="padding: 10px 12px;">{{ $schedule->subject->name ?? '-' }}</td>
                                    <td style="padding: 10px 12px; color: #475569;">{{ $schedule->teacher->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8; font-style: italic;">
                                    Tidak ada jadwal pelajaran aktif untuk hari ini.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Ringkas Jurnal Mengajar Terbaru -->
        <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="background-color: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h6 style="margin: 0; font-weight: 600; color: #334155;">📖 Jurnal Mengajar Terbaru</h6>
                <a href="{{ Route::has('journals.index') ? route('journals.index') : '#' }}" style="font-size: 0.8rem; color: #0d6efd; text-decoration: none; font-weight: 600;">Selengkapnya &rarr;</a>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                    <thead>
                        <tr style="background-color: #f1f5f9; color: #475569;">
                            <th style="padding: 10px 12px;">Guru</th>
                            <th style="padding: 10px 12px;">Mapel / Kelas</th>
                            <th style="padding: 10px 12px;">Materi / Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($recentJournals) && $recentJournals->count() > 0)
                            @foreach($recentJournals as $journal)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px 12px; font-weight: 600;">{{ $journal->teacher->name ?? '-' }}</td>
                                    <td style="padding: 10px 12px;">
                                        <strong>{{ $journal->subject->name ?? '-' }}</strong><br>
                                        <small style="color: #64748b;">{{ $journal->class_name ?? '-' }}</small>
                                    </td>
                                    <td style="padding: 10px 12px; color: #475569;">{{ \Illuminate\Support\Str::limit($journal->material ?? $journal->notes ?? '-', 35) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" style="padding: 20px; text-align: center; color: #94a3b8; font-style: italic;">
                                    Belum ada jurnal mengajar yang diinput.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- TABEL DETAIL CATATAN PIKET TERBARU HARI INI -->
    @php
        $userRole = strtolower(auth()->user()->role ?? auth()->user()->role_id ?? '');
        $isAdminOrWakasek = in_array($userRole, ['admin', 'administrator', 'wakasek']);
    @endphp

    <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="background-color: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h6 style="margin: 0; font-weight: 600; color: #334155;">📋 Catatan Piket Terbaru Hari Ini</h6>
            <a href="{{ Route::has('reports.index') ? route('reports.index') : '#' }}" style="font-size: 0.85rem; color: #0d6efd; text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="background-color: #1e293b; color: #ffffff;">
                        <th style="padding: 12px 15px; width: 50px; text-align: center;">#</th>
                        <th style="padding: 12px 15px;">Nama / Objek</th>
                        <th style="padding: 12px 15px;">Tipe</th>
                        <th style="padding: 12px 15px;">Jam Pelajaran</th>
                        <th style="padding: 12px 15px;">Status</th>
                        <th style="padding: 12px 15px;">Catatan / Keterangan</th>
                        @if($isAdminOrWakasek)
                            <th style="padding: 12px 15px; text-align: center; width: 140px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances as $item)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 12px 15px; text-align: center; font-weight: 600; color: #64748b;">{{ $loop->iteration }}</td>
                            <td style="padding: 12px 15px;">
                                <strong style="color: #1e293b;">{{ $item->name }}</strong>
                                @if(!empty($item->class_or_subject) && $item->class_or_subject !== '-')
                                    <div style="font-size: 0.8rem; color: #64748b;">({{ $item->class_or_subject }})</div>
                                @endif
                            </td>
                            <td style="padding: 12px 15px;">
                                <span style="background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">
                                    {{ $item->type }}
                                </span>
                            </td>
                            <td style="padding: 12px 15px;">
                                @if($item->lesson_hour_start && $item->lesson_hour_end)
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                        @if($item->lesson_hour_start == $item->lesson_hour_end)
                                            Jam ke-{{ $item->lesson_hour_start }}
                                        @else
                                            Jam ke-{{ $item->lesson_hour_start }} s.d. {{ $item->lesson_hour_end }}
                                        @endif
                                    </span>
                                @elseif($item->lesson_hour_start)
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                        Jam ke-{{ $item->lesson_hour_start }}
                                    </span>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td style="padding: 12px 15px;">
                                @switch(strtolower($item->status))
                                    @case('terlambat')
                                        <span style="background: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Terlambat</span>
                                        @break
                                    @case('sakit')
                                        <span style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Sakit</span>
                                        @break
                                    @case('izin')
                                        <span style="background: #dbeafe; color: #1e40af; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Izin</span>
                                        @break
                                    @case('alpa')
                                        <span style="background: #fee2e2; color: #991b1b; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Alpa</span>
                                        @break
                                    @default
                                        <span style="background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">{{ ucfirst($item->status) }}</span>
                                @endswitch
                            </td>
                            <td style="padding: 12px 15px; color: #475569;">{{ $item->description ?? '-' }}</td>

                            {{-- Tombol Aksi Khusus Admin / Wakasek --}}
                            @if($isAdminOrWakasek)
                                <td style="padding: 12px 15px; text-align: center;">
                                    <div style="display: inline-flex; gap: 5px;">
                                        @if(Route::has('piket.edit'))
                                            <a href="{{ route('piket.edit', $item->id) }}" style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                                Edit
                                            </a>
                                        @endif
                                        
                                        @if(Route::has('piket.destroy'))
                                            <form action="{{ route('piket.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan piket ini?')" style="margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: #dc3545; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem; font-weight: 600;">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdminOrWakasek ? '7' : '6' }}" style="padding: 25px; text-align: center; color: #94a3b8;">
                                <em>Belum ada catatan piket yang terekam hari ini.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</x-app-layout>