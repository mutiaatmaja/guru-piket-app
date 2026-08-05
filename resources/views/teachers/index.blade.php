<x-app-layout>
<div style="max-width: 1200px; margin: 30px auto; padding: 0 15px;">
    
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h4 style="margin: 0 0 5px 0; font-weight: 700; color: #1e293b; font-size: 1.5rem;">👨‍🏫 Data Guru & Staff</h4>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">Kelola daftar pengajar dan staf pendidik terdaftar.</p>
        </div>

        <!-- Tombol Aksi HANYA UNTUK ADMIN -->
        @if(auth()->user()->role === 'admin')
            <div style="display: flex; gap: 10px;">
                <button onclick="openModal('importTeacherModal')" style="background-color: #198754; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    📊 Import Excel
                </button>
                <button onclick="openAddTeacherModal()" style="background-color: #0d6efd; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    ➕ Tambah Guru
                </button>
            </div>
        @endif
    </div>

    <!-- Alert Notifikasi Success -->
    @if(session('success'))
        <div style="padding: 12px 15px; background-color: #d1e7dd; color: #0f5132; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; border-left: 4px solid #198754;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Alert Notifikasi Error -->
    @if(session('error'))
        <div style="padding: 12px 15px; background-color: #f8d7da; color: #842029; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; border-left: 4px solid #dc2626;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="padding: 12px 15px; background-color: #f8d7da; color: #842029; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; border-left: 4px solid #dc2626;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter Bar & Search -->
    <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 15px 20px; margin-bottom: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <form action="{{ route('teachers.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari nama, NIP, mapel, atau alamat..." style="padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; width: 320px; max-width: 100%;">
            
            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 0.88rem; cursor: pointer; font-weight: 600;">Cari</button>
            
            @if(request('search'))
                <a href="{{ route('teachers.index') }}" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 6px; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center;">Reset</a>
            @endif
        </form>

        <span style="font-size: 0.85rem; color: #64748b;">
            Total: <strong>{{ method_exists($teachers, 'total') ? $teachers->total() : $teachers->count() }}</strong> Guru
        </span>
    </div>

    <!-- TAMPILAN MODEL CARD (GRID LAYOUT) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 20px; margin-bottom: 25px;">
        @forelse($teachers as $teacher)
            @php
                // Pengecekan apakah data card ini adalah akun guru yang sedang login
                $isSelf = auth()->check() && (
                    auth()->id() === $teacher->user_id || 
                    auth()->user()->email === ($teacher->user->email ?? $teacher->email)
                );
                
                // Hak akses edit: HANYA jika Admin ATAU Pemilik Akun Sendiri
                $canEdit = auth()->user()->role === 'admin' || $isSelf;
            @endphp

            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.04); overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                
                <!-- Card Header / Body -->
                <div style="padding: 20px;">
                    <div style="display: flex; items-center; justify-content: space-between; gap: 12px; margin-bottom: 15px;">
                        <!-- Foto Profil / Kode Guru -->
                        @if($teacher->photo)
                            <img src="{{ asset('storage/' . $teacher->photo) }}" alt="Photo" style="width: 54px; height: 54px; min-width: 54px; min-height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; flex-shrink: 0;">
                        @else
                            <div style="width: 54px; height: 54px; min-width: 54px; min-height: 54px; border-radius: 50%; background: #e0e7ff; color: #3730a3; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem; border: 2px solid #c7d2fe; flex-shrink: 0; text-align: center; overflow: hidden; padding: 2px; box-sizing: border-box;">
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; display: block;" title="{{ $teacher->teacher_code ?? 'GURU' }}">
                                    {{ $teacher->teacher_code ?? 'GURU' }}
                                </span>
                            </div>
                        @endif

                        <!-- Badge Role & Badge Profil Anda -->
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                            <span style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">
                                {{ $teacher->user->role ?? 'guru' }}
                            </span>
                            @if($isSelf)
                                <span style="background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; padding: 2px 8px; border-radius: 12px; font-size: 0.68rem; font-weight: 700;">
                                    👤 Profil Anda
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Nama & NIP -->
                    <h3 style="margin: 0 0 4px 0; font-size: 1rem; font-weight: 700; color: #0f172a; line-height: 1.3;" title="{{ $teacher->name }}">
                        {{ $teacher->name }}
                    </h3>
                    <div style="font-size: 0.8rem; color: #64748b; font-family: monospace; margin-bottom: 12px;">
                        NIP: {{ $teacher->nip ?? '-' }}
                    </div>

                    <!-- Informasi Tambahan -->
                    <div style="border-top: 1px solid #f1f5f9; padding-top: 10px; font-size: 0.82rem; color: #475569; display: flex; flex-direction: column; gap: 6px;">
                        <div>
                            <strong style="color: #1e293b;">Mapel:</strong> 
                            <span style="background: #f0fdf4; color: #166534; padding: 2px 8px; border-radius: 10px; font-weight: 600; font-size: 0.78rem;">
                                {{ $teacher->subject ?? '-' }}
                            </span>
                        </div>
                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $teacher->user->email ?? '-' }}">
                            <strong style="color: #1e293b;">Email:</strong> {{ $teacher->user->email ?? '-' }}
                        </div>
                        <div>
                            <strong style="color: #1e293b;">No HP:</strong> {{ $teacher->phone ?? '-' }}
                        </div>
                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $teacher->address ?? '-' }}">
                            <strong style="color: #1e293b;">Alamat:</strong> {{ $teacher->address ?? '-' }}
                        </div>
                    </div>
                </div>

                <!-- Card Footer Action Buttons -->
                <div style="padding: 12px 20px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                    <!-- Detail Button (SEMUA USER) -->
                    <button onclick="viewTeacher({{ json_encode($teacher->load('user')) }})" title="Detail Guru" style="background: #e0f2fe; border: 1px solid #7dd3fc; color: #0284c7; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                        👁️ Detail
                    </button>

                    <!-- Edit Button (ADMIN atau GURU PEMILIK AKUN) -->
                    @if($canEdit)
                        <button onclick='editTeacher(@json($teacher->load("user")), {{ $isSelf ? "true" : "false" }})' title="Edit Profile" style="background: #fef9c3; border: 1px solid #fde047; color: #ca8a04; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                            ✏️ Edit
                        </button>
                    @endif

                    <!-- Delete Button (HANYA ADMIN & BUKAN AKUN SENDIRI) -->
                    @if(auth()->user()->role === 'admin' && !$isSelf)
                        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Hapus Guru" style="background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600;">
                                🗑️ Hapus
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 40px; background: white; text-align: center; border-radius: 10px; border: 1px solid #e2e8f0; color: #94a3b8;">
                📂 Belum ada data guru ditemukan.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($teachers, 'hasPages') && $teachers->hasPages())
        <div style="padding: 15px; background-color: white; border-radius: 10px; border: 1px solid #e2e8f0;">
            {{ $teachers->links() }}
        </div>
    @endif

