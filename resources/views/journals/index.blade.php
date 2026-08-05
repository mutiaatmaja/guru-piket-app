<x-app-layout>
<div style="max-width: 1100px; margin: 30px auto; padding: 0 15px; font-family: system-ui, -apple-system, sans-serif;">

    <!-- Style Tambahan untuk Interaktivitas & Animasi Modal -->
    <style>
        .journal-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 30px;
            transition: box-shadow 0.2s ease;
        }
        .journal-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        /* Card Schedule Style */
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .schedule-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }
        .schedule-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }
        .schedule-card.is-filled {
            border-left: 5px solid #10b981;
        }
        .schedule-card.not-filled {
            border-left: 5px solid #f59e0b;
        }

        .custom-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
            border-bottom: 2px solid #e2e8f0;
        }
        .custom-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
            font-size: 0.9rem;
        }
        .custom-table tr:hover {
            background-color: #f8fafc;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            width: 100%;
        }
        .btn-action:hover {
            transform: translateY(-1px);
        }
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.775rem;
            font-weight: 600;
        }
        
        /* Modal Style */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .modal-card {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: modalFadeIn 0.2s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    <!-- Header Banner Gradient -->
    <div style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 28px; border-radius: 16px; margin-bottom: 25px; box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.25); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="margin: 0 0 6px 0; font-weight: 800; font-size: 1.5rem; letter-spacing: -0.5px;">📖 Jurnal Mengajar Guru</h3>
            <p style="margin: 0; opacity: 0.9; font-size: 0.95rem;">Catat materi pembelajaran harian, pantau keterlaksanaan KBM, dan dokumentasikan hambatan kelas.</p>
        </div>
        <div>
            <span style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); color: #ffffff; padding: 8px 16px; border-radius: 30px; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                🗓️ {{ $todayNameIndo ?? now()->translatedFormat('l') }}, {{ \Carbon\Carbon::parse($todayDate ?? now())->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    {{-- Pesan Notifikasi --}}
    @if(session('success'))
        <div style="padding: 14px 18px; background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 10px; margin-bottom: 25px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
            <span>✅</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 14px 18px; background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 10px; margin-bottom: 25px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
            <span>⚠️</span>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- STATISTIK RINGKAS MENGAJAR HARI INI -->
    @php
        $totalToday = count($todaySchedules ?? []);
        $filledCount = collect($todaySchedules ?? [])->where('is_filled', true)->count();
        $unfilledCount = $totalToday - $filledCount;
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 25px;">
        <div style="background: #ffffff; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">📚</div>
            <div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Jadwal Hari Ini</div>
                <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a;">{{ $totalToday }} Kelas</div>
            </div>
        </div>
        <div style="background: #ffffff; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">✅</div>
            <div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Jurnal Terisi</div>
                <div style="font-size: 1.35rem; font-weight: 800; color: #16a34a;">{{ $filledCount }} Kelas</div>
            </div>
        </div>
        <div style="background: #ffffff; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">⏳</div>
            <div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Belum Diisi</div>
                <div style="font-size: 1.35rem; font-weight: 800; color: #d97706;">{{ $unfilledCount }} Kelas</div>
            </div>
        </div>
    </div>

    <!-- SEKSI 1: JADWAL MENGAJAR HARI INI (TAMPILAN CARD ELEGAN) -->
    <div class="journal-card">
        <div style="padding: 18px 20px; background-color: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 1.2rem;">📅</span>
                <h6 style="margin: 0; font-weight: 700; color: #1e293b; font-size: 1rem;">Jadwal Mengajar Hari Ini</h6>
            </div>
            <span style="font-size: 0.8rem; color: #64748b;">Pilih kartu kelas untuk mengisi atau memperbarui jurnal</span>
        </div>

        <div class="schedule-grid">
            @forelse($todaySchedules as $item)
                <div class="schedule-card {{ $item['is_filled'] ? 'is-filled' : 'not-filled' }}">
                    <div>
                        <!-- Header Card: Jam & Status Badge -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <span style="background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.8rem;">
                                ⏱️ Jam {{ $item['jam_ke'] }}
                            </span>
                            @if($item['is_filled'])
                                <span class="badge-status" style="background: #dcfce7; color: #15803d;">
                                    <span>✓</span> Sudah Diisi
                                </span>
                            @else
                                <span class="badge-status" style="background: #fef3c7; color: #b45309;">
                                    <span>•</span> Belum Diisi
                                </span>
                            @endif
                        </div>

                        <!-- Kelas & Mapel -->
                        <div style="margin-bottom: 15px;">
                            <h4 style="margin: 0 0 4px 0; font-size: 1.25rem; font-weight: 800; color: #2563eb;">
                                {{ $item['class_name'] }}
                            </h4>
                            <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">
                                {{ $item['subject']->name ?? '-' }}
                            </div>
                        </div>

                        <!-- Ringkasan Materi Jika Sudah Diisi -->
                        @if($item['is_filled'] && !empty($item['journal']->material))
                            <div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; margin-bottom: 15px; border: 1px dashed #cbd5e1;">
                                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 2px;">Materi Pokok:</div>
                                <div style="font-size: 0.85rem; color: #334155; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $item['journal']->material }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Aksi Modal -->
                    <button type="button" class="btn-action" 
                        style="background: {{ $item['is_filled'] ? '#f1f5f9' : '#2563eb' }}; color: {{ $item['is_filled'] ? '#334155' : '#ffffff' }}; box-shadow: {{ $item['is_filled'] ? 'none' : '0 3px 8px rgba(37, 99, 235, 0.25)' }};"
                        onclick="openJournalModal('{{ $item['schedule_id'] }}', '{{ $item['class_name'] }}', '{{ addslashes($item['subject']->name ?? '-') }}', '{{ addslashes($item['journal']->material ?? '') }}', '{{ addslashes($item['journal']->notes ?? '') }}')">
                        <span>✍️</span> {{ $item['is_filled'] ? 'Edit Jurnal' : 'Isi Jurnal Mengajar' }}
                    </button>
                </div>
            @empty
                <div style="grid-column: 1 / -1; padding: 40px 20px; text-align: center; color: #94a3b8;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">☕</div>
                    <div style="font-weight: 600; color: #64748b; font-size: 1rem;">Tidak ada jadwal mengajar hari ini</div>
                    <p style="margin: 4px 0 0 0; font-size: 0.875rem;">Nikmati waktu istirahat atau gunakan waktu untuk mempersiapkan materi besok.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- SEKSI 2: RIWAYAT JURNAL MENGAJAR SAYA -->
    <div class="journal-card">
        <div style="padding: 18px 20px; background-color: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 1.2rem;">📜</span>
                <h6 style="margin: 0; font-weight: 700; color: #1e293b; font-size: 1rem;">Riwayat Jurnal Mengajar Saya</h6>
            </div>
            
            @if(Route::has('journals.printPdf'))
                <a href="{{ route('journals.printPdf') }}" target="_blank" class="btn-action" style="background: #10b981; color: white; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25); width: auto;">
                    <span>🖨️</span> Cetak PDF Jurnal
                </a>
            @endif
        </div>

        <div style="overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr>
                        <th style="width: 130px;">Tanggal</th>
                        <th style="width: 140px;">Kelas & Jam</th>
                        <th>Mata Pelajaran</th>
                        <th>Materi Pokok</th>
                        <th>Catatan / Hambatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myJournals as $journal)
                        <tr>
                            <td style="font-weight: 600; color: #64748b; font-size: 0.85rem;">
                                {{ \Carbon\Carbon::parse($journal->date)->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <strong style="color: #0f172a;">{{ $journal->schedule->class_name ?? '-' }}</strong>
                                <div style="font-size: 0.775rem; color: #64748b; margin-top: 2px;">Jam ke: {{ $journal->formatted_jam_ke ?? '-' }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #334155;">{{ $journal->schedule->subject->name ?? '-' }}</span>
                            </td>
                            <td>
                                <div style="color: #0f172a; font-weight: 500; line-height: 1.5;">{{ $journal->material ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="color: #64748b; font-size: 0.85rem; line-height: 1.4;">{{ $journal->notes ?? '-' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 30px; text-align: center; color: #94a3b8;">
                                <em>Belum ada riwayat jurnal mengajar yang tersimpan.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL POPUP FORM ISI / EDIT JURNAL MENGAJAR -->
<div id="journalModal" class="modal-overlay">
    <div class="modal-card">
        <div style="padding: 18px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h5 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.05rem;" id="modalTitle">✍️ Isi Jurnal Mengajar</h5>
            <button type="button" onclick="closeJournalModal()" style="background: transparent; border: none; font-size: 1.25rem; color: #64748b; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('journals.store') }}" method="POST" style="padding: 20px;">
            @csrf
            <input type="hidden" name="schedule_id" id="modal_schedule_id">
            <input type="hidden" name="date" value="{{ $todayDate ?? date('Y-m-d') }}">

            <div style="background: #eff6ff; padding: 12px 15px; border-radius: 8px; margin-bottom: 18px; font-size: 0.875rem; border-left: 4px solid #2563eb;">
                <div>Kelas: <strong id="modal_class_name" style="color: #1e40af;">-</strong></div>
                <div>Mata Pelajaran: <strong id="modal_subject_name" style="color: #1e40af;">-</strong></div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Materi Pokok / Pembahasan <span style="color: #dc2626;">*</span></label>
                <textarea name="materi_pokok" id="modal_materi_pokok" rows="3" required placeholder="Tuliskan materi yang diajarkan hari ini..." style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; font-size: 0.9rem; font-family: inherit; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#cbd5e1'"></textarea>
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Catatan / Hambatan Pembelajaran (Opsional)</label>
                <textarea name="hambatan" id="modal_hambatan" rows="2" placeholder="Siswa terlambat, kendala proyektor, atau catatan khusus kelas..." style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; font-size: 0.9rem; font-family: inherit; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#cbd5e1'"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 15px;">
                <button type="button" onclick="closeJournalModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 9px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem;">Batal</button>
                <button type="submit" style="background: #2563eb; color: #ffffff; border: none; padding: 9px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);">Simpan Jurnal</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT PENGENDALI MODAL -->
<script>
    function openJournalModal(scheduleId, className, subjectName, material, notes) {
        document.getElementById('modal_schedule_id').value = scheduleId;
        document.getElementById('modal_class_name').innerText = className;
        document.getElementById('modal_subject_name').innerText = subjectName;
        document.getElementById('modal_materi_pokok').value = material;
        document.getElementById('modal_hambatan').value = notes;
        
        if (material !== '') {
            document.getElementById('modalTitle').innerText = '✍️ Edit Jurnal Mengajar';
        } else {
            document.getElementById('modalTitle').innerText = '✍️ Isi Jurnal Mengajar';
        }

        document.getElementById('journalModal').style.display = 'flex';
    }

    function closeJournalModal() {
        document.getElementById('journalModal').style.display = 'none';
    }

    // Tutup modal jika mengklik area gelap luar
    window.onclick = function(event) {
        let modal = document.getElementById('journalModal');
        if (event.target == modal) {
            closeJournalModal();
        }
    }
</script>

</x-app-layout>