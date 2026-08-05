<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Manajemen Pengguna (Admin)') }}
            </h2>
            
            <!-- Tombol Import Excel -->
            <div class="flex items-center gap-2">
                <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Import Excel</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Notifikasi Pesan Sukses/Gagal -->
            @if (session('success'))
                <div class="p-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2">
                    <span>✅</span> <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200 flex items-center gap-2">
                    <span>⚠️</span> <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- CARD FORM TAMBAH PENGGUNA -->
            <div class="p-6 sm:p-8 bg-white shadow-sm border border-slate-200/80 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span>➕</span> Tambah Pengguna Baru
                </h3>

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama..." required class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" autocomplete="off" placeholder="contoh@sekolah.sch.id" required class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password *</label>
                            <input type="password" name="password" placeholder="••••••••" required class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Pilih Role *</label>
                            <select name="role" required class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                <option value="guru_piket" {{ old('role') == 'guru_piket' ? 'selected' : '' }}>Guru Piket</option>
                                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru / Guru Mapel</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="wakasek" {{ old('role') == 'wakasek' ? 'selected' : '' }}>Wakasek Kesiswaan</option>
                                <option value="kepala_sekolah" {{ old('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                            </select>
                        </div>

                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR USER -->
            <div class="bg-white shadow-sm border border-slate-200/80 rounded-2xl overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">Daftar Pengguna Terdaftar</h3>
                    <span class="text-xs text-slate-500 font-medium">Total: {{ count($users) }} Pengguna</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">User</th>
                                <th class="px-6 py-3.5">Email</th>
                                <th class="px-6 py-3.5">Role</th>
                                <th class="px-6 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 font-bold text-sm flex items-center justify-center border border-indigo-100">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-slate-900">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->role === 'admin')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-red-50 text-red-700 rounded-full border border-red-200">
                                                ADMIN
                                            </span>
                                        @elseif($user->role === 'guru_piket')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 rounded-full border border-amber-200">
                                                GURU PIKET
                                            </span>
                                        @elseif($user->role === 'guru')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-blue-50 text-blue-700 rounded-full border border-blue-200">
                                                GURU
                                            </span>
                                        @elseif($user->role === 'kepala_sekolah')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                                                KEPALA SEKOLAH
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-bold bg-indigo-50 text-indigo-700 rounded-full border border-indigo-200">
                                                {{ str_replace('_', ' ', strtoupper($user->role)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
                                        <!-- Edit -->
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition">
                                            Edit
                                        </a>

                                        <!-- Hapus -->
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 italic px-2">(Saya)</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                        Belum ada pengguna terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL IMPORT EXCEL -->
    <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl relative border border-slate-100">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <span>📊</span> Import Users dari Excel
                </h3>
                <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-xs text-blue-800 mb-4 leading-relaxed">
                <strong>Ketentuan Kolom Excel:</strong><br>
                Baris pertama/header wajib memiliki kolom: <br>
                <code class="bg-white px-1 py-0.5 rounded font-mono text-blue-900 border">name</code> | 
                <code class="bg-white px-1 py-0.5 rounded font-mono text-blue-900 border">email</code> | 
                <code class="bg-white px-1 py-0.5 rounded font-mono text-blue-900 border">password</code> | 
                <code class="bg-white px-1 py-0.5 rounded font-mono text-blue-900 border">role</code>
            </div>

            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Upload File (.xlsx / .xls / .csv)</label>
                    <input type="file" name="file" accept=".xlsx, .xls, .csv" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-200 rounded-lg cursor-pointer">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                        Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Javascript Handlers Modal -->
    <script>
        function openImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>