</div>

<!-- MODAL FORM: TAMBAH / EDIT GURU -->
<div id="teacherModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; border-radius: 10px; width: 100%; max-width: 650px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 id="modalTitle" style="margin: 0; font-weight: 700;">➕ Tambah Data Guru</h5>
            <span onclick="closeModal('teacherModal')" style="cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</span>
        </div>

        <form id="teacherForm" action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="methodField"></div>
            
            <!-- SECTION AKUN LOGIN -->
            <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 15px;">
                <span style="font-weight: 700; font-size: 0.85rem; color: #334155; display: block; margin-bottom: 10px;">🔐 Kredensial Akun Login User</span>
                
                <div style="display: flex; gap: 12px; margin-bottom: 10px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.82rem;">Email Login *</label>
                        <input type="email" id="email" name="email" required placeholder="guru@sekolah.sch.id" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 0.88rem;">
                    </div>

                    <!-- DROPDOWN ROLE: HANYA BISA DIUBAH OLEH ADMIN -->
                    <div id="roleContainer" style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.82rem;">Role Sistem *</label>
                        @if(auth()->user()->role === 'admin')
                            <select id="role" name="role" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; background: white; font-size: 0.88rem;">
                                <option value="guru">Guru Mapel</option>
                                <option value="guru_piket">Guru Piket</option>
                                <option value="wakasek">Wakasek</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                                <option value="admin">Administrator</option>
                            </select>
                        @else
                            <!-- Tampilan Kunci Role bagi Guru yang mengedit profil pribadinya -->
                            <input type="text" id="role_disabled_display" readonly style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; background: #e2e8f0; font-size: 0.88rem; color: #475569; font-weight: 600; text-transform: uppercase;">
                            <small style="color: #64748b; font-size: 0.72rem;">🔒 Hak akses/role dikunci oleh Administrator.</small>
                        @endif
                    </div>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.82rem;">Password Login</label>
                    <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin diubah (Default: password123)" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 0.88rem;">
                </div>
            </div>

            <!-- SECTION BIODATA GURU -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Nama Lengkap & Gelar *</label>
                <input type="text" id="name" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">NIP</label>
                    <input type="text" id="nip" name="nip" placeholder="Opsional" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">NIK</label>
                    <input type="text" id="nik" name="nik" placeholder="Opsional" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Jenis Kelamin *</label>
                    <select id="gender" name="gender" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; background: white;">
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Agama</label>
                    <input type="text" id="religion" name="religion" placeholder="misal: Islam, Kristen" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Mata Pelajaran *</label>
                    <input type="text" id="subject" name="subject" required placeholder="misal: Matematika" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Pendidikan Terakhir</label>
                    <input type="text" id="last_education" name="last_education" placeholder="misal: S1 Pendidikan" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Tugas Tambahan</label>
                    <input type="text" id="additional_task" name="additional_task" placeholder="misal: Wali Kelas X, Pembina OSIS" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">No. WhatsApp / HP</label>
                    <input type="text" id="phone" name="phone" placeholder="081234567890" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Alamat Lengkap</label>
                <textarea id="address" name="address" rows="2" placeholder="Alamat rumah..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;"></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem;">Foto Profil Guru</label>
                <input type="file" name="photo" accept="image/*" style="width: 100%; padding: 6px 0;">
            </div>

            <div style="text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('teacherModal')" style="background: #e2e8f0; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="background: #0d6efd; color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL IMPORT EXCEL (ADMIN ONLY) -->
