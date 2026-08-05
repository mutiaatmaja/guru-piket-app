<x-app-layout>
<div style="max-width: 1100px; margin: 30px auto; padding: 0 15px;">
    
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <!-- Logo SMKN 7 Pontianak dari Asset Lokal -->
            <img src="{{ asset('images/logo-smkn7.png') }}" alt="Logo SMKN 7 PTK" style="height: 48px; width: auto; object-fit: contain;">
            <div>
                <h4 style="margin: 0 0 3px 0; font-weight: 700; color: #1e293b; font-size: 1.5rem;">🎓 Data Siswa</h4>
                <p style="margin: 0; color: #64748b; font-size: 0.9rem;">Kelola daftar siswa terdaftar SMKN 7 Pontianak.</p>
            </div>
        </div>

        <!-- Tombol Tambah & Import Siswa HANYA UNTUK ADMIN -->
        @if(in_array(auth()->user()->role, ['admin', 'administrator']))
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="button" onclick="openImportModal()" style="background-color: #10b981; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    📥 Import Excel
                </button>
                <button type="button" onclick="openAddStudentModal()" style="background-color: #0d6efd; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    ➕ Tambah Siswa
                </button>
            </div>
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
        
        <!-- Filter Bar (Pencarian & Filter Kelas) -->
        <div style="padding: 15px 20px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <form action="{{ route('students.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1; align-items: center;">
                
                <!-- Filter Dropdown Pilih Kelas -->
                <select name="class" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; background-color: #ffffff; color: #334155; font-weight: 500; cursor: pointer; min-width: 180px; width: 200px; max-width: 100%;">
                    <option value="">🏫 Semua Kelas</option>
                    @if(isset($classList))
                        @foreach($classList as $c)
                            <option value="{{ $c }}" {{ request('class') == $c ? 'selected' : '' }}>
                                Kelas {{ $c }}
                            </option>
                        @endforeach
                    @endif
                </select>

                <!-- Input Cari Nama / NISN -->
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari nama, NISN, atau NIS..." style="padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; width: 260px; max-width: 100%;">
                
                <button type="submit" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 0.88rem; cursor: pointer; font-weight: 600;">Cari</button>
                
                @if(request('search') || request('class'))
                    <a href="{{ route('students.index') }}" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 6px; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center;">Reset Filter</a>
                @endif
            </form>

            <span style="font-size: 0.85rem; color: #64748b;">
                Total: <strong>{{ method_exists($students, 'total') ? $students->total() : $students->count() }}</strong> Siswa
            </span>
        </div>

        <!-- Table Container -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="background-color: #f1f5f9; color: #475569; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 14px 20px; font-weight: 600; width: 50px;">NO</th>
                        <th style="padding: 14px 20px; font-weight: 600; width: 60px;">FOTO</th>
                        <th style="padding: 14px 20px; font-weight: 600;">NAMA & NISN</th>
                        <th style="padding: 14px 20px; font-weight: 600;">KELAS</th>
                        <th style="padding: 14px 20px; font-weight: 600;">GENDER</th>
                        <th style="padding: 14px 20px; font-weight: 600;">NO HP WALI</th>
                        <th style="padding: 14px 20px; font-weight: 600; text-align: center; width: 140px;">AKSI</th>
                    </tr>
                </thead>
                <tbody style="color: #334155;">
                    @forelse($students as $index => $student)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 20px;">{{ method_exists($students, 'firstItem') ? ($students->firstItem() + $index) : ($index + 1) }}</td>
                            <td style="padding: 14px 20px;">
                                @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" alt="Photo" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">
                                @else
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem;">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px 20px;">
                                <div style="font-weight: 600; color: #0f172a;">{{ $student->name }}</div>
                                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                    NISN: {{ $student->nisn ?? '-' }}
                                </div>
                            </td>
                            <td style="padding: 14px 20px;">
                                <a href="{{ route('students.index', ['class' => $student->class]) }}" title="Klik untuk filter kelas ini" style="text-decoration: none;">
                                    <span style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                        {{ $student->class }}
                                    </span>
                                </a>
                            </td>
                            <td style="padding: 14px 20px; font-size: 0.85rem;">
                                {{ $student->gender == 'P' ? 'Perempuan' : 'Laki-laki' }}
                            </td>
                            <td style="padding: 14px 20px; font-size: 0.85rem;">
                                {{ $student->parent_phone ?? '-' }}
                            </td>
                            <td style="padding: 14px 20px; text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    @php
                                        $encodedStudent = base64_encode(json_encode($student));
                                    @endphp

                                    <button type="button" onclick="viewStudent('{{ $encodedStudent }}')" title="Detail" style="background: #e0f2fe; border: 1px solid #7dd3fc; padding: 6px 10px; border-radius: 6px; cursor: pointer; color: #0284c7;">👁️</button>
                                    
                                    @if(in_array(auth()->user()->role, ['admin', 'administrator']))
                                        <button type="button" onclick="editStudent('{{ $encodedStudent }}')" title="Edit" style="background: #fef9c3; border: 1px solid #fde047; padding: 6px 10px; border-radius: 6px; cursor: pointer; color: #ca8a04;">✏️</button>
                                        
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" style="background: #fef2f2; border: 1px solid #fca5a5; padding: 6px 10px; border-radius: 6px; cursor: pointer; color: #dc2626;">🗑️</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 30px; text-align: center; color: #94a3b8;">
                                📂 Belum ada data siswa ditemukan @if(request('class')) untuk kelas <strong>{{ request('class') }}</strong> @endif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($students, 'hasPages') && $students->hasPages())
            <div style="padding: 15px 20px; background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                {{ $students->links() }}
            </div>
        @endif

    </div>
</div>

<!-- MODAL 1: Tambah / Edit Siswa -->
@if(in_array(auth()->user()->role, ['admin', 'administrator']))
<div id="studentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; border-radius: 10px; width: 100%; max-width: 600px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 id="modalTitle" style="margin: 0; font-weight: 700;">➕ Tambah Data Siswa</h5>
            <span onclick="closeModal('studentModal')" style="cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</span>
        </div>

        <form id="studentForm" action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="methodField"></div>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Nama Lengkap Siswa *</label>
                <input type="text" id="name" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">NISN</label>
                    <input type="text" id="nisn" name="nisn" placeholder="Opsional" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">NIS / NIK</label>
                    <input type="text" id="nis" name="nis" placeholder="Opsional" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Kelas *</label>
                    <input type="text" id="class" name="class" required placeholder="misal: X RPL 1" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Jenis Kelamin *</label>
                    <select id="gender" name="gender" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; background: white;">
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Tempat Lahir</label>
                    <input type="text" id="birth_place" name="birth_place" placeholder="Kota Kelahiran" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Agama</label>
                    <input type="text" id="religion" name="religion" placeholder="misal: Islam, Kristen" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Nama Orang Tua / Wali</label>
                    <input type="text" id="parent_name" name="parent_name" placeholder="Nama Ayah/Ibu/Wali" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">No. HP Orang Tua / Wali</label>
                <input type="text" id="parent_phone" name="parent_phone" placeholder="081234567890" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Alamat Rumah</label>
                <textarea id="address" name="address" rows="2" placeholder="Alamat lengkap tempat tinggal..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;"></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Foto Siswa</label>
                <input type="file" name="photo" accept="image/*" style="width: 100%; padding: 6px 0;">
            </div>

            <div style="text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('studentModal')" style="background: #e2e8f0; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="background: #0d6efd; color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: Import Data Siswa dari Excel/CSV -->
<div id="importStudentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; border-radius: 10px; width: 100%; max-width: 500px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <h5 style="margin: 0; font-weight: 700; color: #1e293b;">📥 Import Data Siswa (Excel / CSV)</h5>
            <span onclick="closeModal('importStudentModal')" style="cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</span>
        </div>

        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem; color: #334155;">Pilih File Excel (.xlsx, .xls) atau CSV *</label>
                <input type="file" name="file" accept=".xlsx, .xls, .csv" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="background-color: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 20px; font-size: 0.8rem; color: #64748b;">
                <strong style="color: #334155; display: block; margin-bottom: 4px;">📌 Ketentuan Format Kolom Excel:</strong>
                Urutan kolom header minimal: <strong>Name</strong>, <strong>Class</strong>, <strong>Gender</strong> (L/P), <strong>NISN</strong>, <strong>NIS</strong>, <strong>Parent Phone</strong>.
            </div>

            <div style="text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('importStudentModal')" style="background: #e2e8f0; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Upload & Import</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- MODAL 3: View Detail Siswa -->
<div id="viewStudentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; border-radius: 10px; width: 100%; max-width: 520px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <h5 style="margin: 0; font-weight: 700;">👁️ Detail Informasi Siswa</h5>
            <span onclick="closeModal('viewStudentModal')" style="cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</span>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <div id="vs_photo_container"></div>
            <h3 id="vs_name" style="margin: 10px 0 2px 0; color: #0f172a;"></h3>
            <span id="vs_class" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 3px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;"></span>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem; color: #334155;">
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b; width: 40%;">NISN</td><td style="font-weight: 600;" id="vs_nisn"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">NIS</td><td style="font-weight: 600;" id="vs_nis"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">Jenis Kelamin</td><td style="font-weight: 600;" id="vs_gender"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">Tempat, Tgl Lahir</td><td style="font-weight: 600;" id="vs_ttl"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">Agama</td><td style="font-weight: 600;" id="vs_religion"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">Nama Orang Tua / Wali</td><td style="font-weight: 600;" id="vs_parent_name"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">No. HP Orang Tua / WA</td><td style="font-weight: 600;" id="vs_parent_phone"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b; vertical-align: top;">Alamat Rumah</td><td style="font-weight: 600;" id="vs_address"></td></tr>
        </table>

        <div style="text-align: right; margin-top: 20px;">
            <button type="button" onclick="closeModal('viewStudentModal')" style="background: #e2e8f0; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">Tutup</button>
        </div>
    </div>
</div>

<script>
// Menghitung Base URL asset secara aman agar kompatibel dengan Ngrok HTTPS
const STORAGE_BASE_URL = "{{ asset('storage') }}";

function parseStudentData(encodedData) {
    try {
        return JSON.parse(atob(encodedData));
    } catch (e) {
        console.error('Failed to parse student data', e);
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

function openAddStudentModal() {
    var form = document.getElementById('studentForm');
    if (!form) return;
    document.getElementById('modalTitle').innerText = '➕ Tambah Data Siswa';
    form.action = "{{ route('students.store') }}";
    document.getElementById('methodField').innerHTML = '';
    form.reset();
    openModal('studentModal');
}

function openImportModal() {
    openModal('importStudentModal');
}

function editStudent(encodedStudent) {
    var student = parseStudentData(encodedStudent);
    var form = document.getElementById('studentForm');
    if (!form) return;
    
    document.getElementById('modalTitle').innerText = '✏️ Edit Data Siswa';
    form.action = '/students/' + student.id;
    document.getElementById('methodField').innerHTML = '{{ method_field("PUT") }}';
    
    document.getElementById('name').value = student.name || '';
    document.getElementById('nisn').value = student.nisn || '';
    document.getElementById('nis').value = student.nis || '';
    document.getElementById('class').value = student.class || '';
    document.getElementById('gender').value = student.gender || 'L';
    document.getElementById('birth_place').value = student.birth_place || '';
    document.getElementById('birth_date').value = student.birth_date || '';
    document.getElementById('religion').value = student.religion || '';
    document.getElementById('parent_name').value = student.parent_name || '';
    document.getElementById('parent_phone').value = student.parent_phone || '';
    document.getElementById('address').value = student.address || '';
    
    openModal('studentModal');
}

function viewStudent(encodedStudent) {
    var student = parseStudentData(encodedStudent);

    document.getElementById('vs_name').innerText = student.name || '-';
    document.getElementById('vs_class').innerText = student.class || '-';
    document.getElementById('vs_nisn').innerText = student.nisn || '-';
    document.getElementById('vs_nis').innerText = student.nis || '-';
    document.getElementById('vs_gender').innerText = student.gender === 'P' ? 'Perempuan' : 'Laki-Laki';
    
    let ttl = '-';
    if (student.birth_place || student.birth_date) {
        ttl = (student.birth_place || '') + (student.birth_place && student.birth_date ? ', ' : '') + (student.birth_date || '');
    }
    document.getElementById('vs_ttl').innerText = ttl;

    document.getElementById('vs_religion').innerText = student.religion || '-';
    document.getElementById('vs_parent_name').innerText = student.parent_name || '-';
    document.getElementById('vs_parent_phone').innerText = student.parent_phone || '-';
    document.getElementById('vs_address').innerText = student.address || '-';
    
    let photoHtml = student.photo 
        ? `<img src="${STORAGE_BASE_URL}/${student.photo}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin: auto;">`
        : `<div style="width: 80px; height: 80px; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.8rem; margin: auto;">${(student.name || 'S').charAt(0).toUpperCase()}</div>`;
        
    document.getElementById('vs_photo_container').innerHTML = photoHtml;
    openModal('viewStudentModal');
}
</script>
</x-app-layout>