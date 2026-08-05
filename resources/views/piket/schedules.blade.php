<x-app-layout>
<div style="max-width: 1100px; margin: 30px auto; padding: 0 15px;">
    
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h4 style="margin: 0 0 5px 0; font-weight: 700; color: #1e293b; font-size: 1.5rem;">📅 Jadwal Guru Piket Harian</h4>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">Kelola penugasan guru piket harian untuk pembatasan dan validasi pencatatan piket.</p>
        </div>
        <div>
            <button onclick="openAddModal()" style="background-color: #0d6efd; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                ➕ Tambah Guru Piket
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
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

    <!-- Card Wrapper Tabel & Filter -->
    <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
        
        <!-- Filter Bar -->
        <div style="padding: 15px 20px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <form action="{{ route('piket-schedules.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
                <input type="date" name="date" value="{{ request('date') }}" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem;">
                
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari nama guru / mapel..." style="padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; width: 260px; max-width: 100%;">
                
                <button type="submit" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 0.88rem; cursor: pointer; font-weight: 600;">Filter</button>
                
                @if(request('date') || request('search'))
                    <a href="{{ route('piket-schedules.index') }}" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 6px; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center;">Reset</a>
                @endif
            </form>

            <span style="font-size: 0.85rem; color: #64748b;">
                Total: <strong>{{ method_exists($schedules, 'total') ? $schedules->total() : $schedules->count() }}</strong> Jadwal
            </span>
        </div>

        <!-- Table Container -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="background-color: #f1f5f9; color: #475569; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 14px 20px; font-weight: 600; width: 50px;">NO</th>
                        <th style="padding: 14px 20px; font-weight: 600;">HARI & TANGGAL</th>
                        <th style="padding: 14px 20px; font-weight: 600;">GURU PIKET</th>
                        <th style="padding: 14px 20px; font-weight: 600;">STATUS</th>
                        <th style="padding: 14px 20px; font-weight: 600;">CATATAN</th>
                        <th style="padding: 14px 20px; font-weight: 600; text-align: center; width: 120px;">AKSI</th>
                    </tr>
                </thead>
                <tbody style="color: #334155;">
                    @forelse($schedules as $index => $item)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 20px;">{{ method_exists($schedules, 'firstItem') ? ($schedules->firstItem() + $index) : ($index + 1) }}</td>
                            <td style="padding: 14px 20px;">
                                <div style="font-weight: 600; color: #0f172a;">{{ $item->day_name }}</div>
                                <div style="font-size: 0.8rem; color: #64748b;">{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}</div>
                            </td>
                            <td style="padding: 14px 20px;">
                                <div style="font-weight: 600; color: #0f172a;">{{ $item->teacher->name ?? '-' }}</div>
                                <div style="font-size: 0.8rem; color: #64748b;">Mapel: {{ $item->teacher->subject ?? '-' }}</div>
                            </td>
                            <td style="padding: 14px 20px;">
                                @if($item->status == 'aktif')
                                    <span style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600;">Bertugas</span>
                                @else
                                    <span style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600;">Non-Aktif</span>
                                @endif
                            </td>
                            <td style="padding: 14px 20px; font-size: 0.85rem; color: #64748b;">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td style="padding: 14px 20px; text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button onclick="editModal({{ json_encode($item) }})" title="Edit" style="background: #fef9c3; border: 1px solid #fde047; padding: 6px 10px; border-radius: 6px; cursor: pointer; color: #ca8a04;">✏️</button>
                                    
                                    <form action="{{ route('piket-schedules.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus penugasan piket ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus" style="background: #fef2f2; border: 1px solid #fca5a5; padding: 6px 10px; border-radius: 6px; cursor: pointer; color: #dc2626;">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 30px; text-align: center; color: #94a3b8;">
                                📂 Belum ada jadwal guru piket yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($schedules, 'hasPages') && $schedules->hasPages())
            <div style="padding: 15px 20px; background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                {{ $schedules->links() }}
            </div>
        @endif

    </div>
</div>

<!-- MODAL: Tambah / Edit Guru Piket -->
<div id="scheduleModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; border-radius: 10px; width: 100%; max-width: 500px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 id="modalTitle" style="margin: 0; font-weight: 700;">➕ Tambah Guru Piket</h5>
            <span onclick="closeModal('scheduleModal')" style="cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</span>
        </div>

        <form id="scheduleForm" action="{{ route('piket-schedules.store') }}" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Pilih Guru *</label>
                <select id="teacher_id" name="teacher_id" required style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; background: white;">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->subject }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Tanggal Tugas Piket *</label>
                <input type="date" id="date" name="date" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div id="statusGroup" style="margin-bottom: 15px; display: none;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Status Penugasan *</label>
                <select id="status" name="status" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; background: white;">
                    <option value="aktif">Bertugas (Aktif)</option>
                    <option value="nonaktif">Non-Aktif</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Catatan Tambahan</label>
                <textarea id="notes" name="notes" rows="2" placeholder="misal: Shift Pagi, Menggantikan Guru X" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;"></textarea>
            </div>

            <div style="text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('scheduleModal')" style="background: #e2e8f0; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="background: #0d6efd; color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function openAddModal() {
    document.getElementById('modalTitle').innerText = '➕ Tambah Guru Piket';
    document.getElementById('scheduleForm').action = "{{ route('piket-schedules.store') }}";
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('scheduleForm').reset();
    document.getElementById('statusGroup').style.display = 'none';
    openModal('scheduleModal');
}

function editModal(item) {
    document.getElementById('modalTitle').innerText = '✏️ Edit Jadwal Guru Piket';
    document.getElementById('scheduleForm').action = '/piket-schedules/' + item.id;
    document.getElementById('methodField').innerHTML = '{{ method_field("PUT") }}';
    
    document.getElementById('teacher_id').value = item.teacher_id;
    document.getElementById('date').value = item.date;
    document.getElementById('status').value = item.status;
    document.getElementById('notes').value = item.notes ?? '';
    
    document.getElementById('statusGroup').style.display = 'block';
    openModal('scheduleModal');
}
</script>
</x-app-layout>