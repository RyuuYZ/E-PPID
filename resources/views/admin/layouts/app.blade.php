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
    <!-- Tom Select for Rich Modern Custom Dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
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

        /* ===================================================
           Custom Modern Tom Select (Midnight Slate / Tailwind)
           =================================================== */
        .ts-wrapper {
            width: 100% !important;
            position: relative;
            font-family: inherit !important;
        }

        .ts-wrapper.single .ts-control,
        .ts-control {
            display: flex !important;
            align-items: center !important;
            min-height: 42px !important;
            padding: 0.5rem 2.25rem 0.5rem 0.875rem !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important; /* rounded-xl */
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
            cursor: pointer !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 0.75rem center !important;
            background-repeat: no-repeat !important;
            background-size: 1.25rem 1.25rem !important;
        }

        .ts-wrapper.focus .ts-control,
        .ts-wrapper.dropdown-active .ts-control {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%232563eb' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 12 4-4 4 4'/%3e%3c/svg%3e") !important;
        }

        .ts-wrapper .ts-control .item {
            font-size: 0.8125rem !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: calc(100% - 1rem) !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .ts-wrapper.plugin-dropdown_input .ts-control > input {
            display: none !important;
        }

        .ts-wrapper .ts-control > input {
            font-size: 0.8125rem !important;
            color: #1e293b !important;
            font-family: inherit !important;
        }

        /* Dropdown Menu Box */
        .ts-dropdown {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.875rem !important;
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.12), 0 4px 6px -2px rgba(15, 23, 42, 0.04) !important;
            padding: 0.4rem !important;
            margin-top: 0.375rem !important;
            font-size: 0.8125rem !important;
            z-index: 9999 !important;
        }

        /* Search input inside dropdown */
        .ts-dropdown .dropdown-input-wrap {
            padding: 0.25rem 0.25rem 0.5rem 0.25rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            margin-bottom: 0.35rem !important;
        }

        .ts-dropdown .dropdown-input {
            width: 100% !important;
            padding: 0.45rem 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            font-size: 0.775rem !important;
            font-family: inherit !important;
            background-color: #f8fafc !important;
            color: #1e293b !important;
            outline: none !important;
            box-sizing: border-box !important;
            transition: all 0.15s ease !important;
        }

        .ts-dropdown .dropdown-input:focus {
            background-color: #ffffff !important;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1) !important;
        }

        /* Dropdown Content & Options */
        .ts-dropdown .ts-dropdown-content {
            max-height: 240px !important;
            overflow-y: auto !important;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .ts-dropdown .ts-dropdown-content::-webkit-scrollbar {
            width: 5px;
        }

        .ts-dropdown .ts-dropdown-content::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }

        .ts-dropdown .option {
            padding: 0.5rem 0.75rem !important;
            border-radius: 0.5rem !important;
            color: #334155 !important;
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: background-color 0.1s ease, color 0.1s ease !important;
            margin-bottom: 2px !important;
            display: flex !important;
            align-items: center !important;
        }

        .ts-dropdown .option.active,
        .ts-dropdown .option:hover {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            font-weight: 600 !important;
        }

        .ts-dropdown .option.selected {
            background-color: #dbeafe !important;
            color: #1e40af !important;
            font-weight: 600 !important;
        }

        .ts-dropdown .no-results {
            padding: 0.875rem !important;
            color: #94a3b8 !important;
            font-size: 0.775rem !important;
            text-align: center !important;
        }

        /* Optgroup Header */
        .ts-dropdown .optgroup-header {
            font-size: 0.6875rem !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 0.5rem 0.75rem 0.25rem 0.75rem !important;
            background: #f8fafc !important;
            border-radius: 0.375rem !important;
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }

        /* Compact variant for toolbars/filters */
        .ts-wrapper.ts-compact .ts-control {
            min-height: 40px !important;
            height: 40px !important;
            padding: 0.35rem 2rem 0.35rem 0.75rem !important;
            font-size: 0.75rem !important;
            background-color: #f8fafc !important;
        }
        .ts-wrapper.ts-compact .ts-control .item {
            font-size: 0.75rem !important;
        }
    </style>
