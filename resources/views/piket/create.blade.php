<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pencatatan Piket') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tab Navigasi Piket -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex space-x-4 border border-gray-100">
            <a href="{{ route('piket.create') }}" class="flex items-center space-x-2 text-indigo-600 font-semibold px-4 py-2 bg-indigo-50 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Catat Piket Siswa</span>
            </a>
            <a href="{{ route('piket.kbm') }}" class="flex items-center space-x-2 text-gray-500 hover:text-indigo-600 font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span>Piket KBM Kelas &rarr;</span>
            </a>
        </div>

        <!-- Alert Notification -->
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="text-emerald-700 font-medium">{{ session('success') }}</div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="text-red-700 font-medium">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        <!-- Card Form Catat Piket Siswa -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Form Catat Piket Siswa</h3>

            <form action="{{ route('piket.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- 1. Dropdown Pilih Kelas -->
                <div>
                    <label for="class_select" class="block text-sm font-medium text-gray-700 mb-1">
                        Pilih Kelas <span class="text-red-500">*</span>
                    </label>
                    <select id="class_select" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}">{{ $class }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Dropdown Pilih Siswa (Disabled secara default) -->
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Pilih Siswa <span class="text-red-500">*</span>
                    </label>
                    <select id="student_id" name="student_id" required disabled class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-gray-100 cursor-not-allowed">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" data-class="{{ $student->class }}" class="student-option hidden">
                                {{ $student->name }} ({{ $student->class ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Status Piket -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status Piket <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="Terlambat" selected>Terlambat</option>
                        <option value="Izin Keluar">Izin Keluar Kelas</option>
                        <option value="Dispensasi">Dispensasi</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Pulang Awal">Pulang Awal</option>
                    </select>
                </div>

                <!-- 4. Jam Ke- (Mulai) -->
                <div>
                    <label for="start_period" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Ke- (Mulai)
                    </label>
                    <input type="number" id="start_period" name="start_period" value="1" min="1" max="12" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <!-- 5. Jam Ke- (Selesai) -->
                <div>
                    <label for="end_period" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Ke- (Selesai)
                    </label>
                    <input type="number" id="end_period" name="end_period" value="1" min="1" max="12" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <!-- 6. Keterangan / Alasan -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Keterangan / Alasan
                    </label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Masukkan alasan atau catatan detail..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
                        Simpan Catatan Piket
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Filter Siswa berdasarkan Kelas -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const classSelect = document.getElementById('class_select');
            const studentSelect = document.getElementById('student_id');
            const studentOptions = document.querySelectorAll('.student-option');

            classSelect.addEventListener('change', function () {
                const selectedClass = this.value;

                // Reset pilihan siswa
                studentSelect.value = '';

                if (selectedClass) {
                    // Enable dropdown siswa
                    studentSelect.disabled = false;
                    studentSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');

                    // Filter siswa berdasarkan kelas yang dipilih
                    studentOptions.forEach(option => {
                        if (option.getAttribute('data-class') === selectedClass) {
                            option.classList.remove('hidden');
                        } else {
                            option.classList.add('hidden');
                        }
                    });
                } else {
                    // Disable kembali jika kelas dikosongkan
                    studentSelect.disabled = true;
                    studentSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
                    studentOptions.forEach(option => option.classList.add('hidden'));
                }
            });
        });
    </script>
</x-app-layout>