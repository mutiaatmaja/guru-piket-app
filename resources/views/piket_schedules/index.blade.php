@php
    $user = auth()->user();
    // Hanya Admin yang memiliki hak akses mutasi (tambah, edit, hapus, import)
    $isAdmin = $user && $user->role === 'admin';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Guru Piket Rutin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 p-8" x-data="{ showImportModal: false }">
    <div class="max-w-6xl mx-auto">
        
        <!-- Navigasi Menu Atas -->
        <div class="flex items-center justify-between bg-white p-4 rounded-lg shadow-sm mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm flex items-center gap-1">
                    ← Dashboard
                </a>
                <span class="text-gray-300">|</span>
                <span class="font-bold text-gray-800">📅 Jadwal Guru Piket</span>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                @if($isAdmin)
                    <button @click="showImportModal = true" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md font-medium transition flex items-center gap-1 text-xs">
                        📊 Import Excel
                    </button>
                @endif
                <a href="{{ route('teachers.index') }}" class="text-blue-600 hover:underline">Data Guru</a>
                <a href="{{ route('students.index') }}" class="text-blue-600 hover:underline">Data Siswa</a>
                <a href="{{ route('attendance.index') }}" class="text-blue-600 hover:underline">Absensi</a>
            </div>
        </div>

        <!-- Alert Notifikasi Success -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Alert Notifikasi Error -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5 m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 {{ $isAdmin ? 'md:grid-cols-3' : '' }} gap-6">
            
            {{-- Form Tambah HANYA untuk ADMIN --}}
            @if($isAdmin)
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4">➕ Tambah Jadwal Rutin</h2>
                    <form action="{{ route('piket-schedules.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Guru *</label>
                            <select name="teacher_code" required class="block w-full border border-gray-300 rounded-md p-2 bg-white text-sm">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->teacher_code }}">
                                        {{ $teacher->name }} ({{ $teacher->teacher_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hari Piket Rutin *</label>
                            <select name="day_name" required class="block w-full border border-gray-300 rounded-md p-2 bg-white text-sm">
                                <option value="">-- Pilih Hari --</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" required class="block w-full border border-gray-300 rounded-md p-2 bg-white text-sm">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="notes" rows="2" class="block w-full border border-gray-300 rounded-md p-2 text-sm" placeholder="Contoh: Guru Piket 1 atau Guru Piket 2"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 font-semibold text-sm transition">
                            Simpan Jadwal
                        </button>
                    </form>
                </div>
            @endif

            <!-- Tabel Daftar Piket Mingguan -->
            <div class="{{ $isAdmin ? 'md:col-span-2' : 'w-full' }} bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">📋 Daftar Piket Mingguan</h2>
                    <span class="text-xs text-gray-500">Total: {{ count($schedules) }} Jadwal</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b text-gray-700">
                                <th class="p-3">Hari Bertugas</th>
                                <th class="p-3">Nama Guru</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Catatan</th>
                                @if($isAdmin)
                                    <th class="p-3 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($schedules as $schedule)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-3 font-semibold text-blue-600">{{ $schedule->day_name }}</td>
                                    <td class="p-3 font-medium text-gray-800">{{ $schedule->teacher->name ?? $schedule->teacher_code }}</td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $schedule->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-gray-600">{{ $schedule->notes ?? '-' }}</td>
                                    
                                    @if($isAdmin)
                                        <td class="p-3 text-center">
                                            <form action="{{ route('piket-schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal piket ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 px-3 py-1 rounded-md text-xs font-medium transition">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isAdmin ? '5' : '4' }}" class="p-6 text-center text-gray-400">
                                        📂 Belum ada jadwal piket rutin terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL IMPORT EXCEL --}}
    @if($isAdmin)
        <div x-show="showImportModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="showImportModal = false" class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
                <div class="flex justify-between items-center pb-3 border-b mb-4">
                    <h3 class="text-lg font-bold text-gray-800">📊 Import Excel Jadwal Piket</h3>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form action="{{ route('piket-schedules.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel / CSV</label>
                        <input type="file" name="file" accept=".xlsx, .xls, .csv" required 
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-md p-1">
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-6 text-xs text-blue-800">
                        <p class="font-semibold mb-1">Format Kolom Excel:</p>
                        <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900">teacher_code</code> | 
                        <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900">day_name</code> | 
                        <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900">status</code> | 
                        <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900">notes</code>
                        <div class="mt-2 text-gray-600">
                            <em>* <strong>day_name</strong>: Senin, Selasa, dst.<br>* <strong>status</strong>: aktif / nonaktif</em>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showImportModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-md hover:bg-emerald-700 transition">
                            Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</body>
</html>