<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Shortcut Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-smkn7.png') }}">    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sistem Informasi Sekolah') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    <!-- NAVBAR -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Logo / Brand Title -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-blue-500/20">
                    🏫
                </div>
                <div>
                    <span class="text-lg font-bold bg-gradient-to-r from-blue-700 to-indigo-700 bg-clip-text text-transparent">
                        SIM-SEKOLAH
                    </span>
                    <p class="text-[10px] text-slate-400 font-medium tracking-wider uppercase -mt-1 hidden sm:block">
                        Sistem Informasi & Piket
                    </p>
                </div>
            </div>

            <!-- Navigation Auth Links -->
            <nav class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-all shadow-md shadow-blue-500/20">
                            <span>Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-all shadow-md shadow-blue-500/25 hover:shadow-lg">
                            <span>Masuk / Login</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        </a>
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <!-- HERO SECTION -->
    <main class="flex-grow flex items-center justify-center relative overflow-hidden py-12 md:py-20">
        <!-- Background Decorative Elements -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Column: Text & CTA -->
                <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        Sistem Manajemen Digital Terpadu
                    </div>

                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-tight tracking-tight">
                        SIPIKET <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Guru & Siswa</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                        Kelola data pendidik, jadwal piket harian, rekap absensi siswa, dan administrasi sekolah secara efisien, terstruktur, dan akurat dalam satu platform terpadu.
                    </p>

                    <!-- Buttons Group -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-center shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5">
                                    Buka Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-center shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <span>Masuk ke Akun</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            @endauth
                        @endif
                    </div>

                    <!-- Highlights Badge -->
                    <div class="pt-6 grid grid-cols-3 gap-4 border-t border-slate-200/60 max-w-lg mx-auto lg:mx-0">
                        <div class="text-center lg:text-left">
                            <span class="block text-xl sm:text-2xl font-bold text-slate-800">100%</span>
                            <span class="text-xs text-slate-500 font-medium">Digital & Paperless</span>
                        </div>
                        <div class="text-center lg:text-left">
                            <span class="block text-xl sm:text-2xl font-bold text-slate-800">Realtime</span>
                            <span class="text-xs text-slate-500 font-medium">Rekapitulasi Absensi</span>
                        </div>
                        <div class="text-center lg:text-left">
                            <span class="block text-xl sm:text-2xl font-bold text-slate-800">Aman</span>
                            <span class="text-xs text-slate-500 font-medium">Akses Berbasis Role</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visual Card / Dashboard Mockup -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Shadow Glow -->
                        <div class="absolute -inset-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl blur opacity-20"></div>
                        
                        <!-- Card Container -->
                        <div class="relative bg-white border border-slate-100 rounded-2xl shadow-xl p-6 space-y-5">
                            
                            <!-- Card Header Mock -->
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                        📋
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-800">Piket Hari Ini</h3>
                                        <p class="text-xs text-slate-400">Jadwal & Presensi Realtime</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 text-[11px] font-semibold bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100">
                                    ● Aktif
                                </span>
                            </div>

                            <!-- Features List -->
                            <div class="space-y-3">
                                <div class="p-3.5 bg-slate-50 rounded-xl flex items-center gap-3 border border-slate-100">
                                    <span class="text-xl">👨‍🏫</span>
                                    <div class="flex-grow">
                                        <h4 class="text-xs font-bold text-slate-700">Manajemen Data Guru</h4>
                                        <p class="text-[11px] text-slate-500">Pencatatan NIP, Mapel, dan Kontak Guru</p>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-slate-50 rounded-xl flex items-center gap-3 border border-slate-100">
                                    <span class="text-xl">🎓</span>
                                    <div class="flex-grow">
                                        <h4 class="text-xs font-bold text-slate-700">Data Siswa & Kelas</h4>
                                        <p class="text-[11px] text-slate-500">Integrasi NISN dan rombel kelas</p>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-slate-50 rounded-xl flex items-center gap-3 border border-slate-100">
                                    <span class="text-xl">📅</span>
                                    <div class="flex-grow">
                                        <h4 class="text-xs font-bold text-slate-700">Jadwal Piket Mingguan</h4>
                                        <p class="text-[11px] text-slate-500">Penugasan guru piket harian otomatis</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Card Action -->
                            <div class="pt-2">
                                <a href="{{ route('login') }}" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold text-center block transition">
                                    Masuk untuk Mengelola →
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-100 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} Sistem Informasi Sekolah. Hak Cipta Dilindungi.</p>
            <p class="flex items-center gap-1">
                <span>Dikembangkan untuk Efisiensi Administrasi Sekolah</span>
            </p>
        </div>
    </footer>

</body>
</html>