@if(auth()->user()->role === 'admin')
<div id="importTeacherModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; border-radius: 10px; width: 100%; max-width: 520px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h5 style="margin: 0; font-weight: 700;">📊 Import Data Guru dari Excel</h5>
            <span onclick="closeModal('importTeacherModal')" style="cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</span>
        </div>

        <div style="background: #e0f2fe; color: #0369a1; padding: 12px; border-radius: 6px; font-size: 0.82rem; margin-bottom: 18px; line-height: 1.5;">
            <strong>Format Header Kolom Excel yang Wajib:</strong><br>
            <code style="background: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold;">nip | nama | email | password | role | mapel | jenis_kelamin | no_hp</code>
            <br><br>
            <small>* Pilihan Role: <strong>admin, guru_piket, guru, wakasek, kepala_sekolah</strong></small><br>
            <small>* Password default jika dikosongkan: <strong>password123</strong></small>
        </div>

        <form action="{{ route('teachers.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.88rem;">Pilih File Excel (.xlsx / .xls / .csv)</label>
                <input type="file" name="file" accept=".xlsx, .xls, .csv" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('importTeacherModal')" style="background: #e2e8f0; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="background: #198754; color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;">Upload & Import</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- MODAL DETAIL GURU (SEMUA ROLE) -->
<div id="viewTeacherModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; border-radius: 10px; width: 100%; max-width: 520px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <h5 style="margin: 0; font-weight: 700;">👁️ Detail Informasi Guru</h5>
            <span onclick="closeModal('viewTeacherModal')" style="cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</span>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <div id="vt_photo_container"></div>
            <h3 id="vt_name" style="margin: 10px 0 2px 0; color: #0f172a;"></h3>
            <span id="vt_role" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"></span>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem; color: #334155;">
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b; width: 40%;">Email Login</td><td style="font-weight: 600;" id="vt_email"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">Mata Pelajaran</td><td style="font-weight: 600;" id="vt_subject"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">NIP</td><td style="font-weight: 600;" id="vt_nip"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">NIK</td><td style="font-weight: 600;" id="vt_nik"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">Jenis Kelamin</td><td style="font-weight: 600;" id="vt_gender"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">Agama</td><td style="font-weight: 600;" id="vt_religion"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">Pendidikan Terakhir</td><td style="font-weight: 600;" id="vt_education"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">Tugas Tambahan</td><td style="font-weight: 600;" id="vt_additional_task"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b;">No. HP/WA</td><td style="font-weight: 600;" id="vt_phone"></td></tr>
            <tr style="border-bottom: 1px solid #f8fafc;"><td style="padding: 8px 0; color: #64748b; vertical-align: top;">Alamat Lengkap</td><td style="font-weight: 600;" id="vt_address"></td></tr>
        </table>

        <div style="text-align: right; margin-top: 20px;">
            <button type="button" onclick="closeModal('viewTeacherModal')" style="background: #e2e8f0; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">Tutup</button>
        </div>
    </div>
