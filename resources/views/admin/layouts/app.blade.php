<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Dashboard - Sistem E-PPID Bappeda</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Alpine.js with Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "secondary-fixed": "#ffdf9f",
                        "on-error": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "primary": "#03224d",
                        "surface-container-highest": "#e0e3e5",
                        "background": "#f7f9fb",
                        "tertiary-fixed": "#d3e4fe",
                        "surface-bright": "#f7f9fb",
                        "on-secondary-container": "#6f5100",
                        "on-surface-variant": "#44474f",
                        "surface-container": "#eceef0",
                        "inverse-primary": "#afc6fb",
                        "primary-container": "#1f3864",
                        "on-tertiary-fixed": "#0b1c30",
                        "outline-variant": "#c4c6d0",
                        "on-surface": "#191c1e",
                        "inverse-on-surface": "#eff1f3",
                        "tertiary": "#142438",
                        "surface-dim": "#d8dadc",
                        "surface": "#f7f9fb",
                        "secondary": "#795900",
                        "surface-variant": "#e0e3e5",
                        "primary-fixed-dim": "#afc6fb",
                        "on-error-container": "#93000a",
                        "on-tertiary": "#ffffff",
                        "primary-fixed": "#d8e2ff",
                        "on-secondary-fixed-variant": "#5c4300",
                        "surface-container-low": "#f2f4f6",
                        "surface-container-high": "#e6e8ea",
                        "tertiary-fixed-dim": "#b7c8e1",
                        "on-primary-container": "#8ba2d5",
                        "error": "#ba1a1a",
                        "on-primary": "#ffffff",
                        "outline": "#747780",
                        "tertiary-container": "#2a3a4e",
                        "on-background": "#191c1e",
                        "on-secondary-fixed": "#261a00",
                        "error-container": "#ffdad6",
                        "on-primary-fixed": "#001a41",
                        "on-secondary": "#ffffff",
                        "secondary-fixed-dim": "#f9bd22",
                        "on-primary-fixed-variant": "#2e4673",
                        "surface-tint": "#475e8c",
                        "inverse-surface": "#2d3133",
                        "secondary-container": "#ffc329",
                        "on-tertiary-fixed-variant": "#38485d",
                        "on-tertiary-container": "#93a4bc"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-desktop": "48px",
                        "stack-sm": "8px",
                        "stack-lg": "32px",
                        "container-max": "1280px",
                        "unit": "8px",
                        "margin-mobile": "16px",
                        "gutter": "24px",
                        "stack-md": "16px"
                    },
                    "fontFamily": {
                        "display-lg": ["Inter"],
                        "label-sm": ["Inter"],
                        "headline-md": ["Inter"],
                        "headline-lg": ["Inter"],
                        "label-md": ["Inter"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "headline-lg-mobile": ["Inter"]
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
        
        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        .sidebar-scroll:hover::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-surface text-on-surface flex min-h-screen">
    
    <!-- SideNavBar -->
    <nav class="hidden md:flex h-screen w-[240px] flex-col fixed left-0 top-0 bg-[#1a2b42] text-gray-300 shadow-xl z-20">
        <!-- Brand Header -->
        <div class="px-5 py-4 flex items-center gap-3 border-b border-gray-700/50">
            <div class="w-8 h-8 rounded bg-white p-1 flex items-center justify-center shrink-0">
                <img alt="Bappeda Logo" class="w-full h-full object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAtX_U2XLH_I1i15-52gLHX6xO-ZMvIWSEXuwC0Gmqe9FUcJUw02S-fvYKmCl8m5lNarR7LyGNFYzw3C80fG3bLDiLuRnQ_tKrdJgcSt14YJwvn1SDIaPD4lLbpACkSKqDRajmLT0hTdC7q7hOxe2xm7Kwo5VtzSPmlHdRIB5tN_lCE8k42SCkmMmJHeWOayGOsVETHk0HTdmnA8fhIR089BrqE1gkSrqbkEBgtQpw3bHDEhhKbKKJA">
            </div>
            <div>
                <h1 class="text-white text-sm font-bold m-0 leading-tight tracking-wide">E-PPID</h1>
                <p class="text-gray-400 text-[10px] m-0">Bappeda Ciamis</p>
            </div>
        </div>

        <!-- Scrollable Menu Area -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-3"
             x-data="{ scroll: $persist(0).as('sidebar-scroll') }"
             x-init="$nextTick(() => { $el.scrollTop = scroll })"
             @scroll.debounce.100ms="scroll = $el.scrollTop">
            
            <div class="px-5 mb-1.5 text-[9px] font-bold text-gray-500 uppercase tracking-widest">Utama</div>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined text-[16px]">dashboard</span>
                Dashboard
            </a>
            
            <div class="mt-4 mb-1.5 px-5 text-[9px] font-bold text-gray-500 uppercase tracking-widest">Menu Utama</div>
            
            <!-- Permohonan Group -->
            <div x-data="{ open: $persist(true).as('sidebar-permohonan-menu') }" class="mb-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-2 text-[13px] {{ request()->routeIs('admin.permohonan.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'text-gray-300 hover:text-white hover:bg-white/5 border-l-2 border-transparent' }} transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[16px]">folder_open</span>
                        <span>Permohonan Informasi</span>
                    </div>
                    <span class="material-symbols-outlined text-[14px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse>
                    @if(auth()->user()->hasRole('Desk Layanan') || auth()->user()->hasRole('Super Admin'))
                    <a class="flex items-center gap-3 pl-11 pr-5 py-1.5 text-[12px] transition-all duration-200 {{ request()->fullUrl() == route('admin.permohonan.index', ['status' => 'masuk']) ? 'text-[#f5d76e]' : 'hover:text-white text-gray-400' }}" href="{{ route('admin.permohonan.index', ['status' => 'masuk']) }}">
                        <span class="material-symbols-outlined text-[14px]">arrow_right</span>
                        Permohonan Masuk
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Petugas Penghubung') || auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                    <a class="flex items-center gap-3 pl-11 pr-5 py-1.5 text-[12px] transition-all duration-200 {{ request()->fullUrl() == route('admin.permohonan.index', ['status' => 'menunggu_koordinasi']) ? 'text-[#f5d76e]' : 'hover:text-white text-gray-400' }}" href="{{ route('admin.permohonan.index', ['status' => 'menunggu_koordinasi']) }}">
                        <span class="material-symbols-outlined text-[14px]">arrow_right</span>
                        Koordinasi Data
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                    <a class="flex items-center gap-3 pl-11 pr-5 py-1.5 text-[12px] transition-all duration-200 {{ request()->fullUrl() == route('admin.permohonan.index', ['status' => 'siap_validasi']) ? 'text-[#f5d76e]' : 'hover:text-white text-gray-400' }}" href="{{ route('admin.permohonan.index', ['status' => 'siap_validasi']) }}">
                        <span class="material-symbols-outlined text-[14px]">arrow_right</span>
                        Uji & Validasi
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Atasan PPID Pelaksana') || auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                    <a class="flex items-center gap-3 pl-11 pr-5 py-1.5 text-[12px] transition-all duration-200 {{ request()->fullUrl() == route('admin.permohonan.index', ['status' => 'menunggu_ttd']) ? 'text-[#f5d76e]' : 'hover:text-white text-gray-400' }}" href="{{ route('admin.permohonan.index', ['status' => 'menunggu_ttd']) }}">
                        <span class="material-symbols-outlined text-[14px]">arrow_right</span>
                        Konsep Jawaban
                    </a>
                    @endif
                    <a class="flex items-center gap-3 pl-11 pr-5 py-1.5 text-[12px] transition-all duration-200 {{ request()->fullUrl() == route('admin.permohonan.index') ? 'text-[#f5d76e]' : 'hover:text-white text-gray-400' }}" href="{{ route('admin.permohonan.index') }}">
                        <span class="material-symbols-outlined text-[14px]">arrow_right</span>
                        Semua Permohonan
                    </a>
                </div>
            </div>
            
            <!-- Keberatan Menu -->
            @if(auth()->user()->hasRole('Atasan PPID Pelaksana') || auth()->user()->hasRole('Desk Layanan') || auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.keberatan.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.keberatan.index') }}">
                <span class="material-symbols-outlined text-[16px]">gavel</span>
                Sengketa & Keberatan
            </a>
            @endif
            
            @if(auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
            <div class="mt-4 mb-1.5 px-5 text-[9px] font-bold text-gray-500 uppercase tracking-widest">E-Office</div>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.surat-masuk.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.surat-masuk.index') }}">
                <span class="material-symbols-outlined text-[16px]">mark_email_unread</span>
                Surat Masuk
            </a>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.surat-keluar.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.surat-keluar.index') }}">
                <span class="material-symbols-outlined text-[16px]">forward_to_inbox</span>
                Surat Keluar
            </a>
            @endif
            
            @if(auth()->user()->hasRole('Super Admin'))
            <div class="mt-4 mb-1.5 px-5 text-[9px] font-bold text-gray-500 uppercase tracking-widest">Pengaturan & Sistem</div>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.unit-pengolah.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.unit-pengolah.index') }}">
                <span class="material-symbols-outlined text-[16px]">corporate_fare</span>
                Master Bidang
            </a>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.klasifikasi-arsip.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.klasifikasi-arsip.index') }}">
                <span class="material-symbols-outlined text-[16px]">folder_special</span>
                Klasifikasi Arsip
            </a>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.kategori-pemohon.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.kategori-pemohon.index') }}">
                <span class="material-symbols-outlined text-[16px]">groups</span>
                Kategori Pemohon
            </a>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.cara-memperoleh-informasi.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.cara-memperoleh-informasi.index') }}">
                <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                Cara Memperoleh Info
            </a>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.kategori-informasi-publik.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.kategori-informasi-publik.index') }}">
                <span class="material-symbols-outlined text-[16px]">category</span>
                Kategori Info Publik
            </a>
            @endif

            @if(auth()->user()->hasRole('Super Admin'))
            <div class="mt-4 mb-1.5 px-5 text-[9px] font-bold text-gray-500 uppercase tracking-widest">Super Admin</div>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.users.index') }}">
                <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
                Pengguna & Akun
            </a>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.roles.index') }}">
                <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                Hak Akses (Role)
            </a>
            @endif

            <!-- Pengaturan Link (Single Page) -->
            <div class="mt-4 mb-1.5 px-5 text-[9px] font-bold text-gray-500 uppercase tracking-widest">Pengaturan</div>
            <a class="flex items-center gap-3 px-5 py-2 text-[13px] transition-all duration-200 {{ request()->routeIs('admin.profile.*') || request()->routeIs('admin.settings.*') || request()->routeIs('admin.logs.*') ? 'text-[#f5d76e] bg-white/5 border-l-2 border-[#f5d76e]' : 'hover:text-white hover:bg-white/5 border-l-2 border-transparent text-gray-300' }}" href="{{ route('admin.profile.index') }}">
                <span class="material-symbols-outlined text-[16px]">settings</span>
                Pengaturan
            </a>
        </div>

        <!-- User Profile Footer -->
        <div class="border-t border-gray-700/50 p-4 shrink-0 bg-[#121c2e] flex flex-col justify-end">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="overflow-hidden min-w-0">
                    <p class="text-[13px] font-semibold text-white truncate m-0 leading-tight">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-[9px] text-gray-400 truncate m-0 mt-0.5">{{ auth()->user()->role->name ?? 'Admin' }}</p>
                </div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 text-gray-400 hover:text-white hover:bg-white/5 transition-colors duration-200 px-2 py-1.5 rounded text-[12px] font-medium bg-transparent cursor-pointer">
                    <span class="material-symbols-outlined text-[16px] text-red-400">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 md:pl-[240px]">
        <!-- TopAppBar -->
        <header class="bg-white border-b border-gray-200 shadow-sm docked full-width top-0 sticky z-50 flex justify-between items-center w-full px-6 py-2.5">
            <div class="flex items-center">
                <span class="text-primary font-bold text-sm md:hidden mr-4">E-PPID</span>
                
                <!-- Desktop Header Title -->
                <div class="hidden md:flex items-center gap-3">
                    <h2 class="text-sm font-bold text-gray-800 m-0">E-PPID Dashboard</h2>
                    <span class="bg-[#1a2b42] text-white text-[10px] px-2 py-0.5 rounded-full font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-[10px]">location_on</span>
                        Bappeda Ciamis
                    </span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Clock / Date -->
                <div class="hidden lg:flex items-center gap-1.5 text-gray-400 text-xs mr-2">
                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors relative">
                    <span class="material-symbols-outlined text-[16px]">notifications</span>
                    <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                </button>
                
                <div x-data="{ openProfile: false }" class="relative">
                    <div @click="openProfile = !openProfile" @click.away="openProfile = false" class="flex items-center gap-2 border border-gray-200 rounded-full px-3 py-1 bg-white cursor-pointer hover:bg-gray-50 transition-colors">
                        <div class="w-5 h-5 rounded-full bg-[#1a2b42] flex items-center justify-center text-white font-bold text-[10px]">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="hidden md:block">
                            <p class="text-xs font-semibold text-gray-700 m-0 leading-none">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        </div>
                        <span class="material-symbols-outlined text-[14px] text-gray-400 ml-1 transition-transform duration-200" :class="openProfile ? 'rotate-180' : ''">arrow_drop_down</span>
                    </div>

                    <!-- Dropdown Menu -->
                    <div x-show="openProfile" 
                         x-transition:enter="transition ease-out duration-100" 
                         x-transition:enter-start="transform opacity-0 scale-95" 
                         x-transition:enter-end="transform opacity-100 scale-100" 
                         x-transition:leave="transition ease-in duration-75" 
                         x-transition:leave-start="transform opacity-100 scale-100" 
                         x-transition:leave-end="transform opacity-0 scale-95" 
                         class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-50 py-1" style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ auth()->user()->email ?? '' }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-gray-600 text-[9px] font-bold uppercase rounded">{{ auth()->user()->role->name ?? 'Admin' }}</span>
                        </div>

                        <!-- 
                        <a href="#" class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-[#1a2b42] transition-colors">
                            <span class="material-symbols-outlined text-[16px]">person</span> Profil Saya
                        </a> 
                        -->

                        @if(auth()->user()->hasRole('Super Admin'))
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-[#1a2b42] transition-colors">
                            <span class="material-symbols-outlined text-[16px]">settings</span> Pengaturan Sistem
                        </a>
                        <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-[#1a2b42] transition-colors">
                            <span class="material-symbols-outlined text-[16px]">policy</span> Log Aktivitas
                        </a>
                        @endif

                        <form action="{{ route('admin.logout') }}" method="POST" class="m-0 border-t border-gray-100 mt-1 pt-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors text-left font-semibold cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">logout</span> Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        @yield('content')
    </div>
</body>
</html>
