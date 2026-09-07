<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'Layanan Informasi Bappeda PPID')</title>

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Tom Select for Rich Modern Custom Dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js Core & Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        [x-cloak] { display: none !important; }

        /* ===================================================
           Custom Modern Tom Select (Public Portal Theme)
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
            min-height: 44px !important;
            padding: 0.5rem 2.25rem 0.5rem 1rem !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important; /* rounded-xl */
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
            cursor: pointer !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 0.875rem center !important;
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
            font-size: 0.875rem !important;
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
            font-size: 0.875rem !important;
            color: #1e293b !important;
            font-family: inherit !important;
        }

        /* Dropdown Menu Box */
        .ts-dropdown {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.875rem !important;
            box-shadow: 0 14px 30px -4px rgba(15, 23, 42, 0.14), 0 4px 6px -2px rgba(15, 23, 42, 0.04) !important;
            padding: 0.4rem !important;
            margin-top: 0.375rem !important;
            font-size: 0.875rem !important;
            z-index: 99999 !important;
        }

        /* Search input inside dropdown */
        .ts-dropdown .dropdown-input-wrap {
            padding: 0.25rem 0.25rem 0.5rem 0.25rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            margin-bottom: 0.35rem !important;
        }

        .ts-dropdown .dropdown-input {
            width: 100% !important;
            padding: 0.5rem 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            font-size: 0.8125rem !important;
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
            max-height: 260px !important;
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
            padding: 0.55rem 0.75rem !important;
            border-radius: 0.5rem !important;
            color: #334155 !important;
            font-size: 0.875rem !important;
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

        .ts-dropdown .optgroup-header {
            font-size: 0.6875rem !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 0.5rem 0.75rem 0.25rem 0.75rem !important;
            background: #f8fafc !important;
            border-radius: 0.375rem !important;
            margin-top: 0.35rem !important;
            margin-bottom: 0.25rem !important;
        }

        .ts-dropdown .no-results {
            padding: 0.875rem !important;
            color: #94a3b8 !important;
            font-size: 0.8125rem !important;
            text-align: center !important;
        }

        @yield('styles')
    </style>
</head>
<body class="bg-[#f8fafc] text-gray-800 antialiased min-h-screen flex flex-col">
<!-- TopNavBar -->
<header class="bg-white/80 backdrop-blur-md border-b border-gray-200/80 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] w-full sticky top-0 z-50 transition-all duration-300">
    <div class="w-full flex justify-between items-center px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('ppid_logo.png') }}" alt="Logo Bappeda PPID" class="h-10 w-auto">
            </a>
        </div>
        <nav class="hidden md:flex items-center gap-6">
            <a class="{{ request()->routeIs('home') ? 'text-blue-700 font-bold border-b-2 border-blue-700 pb-1' : 'text-gray-600 hover:text-blue-700 font-medium' }} text-sm transition-colors duration-200" href="{{ route('home') }}">Beranda</a>
            <a class="{{ request()->routeIs('informasi-publik.*') ? 'text-blue-700 font-bold border-b-2 border-blue-700 pb-1' : 'text-gray-600 hover:text-blue-700 font-medium' }} text-sm transition-colors duration-200" href="{{ route('informasi-publik.index') }}">Daftar Informasi Publik</a>
            <a class="{{ request()->routeIs('permohonan.lacak') ? 'text-blue-700 font-bold border-b-2 border-blue-700 pb-1' : 'text-gray-600 hover:text-blue-700 font-medium' }} text-sm transition-colors duration-200" href="{{ route('permohonan.lacak') }}">Lacak Status</a>
            <a class="{{ request()->routeIs('permohonan.create') ? 'text-blue-700 font-bold border-b-2 border-blue-700 pb-1' : 'text-gray-600 hover:text-blue-700 font-medium' }} text-sm transition-colors duration-200" href="{{ route('permohonan.create') }}">Permohonan Baru</a>
        </nav>
        <div class="flex items-center gap-4">
            <a href="{{ route('informasi-publik.index') }}" class="hidden md:flex items-center gap-1.5 bg-[#03224d] text-white font-semibold text-xs px-5 py-2.5 rounded-full hover:bg-[#0B1B3D] transition-all shadow-sm">
                <span class="material-symbols-outlined text-[16px]">search</span>
                Cari Dokumen
            </a>
            <button class="md:hidden text-primary">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
    </div>
</header>

@yield('content')

<!-- Footer -->
<footer class="w-full pt-16 pb-8 px-margin-mobile md:px-margin-desktop mt-auto bg-[#0B1B3D]">
    <div class="max-w-container-max mx-auto flex flex-col md:flex-row justify-between items-start gap-12">
        <div class="flex flex-col gap-4">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('ppid_logo.png') }}" alt="Logo Bappeda PPID" class="h-12 w-auto brightness-0 invert object-contain object-left">
            </a>
            <p class="font-body-md text-body-md text-on-primary/80 dark:text-on-tertiary-container/80 max-w-sm">
                Layanan Informasi Publik Bappeda untuk mewujudkan pemerintahan yang transparan dan akuntabel.
            </p>
        </div>
        <div class="flex flex-col gap-3">
            <h4 class="font-label-md text-label-md text-on-primary mb-2">Tautan Penting</h4>
            <a class="font-label-sm text-label-sm text-on-primary/80 dark:text-on-tertiary-container/80 hover:text-secondary-container dark:hover:text-secondary-fixed transition-colors" href="#">Kebijakan Privasi</a>
            <a class="font-label-sm text-label-sm text-on-primary/80 dark:text-on-tertiary-container/80 hover:text-secondary-container dark:hover:text-secondary-fixed transition-colors" href="#">Syarat &amp; Ketentuan</a>
            <a class="font-label-sm text-label-sm text-on-primary/80 dark:text-on-tertiary-container/80 hover:text-secondary-container dark:hover:text-secondary-fixed transition-colors" href="#">Peta Situs</a>
            <a class="font-label-sm text-label-sm text-on-primary/80 dark:text-on-tertiary-container/80 hover:text-secondary-container dark:hover:text-secondary-fixed transition-colors" href="#">Aksesibilitas</a>
        </div>
    </div>
    <div class="max-w-container-max mx-auto mt-stack-lg pt-4 border-t border-on-primary/20">
        <p class="font-label-sm text-label-sm text-on-primary/60">© 2024 Bappeda PPID. Hak Cipta Dilindungi Undang-Undang.</p>
    </div>
</footer>

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