</div>

<script>
function openModal(id) {
    var elem = document.getElementById(id);
    if (elem) elem.style.display = 'flex';
}
function closeModal(id) {
    var elem = document.getElementById(id);
    if (elem) elem.style.display = 'none';
}

function openAddTeacherModal() {
    var form = document.getElementById('teacherForm');
    if (!form) return;
    document.getElementById('modalTitle').innerText = '➕ Tambah Data Guru';
    form.action = "{{ route('teachers.store') }}";
    document.getElementById('methodField').innerHTML = '';
    form.reset();
    openModal('teacherModal');
}

function editTeacher(teacher, isSelf) {
    var form = document.getElementById('teacherForm');
    if (!form) return;
    
    document.getElementById('modalTitle').innerText = isSelf ? '✏️ Edit Profil Saya' : '✏️ Edit Data Guru';
    form.action = '/teachers/' + teacher.id;
    document.getElementById('methodField').innerHTML = '{{ method_field("PUT") }}';
    
    // Fill User Auth Info
    document.getElementById('email').value = teacher.user ? teacher.user.email : '';
    
    var roleElem = document.getElementById('role');
    var roleDisabledElem = document.getElementById('role_disabled_display');
    var currentRole = teacher.user ? teacher.user.role : 'guru';

    if (roleElem) {
        roleElem.value = currentRole;
    }
    if (roleDisabledElem) {
        roleDisabledElem.value = currentRole;
    }

    document.getElementById('password').value = ''; // Kosongkan password saat edit
    
    // Fill Teacher Info
    document.getElementById('name').value = teacher.name;
    document.getElementById('nip').value = teacher.nip ?? '';
    document.getElementById('nik').value = teacher.nik ?? '';
    document.getElementById('gender').value = teacher.gender;
    document.getElementById('religion').value = teacher.religion ?? '';
    document.getElementById('subject').value = teacher.subject ?? '';
    document.getElementById('last_education').value = teacher.last_education ?? '';
    document.getElementById('additional_task').value = teacher.additional_task ?? '';
    document.getElementById('phone').value = teacher.phone ?? '';
    document.getElementById('address').value = teacher.address ?? '';
    
    openModal('teacherModal');
}

function viewTeacher(teacher) {
    document.getElementById('vt_name').innerText = teacher.name;
    document.getElementById('vt_role').innerText = teacher.user ? teacher.user.role : 'guru';
    document.getElementById('vt_email').innerText = teacher.user ? teacher.user.email : '-';
    document.getElementById('vt_subject').innerText = teacher.subject ?? '-';
    document.getElementById('vt_nip').innerText = teacher.nip ?? '-';
    document.getElementById('vt_nik').innerText = teacher.nik ?? '-';
    document.getElementById('vt_gender').innerText = teacher.gender === 'P' ? 'Perempuan' : 'Laki-Laki';
    document.getElementById('vt_religion').innerText = teacher.religion ?? '-';
    document.getElementById('vt_education').innerText = teacher.last_education ?? '-';
    document.getElementById('vt_additional_task').innerText = teacher.additional_task ?? '-';
    document.getElementById('vt_phone').innerText = teacher.phone ?? '-';
    document.getElementById('vt_address').innerText = teacher.address ?? '-';
    
    let photoHtml = teacher.photo 
        ? `<img src="/storage/${teacher.photo}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin: auto;">`
        : `<div style="width: 80px; height: 80px; border-radius: 50%; background: #e0e7ff; color: #3730a3; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.8rem; margin: auto;">${teacher.name.charAt(0).toUpperCase()}</div>`;
        
    document.getElementById('vt_photo_container').innerHTML = photoHtml;
    openModal('viewTeacherModal');
}
</script>
</x-app-layout>