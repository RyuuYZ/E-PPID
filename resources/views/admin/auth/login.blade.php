<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login Petugas - E-PPID Bappeda Ciamis</title>
    <meta name="description" content="Halaman login petugas Sistem E-PPID Bappeda Kabupaten Ciamis">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'navy': { 900: '#0b162c', 800: '#0f1d36', 700: '#152847', 600: '#1e3a5f' },
                        'brand-blue': '#3b82f6',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out both; }
        .animate-fade-in { animation: fadeIn 0.6s ease-out both; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.08);
            animation: float 8s ease-in-out infinite;
        }
        .particle:nth-child(1) { width: 120px; height: 120px; top: 10%; left: 5%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 80px; height: 80px; top: 60%; right: 10%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 60px; height: 60px; bottom: 15%; left: 15%; animation-delay: 4s; }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.5; }
            50% { transform: translateY(-20px) rotate(5deg); opacity: 0.8; }
        }
    </style>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-[1000px] bg-white rounded-3xl shadow-2xl shadow-slate-200/60 flex overflow-hidden min-h-[560px] max-h-[92vh]">

        <!-- Left Panel: Illustration & Branding -->
        <div class="hidden lg:flex lg:w-[45%] relative bg-gradient-to-br from-navy-900 via-navy-800 to-navy-600 flex-col justify-between overflow-hidden">
            <!-- Floating particles -->
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>

            <!-- Top branding -->
            <div class="relative z-10 p-8 pb-0">
                <div class="flex items-center gap-2.5 mb-6 animate-fade-in">
                    <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm p-1.5 flex items-center justify-center border border-white/10">
                        <img alt="Logo" class="w-full h-full object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAtX_U2XLH_I1i15-52gLHX6xO-ZMvIWSEXuwC0Gmqe9FUcJUw02S-fvYKmCl8m5lNarR7LyGNFYzw3C80fG3bLDiLuRnQ_tKrdJgcSt14YJwvn1SDIaPD4lLbpACkSKqDRajmLT0hTdC7q7hOxe2xm7Kwo5VtzSPmlHdRIB5tN_lCE8k42SCkmMmJHeWOayGOsVETHk0HTdmnA8fhIR089BrqE1gkSrqbkEBgtQpw3bHDEhhKbKKJA">
                    </div>
                    <div>
                        <h1 class="text-white text-sm font-bold tracking-tight leading-none">E-PPID</h1>
                        <p class="text-blue-300/70 text-[11px] font-medium mt-0.5">Bappeda Ciamis</p>
                    </div>
                </div>
            </div>

            <!-- Illustration -->
            <div class="relative z-10 flex-1 flex items-center justify-center px-6 animate-fade-in delay-200">
                <img src="{{ asset('images/login-illustration.jpg') }}" alt="Ilustrasi Layanan Informasi" class="w-full max-w-[320px] rounded-2xl object-cover shadow-2xl shadow-black/30 border border-white/5">
            </div>

            <!-- Bottom text -->
            <div class="relative z-10 p-8 pt-4 animate-fade-in delay-300">
                <h2 class="text-white text-lg font-bold tracking-tight mb-1.5">Portal Petugas PPID</h2>
                <p class="text-blue-200/60 text-xs leading-relaxed max-w-[280px]">
                    Sistem layanan informasi publik yang transparan dan akuntabel sesuai standar pelayanan instansi pemerintah.
                </p>
            </div>

            <!-- Subtle gradient overlay at bottom -->
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-navy-900/40 to-transparent pointer-events-none"></div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="w-full lg:w-[55%] flex flex-col justify-center p-6 sm:p-8 lg:px-12 lg:py-8 relative overflow-y-auto" x-data="{ showPassword: false }">
            <!-- Mobile branding -->
            <div class="lg:hidden text-center mb-6 animate-fade-in-up">
                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-navy-900 p-2 flex items-center justify-center shadow-lg">
                    <img alt="Logo" class="w-full h-full object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAtX_U2XLH_I1i15-52gLHX6xO-ZMvIWSEXuwC0Gmqe9FUcJUw02S-fvYKmCl8m5lNarR7LyGNFYzw3C80fG3bLDiLuRnQ_tKrdJgcSt14YJwvn1SDIaPD4lLbpACkSKqDRajmLT0hTdC7q7hOxe2xm7Kwo5VtzSPmlHdRIB5tN_lCE8k42SCkmMmJHeWOayGOsVETHk0HTdmnA8fhIR089BrqE1gkSrqbkEBgtQpw3bHDEhhKbKKJA">
                </div>
            </div>

            <div class="animate-fade-in-up delay-100">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-1 h-6 bg-brand-blue rounded-full"></div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Masuk ke Dashboard</h2>
                </div>
                <p class="text-sm text-slate-500 font-medium ml-3 mb-6">Masukkan kredensial akun petugas Anda.</p>
            </div>

            <!-- Error Messages -->
            @if(session('error'))
                <div class="mb-4 flex items-center gap-2.5 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium animate-fade-in">
                    <span class="material-symbols-outlined text-[18px] text-rose-500 shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @error('username')
                <div class="mb-4 flex items-center gap-2.5 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium animate-fade-in">
                    <span class="material-symbols-outlined text-[18px] text-rose-500 shrink-0">error</span>
                    <span>{{ $message }}</span>
                </div>
            @enderror
            @error('cf-turnstile-response')
                <div class="mb-4 flex items-center gap-2.5 p-3 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-xl font-medium animate-fade-in">
                    <span class="material-symbols-outlined text-[18px] text-amber-500 shrink-0">warning</span>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <form action="{{ route('admin.authenticate') }}" method="POST" class="space-y-4 animate-fade-in-up delay-200">
                @csrf

                <!-- Username / Email -->
                <div>
                    <label for="username" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Username atau Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                        </div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                            class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
                            placeholder="Masukkan username atau email Anda">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">lock</span>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                            class="block w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
                            placeholder="Masukkan kata sandi">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <span class="material-symbols-outlined text-[18px]" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2.5 pt-1">
                    <input id="remember" name="remember" type="checkbox"
                        class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer transition-colors">
                    <label for="remember" class="text-xs font-medium text-slate-600 cursor-pointer select-none">
                        Ingat saya selama 30 hari
                    </label>
                </div>

                <!-- Turnstile -->
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>

                <!-- Submit -->
                <div class="pt-1 animate-fade-in-up delay-300">
                    <button type="submit" class="w-full bg-navy-900 text-white font-bold rounded-xl py-3 px-4 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-900 transition-all duration-200 text-sm shadow-lg shadow-navy-900/20 active:scale-[0.98]">
                        <span class="flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">login</span>
                            Masuk
                        </span>
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center animate-fade-in delay-400">
                <p class="text-[11px] text-slate-400 font-medium">
                    &copy; {{ date('Y') }} E-PPID Bappeda Kabupaten Ciamis
                </p>
                <p class="text-[10px] text-slate-300 mt-1">
                    Sistem Pejabat Pengelola Informasi & Dokumentasi
                </p>
            </div>
        </div>
    </div>

</body>
</html>
