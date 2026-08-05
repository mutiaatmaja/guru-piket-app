<x-app-layout>
<div style="max-width: 1100px; margin: 30px auto; padding: 0 15px;">
    
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="{{ asset('images/logo-smkn7.png') }}" alt="Logo SMKN 7 PTK" style="height: 48px; width: auto; object-fit: contain;">
            <div>
                <h4 style="margin: 0 0 3px 0; font-weight: 700; color: #1e293b; font-size: 1.5rem;">📚 Data Mata Pelajaran</h4>
                <p style="margin: 0; color: #64748b; font-size: 0.9rem;">Kelola kurikulum dan daftar mata pelajaran SMKN 7 Pontianak.</p>
            </div>
        </div>

        <!-- Tombol Tambah HANYA UNTUK ADMIN -->
        @if(in_array(auth()->user()->role, ['admin', 'administrator']))
            <button type="button" onclick="openAddSubjectModal()" style="background-color: #0d6efd; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                ➕ Tambah Mapel
            </button>
        @endif
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

    @if($errors->any())
        <div style="padding: 12px 15px; background-color: #f8d7da; color: #842029; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Wrapper Tabel & Filter -->
    <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
        
        <!-- Filter Bar (Pencarian Mapel) -->
        <div style="padding: 15px 20px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <form action="{{ route('subjects.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1; align-items: center;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari kode atau nama mapel..." style="padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; width: 280px; max-width: 100%;">
                
                <button type="submit" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 0.88rem; cursor: pointer; font-weight: 600;">Cari</button>
                
                @if(request('search'))
                    <a href="{{ route('subjects.index') }}" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 6px; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center;">Reset Filter</a>
                @endif
            </form>

            <span style="font-size: 0.85rem; color: #64748b;">
                Total: <strong>{{ method_exists($subjects, 'total') ? $subjects->total() : $subjects->count() }}</strong> Mata Pelajaran
            </span>
        </div>

        <!-- Table Container -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="background-color: #f1f5f9; color: #475569; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 14px 20px; font-weight: 600; width: 60px;">NO</th>
                        <th style="padding: 14px 20px; font-weight: 600; width: 150px;">KODE MAPEL</th>
                        <th style="padding: 14px 20px; font-weight: 600;">NAMA MATA PELAJARAN</th>
                        <th style="padding: 14px 20px; font-weight: 600; text-align: center; width: 140px;">BEBAN (JP)</th>
                        @if(in_array(auth()->user()->role, ['admin', 'administrator']))
                            <th style="padding: 14px 20px; font-weight: 600; text-align: center; width: 120px;">AKSI</th>
                        @endif
                    </tr>
                </thead>
                <tbody style="color: #334155;">
                    @forelse($subjects as $index => $subject)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 20px;">{{ method_exists($subjects, 'firstItem') ? ($subjects->firstItem() + $index) : ($index + 1) }}</td>
                            <td style="padding: 14px 20px;">
                                <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 4px; font-size: 0.82rem; font-weight: 700;">
                                    {{ $subject->code }}
                                </span>
                            </td>
                            <td style="padding: 14px 20px; font-weight: 600; color: #0f172a;">
                                {{ $subject->name }}
                            </td>
                            <td style="padding: 14px 20px; text-align: center;">
                                <span style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 2px 8px; border-radius: 12px; font-size: 0.82rem; font-weight: 600;">
                                    {{ $subject->credit_hours }} Jam / Minggu
                                </span>
                            </td>
                            @if(in_array(auth()->user()->role, ['admin', 'administrator']))
                                <td style="padding: 14px 20px; text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        @php
                                            $encodedSubject = base64_encode(json_encode($subject));
                                        @endphp

                                        <button type="button" onclick="editSubject('{{ $encodedSubject }}')" title="Edit" style="background: #fef9c3; border: 1px solid #fde047; padding: 6px 10px; border-radius: 6px; cursor: pointer; color: #ca8a04;">✏️</button>
                                        
                                        <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" style="background: #fef2f2; border: 1px solid #fca5a5; padding: 6px 10px; border-radius: 6px; cursor: pointer; color: #dc2626;">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array(auth()->user()->role, ['admin', 'administrator']) ? '5' : '4' }}" style="padding: 30px; text-align: center; color: #94a3b8;">
                                📂 Belum ada data mata pelajaran ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($subjects, 'hasPages') && $subjects->hasPages())
            <div style="padding: 15px 20px; background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                {{ $subjects->links() }}
            </div>
        @endif

    </div>
</div>

<!-- MODAL: Tambah / Edit Mata Pelajaran -->
@if(in_array(auth()->user()->role, ['admin', 'administrator']))
<div id="subjectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; border-radius: 10px; width: 100%; max-width: 450px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <h5 id="modalTitle" style="margin: 0; font-weight: 700; color: #1e293b;">➕ Tambah Mata Pelajaran</h5>
            <span onclick="closeModal('subjectModal')" style="cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</span>
        </div>

        <form id="subjectForm" action="{{ route('subjects.store') }}" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Kode Mapel *</label>
                <input type="text" id="code" name="code" required placeholder="misal: MTK-X, BIN-XI" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Nama Mata Pelajaran *</label>
                <input type="text" id="name" name="name" required placeholder="misal: Matematika" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Beban Jam Pelajaran (JP/Minggu) *</label>
                <input type="number" id="credit_hours" name="credit_hours" min="1" value="2" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('subjectModal')" style="background: #e2e8f0; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="background: #0d6efd; color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function parseSubjectData(encodedData) {
    try {
        return JSON.parse(atob(encodedData));
    } catch (e) {
        console.error('Failed to parse subject data', e);
        return {};
    }
}

function openModal(id) {
    var elem = document.getElementById(id);
    if (elem) elem.style.display = 'flex';
}

function closeModal(id) {
    var elem = document.getElementById(id);
    if (elem) elem.style.display = 'none';
}

function openAddSubjectModal() {
    var form = document.getElementById('subjectForm');
    if (!form) return;
    document.getElementById('modalTitle').innerText = '➕ Tambah Mata Pelajaran';
    form.action = "{{ route('subjects.store') }}";
    document.getElementById('methodField').innerHTML = '';
    form.reset();
    document.getElementById('credit_hours').value = 2;
    openModal('subjectModal');
}

function editSubject(encodedSubject) {
    var subject = parseSubjectData(encodedSubject);
    var form = document.getElementById('subjectForm');
    if (!form) return;
    
    document.getElementById('modalTitle').innerText = '✏️ Edit Mata Pelajaran';
    form.action = '/subjects/' + subject.id;
    document.getElementById('methodField').innerHTML = '{{ method_field("PUT") }}';
    
    document.getElementById('code').value = subject.code || '';
    document.getElementById('name').value = subject.name || '';
    document.getElementById('credit_hours').value = subject.credit_hours || 2;
    
    openModal('subjectModal');
}
</script>
</x-app-layout>