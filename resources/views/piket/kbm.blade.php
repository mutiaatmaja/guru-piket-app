<x-app-layout>
<div style="max-width: 1200px; margin: 24px auto; padding: 0 20px; font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif; color: #0f172a;">

    <!-- STYLING CUSTOM UI MODERN -->
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #e0e7ff;
            --surface: #ffffff;
            --background: #f8fafc;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        /* Glassmorphism & Header Card */
        .glass-header {
            background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 50%, #4f46e5 100%);
            border-radius: 20px;
            padding: 32px;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
            position: relative;
            overflow: hidden;
            margin-bottom: 28px;
        }
        .glass-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Stat Card */
        .stat-card {
            background: var(--surface);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.06);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        /* Layout Main Containers */
        .content-card {
            background: var(--surface);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            margin-bottom: 28px;
            overflow: hidden;
        }
        .card-header-clean {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: #ffffff;
        }

        /* Class Schedule Cards */
        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            padding: 24px;
            background: #f8fafc;
        }
        .class-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            transition: all 0.25s ease;
        }
        .class-card:hover {
            transform: translateY(-3px);
            border-color: #c7d2fe;
            box-shadow: 0 12px 24px -6px rgba(79, 70, 229, 0.08);
        }

        /* Inputs & Controls */
        .input-modern {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            color: #0f172a;
            background-color: #ffffff;
            transition: all 0.2s ease;
            outline: none;
        }
        .input-modern:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* Table Styling */
        .clean-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .clean-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
        }
        .clean-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
            color: #334155;
            vertical-align: middle;
        }
        .clean-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Buttons & Badges */
        .btn-primary-modern {
            background: #4f46e5;
            color: #ffffff;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
        }
        .btn-primary-modern:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.3);
        }
        .btn-secondary-modern {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-secondary-modern:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Modal Backdrop */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(6px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-container {
            background: #ffffff;
            border-radius: 20px;
            width: 100%;
            max-width: 540px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes modalPop {
            0% { opacity: 0; transform: scale(0.96) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>

    <!-- HEADER BANNER -->
    <div class="glass-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; position: relative; z-index: 2;">
            <div>
                <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 12px; backdrop-filter: blur(4px);">
                    📋 Petugas Piket Sekolah
                </div>
                <h2 style="margin: 0 0 8px 0; font-weight: 800; font-size: 1.75rem; letter-spacing: -0.02em;">Pemantauan KBM Piket</h2>
                <p style="margin: 0; opacity: 0.88; font-size: 0.95rem; max-width: 600px; line-height: 1.5;">
                    Pantau kehadiran guru di kelas, atur guru pengganti, dan kelola status pelaksanaan Kegiatan Belajar Mengajar harian.
                </p>
            </div>
            
            <!-- Date Filter Box -->
            <form method="GET" action="{{ route('piket.kbm') }}" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); padding: 12px 18px; border-radius: 16px; display: flex; align-items: center; gap: 10px;">
                <input type="date" name="date" value="{{ $dateInput }}" class="input-modern" style="padding: 6px 10px; font-size: 0.85rem; border: none; background: rgba(255,255,255,0.9);">
                <button type="submit" class="btn-primary-modern" style="padding: 6px 14px; font-size: 0.85rem;">
                    Filter
                </button>
            </form>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div style="padding: 16px 20px; background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; border-radius: 12px; margin-bottom: 24px; font-size: 0.9rem; display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 1.2rem;">✅</span>
            <div style="font-weight: 500;">{{ session('success') }}</div>
        </div>
    @endif

    <!-- RINGKASAN STATISTIK KBM -->
    @php
        $flatSchedules = collect($groupedSchedulesByClass)->flatten(1);
        $totalSchedules = $flatSchedules->count();
        $filledSchedules = $flatSchedules->where('is_filled', true)->count();
        $unfilledSchedules = $totalSchedules - $filledSchedules;
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 28px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">🏫</div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Total Sesi KBM</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); line-height: 1.2;">{{ $totalSchedules }} Sesi</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">✅</div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Sudah Terpantau</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #059669; line-height: 1.2;">{{ $filledSchedules }} Sesi</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">⏳</div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Belum Diisi</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #d97706; line-height: 1.2;">{{ $unfilledSchedules }} Sesi</div>
            </div>
        </div>
    </div>

    <!-- SEKSI 1: JADWAL KBM PER KELAS & MULTI-FILTER BAR -->
    <div class="content-card">
        <div class="card-header-clean">
            <!-- Header Title -->
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="background: #e0e7ff; color: #4f46e5; padding: 8px; border-radius: 10px; font-size: 1rem; display: flex;">📌</div>
                    <div>
                        <h3 style="margin: 0; font-weight: 700; color: var(--text-main); font-size: 1.1rem;">Pemantauan Jadwal per Kelas</h3>
                        <p style="margin: 2px 0 0 0; font-size: 0.8rem; color: var(--text-muted);">
                            Hari <strong style="color: #4f46e5;">{{ $dayName }}</strong>, {{ \Carbon\Carbon::parse($dateInput)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>

                <button type="button" onclick="resetFilters()" class="btn-secondary-modern" style="font-size: 0.775rem; padding: 6px 12px;">
                    🔄 Reset Filter
                </button>
            </div>

            <!-- BAR MULTI-FILTER INTERAKTIF -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid var(--border);">
                <!-- Filter 1: Kelas -->
                <div>
                    <label style="display: block; font-size: 0.725rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase;">Filter Kelas</label>
                    <select id="filterClass" onchange="applyFilters()" class="input-modern" style="padding: 7px 10px; font-size: 0.825rem;">
                        <option value="">-- Semua Kelas --</option>
                        @foreach(array_keys($groupedSchedulesByClass) as $cName)
                            <option value="{{ strtolower($cName) }}">{{ $cName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter 2: Jam Ke- -->
                <div>
                    <label style="display: block; font-size: 0.725rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase;">Jam Ke-</label>
                    <select id="filterJam" onchange="applyFilters()" class="input-modern" style="padding: 7px 10px; font-size: 0.825rem;">
                        <option value="">-- Semua Jam --</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="jam ke-{{ $i }}">Jam Ke-{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Filter 3: Guru -->
                <div>
                    <label style="display: block; font-size: 0.725rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase;">Guru Mengajar</label>
                    <select id="filterTeacher" onchange="applyFilters()" class="input-modern" style="padding: 7px 10px; font-size: 0.825rem;">
                        <option value="">-- Semua Guru --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ strtolower($teacher->name) }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter 4: Mata Pelajaran -->
                <div>
                    <label style="display: block; font-size: 0.725rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase;">Cari Mata Pelajaran</label>
                    <input type="text" id="filterSubject" onkeyup="applyFilters()" placeholder="Ketik nama mapel..." class="input-modern" style="padding: 7px 10px; font-size: 0.825rem;">
                </div>
            </div>
        </div>

        <div class="class-grid" id="classGridContainer">
            @forelse($groupedSchedulesByClass as $className => $schedules)
                <div class="class-card" data-class="{{ strtolower($className) }}">
                    <!-- Header Card Kelas -->
                    <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                        <h4 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;">{{ $className }}</h4>
                        <span style="font-size: 0.75rem; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 3px 10px; border-radius: 20px;">
                            {{ count($schedules) }} Sesi
                        </span>
                    </div>

                    <!-- List Jam Mengajar -->
                    <div style="padding: 16px 20px; display: flex; flex-direction: column; gap: 14px;">
                        @foreach($schedules as $item)
                            <div class="schedule-session-item" 
                                 data-jam="{{ strtolower($item['jam_display']) }}"
                                 data-teacher="{{ strtolower($item['teacher']->name ?? '') }}"
                                 data-subject="{{ strtolower($item['subject']->name ?? '') }}"
                                 style="padding-bottom: 12px; border-bottom: 1px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                                
                                <div style="flex-grow: 1;">
                                    <div style="font-size: 0.775rem; font-weight: 700; color: #4f46e5; margin-bottom: 2px;">
                                        ⏱️ {{ $item['jam_display'] }}
                                    </div>
                                    <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">
                                        {{ $item['subject']->name ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                        👤 {{ $item['teacher']->name ?? 'Belum ada guru' }}
                                    </div>
                                </div>

                                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
                                    <!-- Badge Status Kehadiran -->
                                    <span class="badge-pill" style="
                                        @if($item['status'] == 'hadir') background: #dcfce7; color: #15803d;
                                        @elseif($item['status'] == 'tugas') background: #fef3c7; color: #b45309;
                                        @elseif($item['status'] == 'izin') background: #dbeafe; color: #1d4ed8;
                                        @elseif($item['status'] == 'alpa') background: #fee2e2; color: #b91c1c;
                                        @elseif($item['status'] == 'kosong') background: #f3e8ff; color: #6b21a8;
                                        @else background: #f1f5f9; color: #64748b; @endif">
                                        {{ strtoupper(str_replace('_', ' ', $item['status'])) }}
                                    </span>

                                    <!-- Action Button -->
                                    <button type="button" class="btn-secondary-modern" onclick="openModal({{ json_encode($item) }})">
                                        ✍️ {{ $item['is_filled'] ? 'Ubah Status' : 'Isi Status' }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; padding: 60px 20px; text-align: center; color: #94a3b8; background: #ffffff; border-radius: 12px;">
                    <div style="font-size: 3rem; margin-bottom: 12px;">🗓️</div>
                    <div style="font-weight: 700; color: #334155; font-size: 1.1rem;">Tidak Ada Jadwal KBM</div>
                    <p style="margin: 4px 0 0 0; font-size: 0.875rem;">Tidak ada jadwal pembelajaran pada hari {{ $dayName }}.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- SEKSI 2: RIWAYAT PEMANTAUAN PIKET TERAKHIR -->
    <div class="content-card">
        <div class="card-header-clean" style="flex-direction: row; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="background: #f3e8ff; color: #9333ea; padding: 8px; border-radius: 10px; font-size: 1rem; display: flex;">📜</div>
                <div>
                    <h3 style="margin: 0; font-weight: 700; color: var(--text-main); font-size: 1.1rem;">Riwayat Pemantauan Hari Ini</h3>
                    <p style="margin: 2px 0 0 0; font-size: 0.8rem; color: var(--text-muted);">Catatan kehadiran guru yang baru saja diperbarui oleh piket</p>
                </div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th>Kelas & Jam</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Utama</th>
                        <th>Status</th>
                        <th>Guru Pengganti / Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances as $att)
                        <tr>
                            <td>
                                <span style="font-weight: 700; color: #4f46e5; background: #e0e7ff; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem;">
                                    {{ $att->schedule->class_name ?? '-' }}
                                </span>
                                <div style="font-size: 0.775rem; color: #64748b; margin-top: 4px;">Jam Ke-{{ $att->schedule->time_slot ?? '-' }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #334155;">{{ $att->schedule->subject->name ?? '-' }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: #0f172a;">{{ $att->schedule->teacher->name ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge-pill" style="
                                    @if($att->status == 'hadir') background: #dcfce7; color: #15803d;
                                    @elseif($att->status == 'tugas') background: #fef3c7; color: #b45309;
                                    @elseif($att->status == 'izin') background: #dbeafe; color: #1d4ed8;
                                    @elseif($att->status == 'alpa') background: #fee2e2; color: #b91c1c;
                                    @elseif($att->status == 'kosong') background: #f3e8ff; color: #6b21a8;
                                    @else background: #f1f5f9; color: #64748b; @endif">
                                    {{ strtoupper(str_replace('_', ' ', $att->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($att->substituteTeacher)
                                    <div style="font-weight: 600; color: #2563eb; font-size: 0.85rem;">🔄 Pengganti: {{ $att->substituteTeacher->name }}</div>
                                @endif
                                <div style="color: #64748b; font-size: 0.825rem; line-height: 1.4;">{{ $att->task_description ?? '-' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px 20px; text-align: center; color: #94a3b8;">
                                <em>Belum ada aktivitas presensi KBM terisi untuk tanggal ini.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL FORM UPDATE STATUS PRESENSI KBM -->
<div id="attendanceModal" class="modal-backdrop">
    <div class="modal-container">
        <!-- Header Modal -->
        <div style="padding: 20px 24px; background: #ffffff; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="background: #e0e7ff; color: #4f46e5; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">✍️</div>
                <h4 style="margin: 0; font-weight: 700; color: var(--text-main); font-size: 1.1rem;">Update Status KBM</h4>
            </div>
            <button type="button" onclick="closeModal()" style="background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; font-size: 1.2rem; color: #64748b; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('piket.kbm.store') }}" method="POST" style="padding: 24px;">
            @csrf
            <input type="hidden" name="date" value="{{ $dateInput }}">
            <div id="scheduleIdsContainer"></div>

            <!-- Detail Info Kelas & Jam -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 12px; margin-bottom: 18px;">
                <span style="font-size: 0.725rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Sesi Pembelajaran</span>
                <div id="modalInfo" style="font-weight: 800; color: #4f46e5; font-size: 1rem; margin-top: 2px;">-</div>
            </div>

            <!-- Status Selection -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Status Kehadiran Guru</label>
                <select name="status" id="modalStatus" class="input-modern" required>
                    <option value="hadir">Hadir (Sesuai Jadwal)</option>
                    <option value="tugas">Tugas / Ada Digantikan</option>
                    <option value="izin">Izin / Sakit</option>
                    <option value="alpa">Alpa (Tanpa Keterangan)</option>
                    <option value="kosong">Jam Kosong</option>
                </select>
            </div>

            <!-- Guru Pengganti Selection -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Guru Pengganti (Opsional)</label>
                <select name="substitute_teacher_id" id="modalSubstitute" class="input-modern">
                    <option value="">-- Tidak Ada --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Catatan Tugas -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Keterangan / Catatan Tugas</label>
                <textarea name="task_description" id="modalTask" rows="3" class="input-modern" placeholder="Tuliskan materi tugas atau instruksi tambahan jika guru berhalangan..." style="resize: vertical; font-family: inherit;"></textarea>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border); padding-top: 18px;">
                <button type="button" onclick="closeModal()" class="btn-secondary-modern">Batal</button>
                <button type="submit" class="btn-primary-modern">Simpan Status</button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT HANDLER -->
<script>
    function openModal(item) {
        const container = document.getElementById('scheduleIdsContainer');
        container.innerHTML = '';
        
        // Generate hidden inputs for schedule_ids
        if (item.schedule_ids && item.schedule_ids.length > 0) {
            item.schedule_ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'schedule_ids[]';
                input.value = id;
                container.appendChild(input);
            });
        } else if (item.schedule_id) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'schedule_ids[]';
            input.value = item.schedule_id;
            container.appendChild(input);
        }

        document.getElementById('modalInfo').innerText = `${item.class_name} - ${item.jam_display}`;
        document.getElementById('modalStatus').value = (item.status && item.status !== 'belum_diisi') ? item.status : 'hadir';
        document.getElementById('modalSubstitute').value = item.substitute_teacher_id || '';
        document.getElementById('modalTask').value = item.task_description || item.notes || '';

        document.getElementById('attendanceModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('attendanceModal').style.display = 'none';
    }

    // MULTI-FILTER REALTIME FUNCTION
    function applyFilters() {
        const selectedClass = document.getElementById('filterClass').value.toLowerCase();
        const selectedJam = document.getElementById('filterJam').value.toLowerCase();
        const selectedTeacher = document.getElementById('filterTeacher').value.toLowerCase();
        const inputSubject = document.getElementById('filterSubject').value.toLowerCase();

        const classCards = document.querySelectorAll('#classGridContainer .class-card');

        classCards.forEach(card => {
            const cardClass = card.getAttribute('data-class') || '';
            const matchClass = selectedClass === '' || cardClass === selectedClass;

            let visibleSessionsCount = 0;
            const sessions = card.querySelectorAll('.schedule-session-item');

            sessions.forEach(session => {
                const jam = session.getAttribute('data-jam') || '';
                const teacher = session.getAttribute('data-teacher') || '';
                const subject = session.getAttribute('data-subject') || '';

                const matchJam = selectedJam === '' || jam.includes(selectedJam);
                const matchTeacher = selectedTeacher === '' || teacher.includes(selectedTeacher);
                const matchSubject = inputSubject === '' || subject.includes(inputSubject);

                if (matchClass && matchJam && matchTeacher && matchSubject) {
                    session.style.display = 'flex';
                    visibleSessionsCount++;
                } else {
                    session.style.display = 'none';
                }
            });

            // Tampilkan card kelas jika ada minimal 1 sesi jam mengajar yang cocok dengan filter
            if (matchClass && visibleSessionsCount > 0) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function resetFilters() {
        document.getElementById('filterClass').value = '';
        document.getElementById('filterJam').value = '';
        document.getElementById('filterTeacher').value = '';
        document.getElementById('filterSubject').value = '';
        applyFilters();
    }

    window.onclick = function(event) {
        const modal = document.getElementById('attendanceModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

</x-app-layout>