</head>
<body class="bg-surface text-on-surface flex min-h-screen" x-data="{ mobileSidebarOpen: false }">
    
    <!-- Mobile Sidebar Backdrop -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 md:hidden"
         @click="mobileSidebarOpen = false"
         style="display: none;"></div>

    <!-- SideNavBar (Desktop & Mobile Drawer) -->
    <nav :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
         class="fixed top-0 left-0 bottom-0 w-64 flex flex-col bg-[#0f172a] text-slate-300 border-r border-slate-800/80 shadow-2xl z-50 transition-transform duration-300 ease-in-out">
        
        <!-- Brand Header -->
        <div class="px-5 py-4 flex items-center justify-between border-b border-slate-800/80 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-white p-1.5 flex items-center justify-center shrink-0 shadow-sm ring-1 ring-white/20">
                    <img alt="Bappeda Logo" class="w-full h-full object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAtX_U2XLH_I1i15-52gLHX6xO-ZMvIWSEXuwC0Gmqe9FUcJUw02S-fvYKmCl8m5lNarR7LyGNFYzw3C80fG3bLDiLuRnQ_tKrdJgcSt14YJwvn1SDIaPD4lLbpACkSKqDRajmLT0hTdC7q7hOxe2xm7Kwo5VtzSPmlHdRIB5tN_lCE8k42SCkmMmJHeWOayGOsVETHk0HTdmnA8fhIR089BrqE1gkSrqbkEBgtQpw3bHDEhhKbKKJA">
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h1 class="text-white text-sm font-bold m-0 tracking-tight leading-tight">E-PPID</h1>
                        <span class="text-[9px] font-bold text-blue-400 bg-blue-500/10 px-1.5 py-0.2 rounded border border-blue-500/20">ADMIN</span>
                    </div>
                    <p class="text-slate-400 text-[11px] m-0 mt-0.5 font-medium truncate">Bappeda Ciamis</p>
                </div>
            </div>
            <button @click="mobileSidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Scrollable Menu Area -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-3 px-3 space-y-0.5"
             x-data="{ scroll: $persist(0).as('sidebar-scroll') }"
             x-init="$nextTick(() => { $el.scrollTop = scroll })"
             @scroll.debounce.100ms="scroll = $el.scrollTop">
            
            <!-- SECTION: UTAMA -->
            <div class="px-3 pt-2 pb-1.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Utama</div>
            
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">dashboard</span>
                <span>Dashboard</span>
            </a>
            
            <!-- SECTION: LAYANAN INFORMASI -->
            <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Layanan Informasi</div>
            
            <!-- Permohonan Group -->
            <div x-data="{ open: $persist(true).as('sidebar-permohonan-menu') }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.permohonan.*') ? 'bg-slate-800/90 text-white font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.permohonan.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-200' }}">folder_open</span>
                        <span class="truncate">Permohonan Informasi</span>
                    </div>
                    <span class="material-symbols-outlined text-[16px] transition-transform duration-200 text-slate-500 group-hover:text-slate-300" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse class="pl-4 ml-5 my-1 space-y-1 border-l border-slate-800/80">
                    @php
                        $isMasuk = (request()->routeIs('admin.permohonan.index') && request('status') == 'diajukan') || (request()->routeIs('admin.permohonan.show') && isset($permohonan) && $permohonan->status->value == 'diajukan');
                        $isKoordinasi = (request()->routeIs('admin.permohonan.index') && request('status') == 'menunggu_data') || (request()->routeIs('admin.permohonan.show') && isset($permohonan) && $permohonan->status->value == 'menunggu_data');
                        $isUji = (request()->routeIs('admin.permohonan.index') && request('status') == 'data_diuji') || (request()->routeIs('admin.permohonan.show') && isset($permohonan) && $permohonan->status->value == 'data_diuji');
                        $isKonsep = (request()->routeIs('admin.permohonan.index') && request('status') == 'menunggu_tanda_tangan') || (request()->routeIs('admin.permohonan.show') && isset($permohonan) && $permohonan->status->value == 'menunggu_tanda_tangan');
                        $isSemua = request()->routeIs('admin.permohonan.index') && empty(request()->except('page'));
                    @endphp
                    @if(auth()->user()->hasRole('Desk Layanan') || auth()->user()->hasRole('Super Admin'))
                    <a class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs transition-all duration-150 group {{ $isMasuk ? 'text-blue-400 font-bold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}" href="{{ route('admin.permohonan.index', ['status' => 'diajukan']) }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isMasuk ? 'bg-blue-400 shadow-xs shadow-blue-400/50' : 'bg-slate-600 group-hover:bg-slate-400' }}"></span>
                        <span>Permohonan Masuk</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Petugas Penghubung') || auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                    <a class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs transition-all duration-150 group {{ $isKoordinasi ? 'text-blue-400 font-bold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}" href="{{ route('admin.permohonan.index', ['status' => 'menunggu_data']) }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isKoordinasi ? 'bg-blue-400 shadow-xs shadow-blue-400/50' : 'bg-slate-600 group-hover:bg-slate-400' }}"></span>
                        <span>Koordinasi Data</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                    <a class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs transition-all duration-150 group {{ $isUji ? 'text-blue-400 font-bold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}" href="{{ route('admin.permohonan.index', ['status' => 'data_diuji']) }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isUji ? 'bg-blue-400 shadow-xs shadow-blue-400/50' : 'bg-slate-600 group-hover:bg-slate-400' }}"></span>
                        <span>Uji & Validasi</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Atasan PPID Pelaksana') || auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                    <a class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs transition-all duration-150 group {{ $isKonsep ? 'text-blue-400 font-bold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}" href="{{ route('admin.permohonan.index', ['status' => 'menunggu_tanda_tangan']) }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isKonsep ? 'bg-blue-400 shadow-xs shadow-blue-400/50' : 'bg-slate-600 group-hover:bg-slate-400' }}"></span>
                        <span>Konsep Jawaban</span>
                    </a>
                    @endif
                    <a class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs transition-all duration-150 group {{ $isSemua ? 'text-blue-400 font-bold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}" href="{{ route('admin.permohonan.index') }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isSemua ? 'bg-blue-400 shadow-xs shadow-blue-400/50' : 'bg-slate-600 group-hover:bg-slate-400' }}"></span>
                        <span>Semua Permohonan</span>
                    </a>
                </div>
            </div>
            
            <!-- Daftar Informasi Publik (DIP) -->
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.informasi-publik.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.informasi-publik.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.informasi-publik.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">public</span>
                <span>Daftar Info Publik (DIP)</span>
            </a>

            <!-- Keberatan Menu -->
            @if(auth()->user()->hasRole('Atasan PPID Pelaksana') || auth()->user()->hasRole('Desk Layanan') || auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.keberatan.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.keberatan.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.keberatan.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">gavel</span>
                <span>Sengketa & Keberatan</span>
            </a>
            @endif
            
            <!-- SECTION: PERSURATAN / E-OFFICE -->
            @if(auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
            <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Persuratan (E-Office)</div>
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.surat-masuk.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.surat-masuk.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.surat-masuk.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">mark_email_unread</span>
                <span>Surat Masuk</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.surat-keluar.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.surat-keluar.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.surat-keluar.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">forward_to_inbox</span>
                <span>Surat Keluar</span>
            </a>
            @endif
            
            <!-- SECTION: MASTER DATA -->
            @if(auth()->user()->hasRole('Super Admin'))
            <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Master Data</div>
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.unit-pengolah.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.unit-pengolah.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.unit-pengolah.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">corporate_fare</span>
                <span>Master Bidang</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.klasifikasi-arsip.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.klasifikasi-arsip.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.klasifikasi-arsip.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">folder_special</span>
                <span>Klasifikasi Arsip</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.kategori-pemohon.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.kategori-pemohon.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.kategori-pemohon.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">groups</span>
                <span>Kategori Pemohon</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.cara-memperoleh-informasi.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.cara-memperoleh-informasi.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.cara-memperoleh-informasi.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">receipt_long</span>
                <span>Cara Perolehan Info</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.kategori-informasi-publik.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.kategori-informasi-publik.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.kategori-informasi-publik.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">category</span>
                <span>Kategori Info Publik</span>
            </a>
            @endif

            <!-- SECTION: ADMINISTRATOR & SISTEM -->
            @if(auth()->user()->hasRole('Super Admin'))
            <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Administrator</div>
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.users.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">manage_accounts</span>
                <span>Kelola Pengguna</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.roles.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.roles.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.roles.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">admin_panel_settings</span>
                <span>Hak Akses (Role)</span>
            </a>
            @endif

            <!-- SECTION: PENGATURAN & AUDIT -->
            <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pengaturan & Log</div>
            
            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.logs.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.logs.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.logs.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">history</span>
                <span>Log Aktivitas</span>
            </a>

            <a class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.profile.*') || request()->routeIs('admin.settings.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}" href="{{ route('admin.profile.index') }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('admin.profile.*') || request()->routeIs('admin.settings.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">settings</span>
                <span>Pengaturan Sistem</span>
            </a>
        </div>

        <!-- User Profile Card Footer -->
        <div class="p-3 border-t border-slate-800/80 bg-slate-900/60 shrink-0">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-800/60 border border-slate-700/60">
                <div class="relative w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0 overflow-hidden ring-1 ring-white/10">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" class="w-full h-full object-cover">
                    @else
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    @endif
                    <span class="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-slate-800"></span>
                </div>
                <div class="overflow-hidden min-w-0 flex-1">
                    <p class="text-xs font-bold text-white truncate m-0 leading-tight">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-[10px] text-slate-400 truncate m-0 mt-0.5 font-medium">{{ auth()->user()->role->name ?? 'Admin' }}</p>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" class="m-0 shrink-0">
                    @csrf
                    <button type="submit" title="Keluar (Logout)" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 md:pl-64">
        <!-- TopAppBar -->
        <header class="bg-white/95 backdrop-blur-sm border-b border-slate-200 shadow-2xs docked full-width top-0 sticky z-30 flex justify-between items-center w-full px-5 md:px-8 py-3">
            <div class="flex items-center">
                <!-- Mobile Toggle Button -->
                <button @click="mobileSidebarOpen = true" class="md:hidden mr-3 p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">menu</span>
                </button>
                
                <span class="text-blue-950 font-bold text-sm md:hidden">E-PPID</span>

                
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
                        <div class="w-5 h-5 rounded-full bg-[#1a2b42] flex items-center justify-center text-white font-bold text-[10px] overflow-hidden">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                            @endif
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

    <!-- Global Custom Select Initialization Script -->
    <script>
        function initCustomSelects(parent = document) {
            if (typeof TomSelect === 'undefined') return;
            
            parent.querySelectorAll('select.custom-select:not(.tomselected)').forEach(function(el) {
                const noSearch = el.dataset.noSearch === 'true';
                const placeholder = el.getAttribute('placeholder') || el.dataset.placeholder || (el.options[0] && el.options[0].value === '' ? el.options[0].text : '-- Pilih --');
                
                const plugins = [];
                if (!noSearch) {
                    plugins.push('dropdown_input');
                }
                if (el.hasAttribute('multiple')) {
                    plugins.push('remove_button');
                }

                try {
                    const ts = new TomSelect(el, {
                        plugins: plugins,
                        create: false,
                        allowEmptyOption: true,
                        maxOptions: null, // show all items
                        sortField: { field: '$order' }, // preserve original order
                        placeholder: placeholder,
                        searchField: ['text'],
                        onInitialize: function() {
                            const searchInput = this.dropdown.querySelector('.dropdown-input');
                            if (searchInput) {
                                searchInput.setAttribute('placeholder', el.dataset.searchPlaceholder || 'Ketik untuk mencari...');
                                searchInput.setAttribute('autocomplete', 'off');
                            }
                        }
                    });
                } catch (err) {
                    console.error('Error initializing TomSelect on', el, err);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initCustomSelects();
        });
    </script>
    @yield('scripts')
</body>
</html>
