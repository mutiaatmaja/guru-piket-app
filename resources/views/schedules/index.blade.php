<x-app-layout>
<div style="max-width: 1100px; margin: 30px auto; padding: 0 15px;">

    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h4 style="margin: 0; font-weight: 700; color: #1e293b;">📅 Kelola & Filter Jadwal Pelajaran</h4>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.9rem;">
                Hari ini: <strong>{{ $todayName ?? \Carbon\Carbon::now()->locale('id')->isoFormat('dddd') }}</strong> — Pilih kriteria di bawah untuk memfilter jadwal pelajaran.
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if(in_array(auth()->user()->role ?? '', ['admin', 'administrator', 'wakasek']))
                <!-- Tombol Import Excel -->
                <button type="button" onclick="openImportModal()" style="background: #198754; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    📥 Import Excel
                </button>

                <!-- Tombol Tambah Jadwal -->
                <button type="button" onclick="openCreateModal()" style="background: #0d6efd; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    + Tambah Jadwal
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div style="padding: 12px 15px; background-color: #d1e7dd; color: #0f5132; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 12px 15px; background-color: #f8d7da; color: #842029; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form Filter (Hari, Kelas, Jam ke, Mata Pelajaran, Guru Pengampu) -->
    <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 25px;">
        <form method="GET" action="{{ route('schedules.index') }}">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
                
                <!-- 1. Pilih Hari -->
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        📆 Pilih Hari
                    </label>
                    <select name="day" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem; background-color: #f8fafc;">
                        <option value="">-- Semua Hari --</option>
                        @foreach($days ?? ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $day)
                            <option value="{{ $day }}" {{ request('day') == $day ? 'selected' : '' }}>
                                {{ $day }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Pilih Kelas -->
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        🏫 Pilih Kelas
                    </label>
                    <select name="class_name" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem; background-color: #f8fafc;">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($classList ?? [] as $class)
                            <option value="{{ $class }}" {{ request('class_name') == $class ? 'selected' : '' }}>
                                {{ $class }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Pilih Jam Ke -->
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        ⏰ Pilih Jam ke-
                    </label>
                    <select name="time_slot" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem; background-color: #f8fafc;">
                        <option value="">-- Semua Jam --</option>
                        @foreach($timeSlots ?? range(1,12) as $slot)
                            <option value="{{ $slot }}" {{ request('time_slot') == $slot ? 'selected' : '' }}>
                                Jam ke-{{ $slot }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Pilih Mata Pelajaran -->
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        📚 Pilih Mata Pelajaran
                    </label>
                    <select name="subject_id" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem; background-color: #f8fafc;">
                        <option value="">-- Semua Mapel --</option>
                        @foreach($subjects ?? [] as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Pilih Guru Pengampu -->
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        👤 Pilih Guru Pengampu
                    </label>
                    <select name="teacher_id" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem; background-color: #f8fafc;">
                        <option value="">-- Semua Guru --</option>
                        @foreach($teachers ?? [] as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Tombol Filter & Reset -->
            <div style="display: flex; gap: 10px; margin-top: 18px; justify-content: flex-end;">
                <a href="{{ route('schedules.index') }}" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                    Reset Filter
                </a>
                <button type="submit" style="background: #0d6efd; color: #fff; border: none; padding: 8px 20px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                    🔍 Tampilkan Jadwal
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL HASIL JADWAL PELAJARAN -->
    <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="background-color: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h6 style="margin: 0; font-weight: 600; color: #334155;">📋 Daftar Hasil Jadwal Pelajaran</h6>
            <span style="font-size: 0.8rem; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-weight: 600;">
                Total: {{ count($schedules ?? []) }} Data
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="background-color: #1e293b; color: #ffffff;">
                        <th style="padding: 12px 15px; width: 50px; text-align: center;">#</th>
                        <th style="padding: 12px 15px;">Hari</th>
                        <th style="padding: 12px 15px;">Jam ke-</th>
                        <th style="padding: 12px 15px;">Kelas</th>
                        <th style="padding: 12px 15px;">Mata Pelajaran</th>
                        <th style="padding: 12px 15px;">Guru Pengampu</th>
                        @if(in_array(auth()->user()->role ?? '', ['admin', 'administrator', 'wakasek']))
                            <th style="padding: 12px 15px; text-align: center; width: 140px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules ?? [] as $schedule)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 12px 15px; text-align: center; font-weight: 600; color: #64748b;">{{ $loop->iteration }}</td>
                            <td style="padding: 12px 15px;">
                                <span style="background: #f1f5f9; color: #334155; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                    {{ $schedule->day }}
                                </span>
                            </td>
                            <td style="padding: 12px 15px;">
                                <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                    Jam ke-{{ $schedule->time_slot }}
                                </span>
                            </td>
                            <td style="padding: 12px 15px; font-weight: 600; color: #0d6efd;">
                                {{ $schedule->class_name }}
                            </td>
                            <td style="padding: 12px 15px; color: #1e293b; font-weight: 600;">
                                {{ $schedule->subject->name ?? '-' }}
                            </td>
                            <td style="padding: 12px 15px; color: #475569;">
                                {{ $schedule->teacher->name ?? '-' }}
                            </td>
                            @if(in_array(auth()->user()->role ?? '', ['admin', 'administrator', 'wakasek']))
                                <td style="padding: 12px 15px; text-align: center;">
                                    <div style="display: inline-flex; gap: 5px;">
                                        <!-- Tombol Edit -->
                                        <button type="button" 
                                                onclick="openEditModal({{ json_encode($schedule) }})"
                                                style="background: #ffc107; color: #000; border: none; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                            Edit
                                        </button>

                                        <!-- Tombol Hapus -->
                                        @if(Route::has('schedules.destroy'))
                                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')" style="margin: 0;">
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
                            <td colspan="{{ in_array(auth()->user()->role ?? '', ['admin', 'administrator', 'wakasek']) ? '7' : '6' }}" style="padding: 25px; text-align: center; color: #94a3b8;">
                                <em>Tidak ada data jadwal pelajaran yang sesuai dengan kriteria filter pilihan Anda.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL IMPORT EXCEL -->
<div id="importScheduleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; padding: 15px;">
    <div style="background: #ffffff; border-radius: 10px; width: 100%; max-width: 480px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
            <h5 style="margin: 0; font-weight: 700; color: #1e293b;">📥 Import Jadwal dari Excel</h5>
            <button type="button" onclick="closeImportModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #64748b;">✕</button>
        </div>

        @if(Route::has('schedules.import'))
            <form method="POST" action="{{ route('schedules.import') }}" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Pilih File Excel (.xlsx, .xls, .csv)
                    </label>
                    <input type="file" name="file" required accept=".xlsx, .xls, .csv" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem; background-color: #f8fafc;">
                </div>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 6px; font-size: 0.8rem; color: #475569; margin-bottom: 18px; line-height: 1.4;">
                    <strong>ℹ️ Ketentuan File:</strong>
                    <ul style="margin: 4px 0 0 0; padding-left: 18px;">
                        <li>Format yang didukung: Excel (.xlsx, .xls) atau CSV.</li>
                        <li>Pastikan kolom sesuai format: Hari, Kelas, Jam Ke, Mata Pelajaran, Guru.</li>
                    </ul>
                </div>

                <!-- Tombol Aksi -->
                <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                    <button type="button" onclick="closeImportModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">Batal</button>
                    <button type="submit" style="background: #198754; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Upload & Import</button>
                </div>
            </form>
        @else
            <p style="color: #dc3545; font-size: 0.85rem;">Route <code>schedules.import</code> belum didefinisikan di <code>web.php</code>.</p>
        @endif
    </div>
</div>

<!-- MODAL TAMBAH JADWAL -->
<div id="createScheduleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; padding: 15px;">
    <div style="background: #ffffff; border-radius: 10px; width: 100%; max-width: 500px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
            <h5 style="margin: 0; font-weight: 700; color: #1e293b;">➕ Tambah Jadwal Pelajaran</h5>
            <button type="button" onclick="closeCreateModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #64748b;">✕</button>
        </div>

        <form method="POST" action="{{ route('schedules.store') }}">
            @csrf

            <!-- Pilih Kelas -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Kelas</label>
                <select name="class_name" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classList ?? [] as $class)
                        <option value="{{ $class }}">{{ $class }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Hari -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Hari</label>
                <select name="day" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    <option value="">-- Pilih Hari --</option>
                    @foreach($days ?? ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $day)
                        <option value="{{ $day }}">{{ $day }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Jam Ke -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Jam ke-</label>
                <select name="time_slot" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    <option value="">-- Pilih Jam --</option>
                    @foreach($timeSlots ?? range(1,12) as $slot)
                        <option value="{{ $slot }}">Jam ke-{{ $slot }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Mata Pelajaran -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Mata Pelajaran</label>
                <select name="subject_id" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($subjects ?? [] as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Guru Pengampu -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Guru Pengampu</label>
                <select name="teacher_id" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers ?? [] as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                <button type="button" onclick="closeCreateModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" style="background: #0d6efd; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT JADWAL -->
<div id="editScheduleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; padding: 15px;">
    <div style="background: #ffffff; border-radius: 10px; width: 100%; max-width: 500px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
            <h5 style="margin: 0; font-weight: 700; color: #1e293b;">✏️ Edit Jadwal Pelajaran</h5>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #64748b;">✕</button>
        </div>

        <form id="editScheduleForm" method="POST" action="">
            @csrf
            @method('PUT')

            <!-- Pilih Kelas -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Kelas</label>
                <select name="class_name" id="edit_class_name" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    @foreach($classList ?? [] as $class)
                        <option value="{{ $class }}">{{ $class }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Hari -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Hari</label>
                <select name="day" id="edit_day" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    @foreach($days ?? ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $day)
                        <option value="{{ $day }}">{{ $day }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Jam Ke -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Jam ke-</label>
                <select name="time_slot" id="edit_time_slot" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    @foreach($timeSlots ?? range(1,12) as $slot)
                        <option value="{{ $slot }}">Jam ke-{{ $slot }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Mata Pelajaran -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Mata Pelajaran</label>
                <select name="subject_id" id="edit_subject_id" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    @foreach($subjects ?? [] as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Guru Pengampu -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 4px;">Guru Pengampu</label>
                <select name="teacher_id" id="edit_teacher_id" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    @foreach($teachers ?? [] as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                <button type="button" onclick="closeEditModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" style="background: #0d6efd; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT JAVASCRIPT MODAL -->
<script>
    // Modal Import Excel
    function openImportModal() {
        document.getElementById('importScheduleModal').style.display = 'flex';
    }

    function closeImportModal() {
        document.getElementById('importScheduleModal').style.display = 'none';
    }

    // Modal Tambah Jadwal
    function openCreateModal() {
        document.getElementById('createScheduleModal').style.display = 'flex';
    }

    function closeCreateModal() {
        document.getElementById('createScheduleModal').style.display = 'none';
    }

    // Modal Edit Jadwal
    function openEditModal(schedule) {
        const form = document.getElementById('editScheduleForm');
        form.action = `/schedules/${schedule.id}`;

        document.getElementById('edit_class_name').value = schedule.class_name;
        document.getElementById('edit_day').value = schedule.day;
        document.getElementById('edit_time_slot').value = schedule.time_slot;
        document.getElementById('edit_subject_id').value = schedule.subject_id;
        document.getElementById('edit_teacher_id').value = schedule.teacher_id;

        document.getElementById('editScheduleModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editScheduleModal').style.display = 'none';
    }
</script>

</x-app-layout>