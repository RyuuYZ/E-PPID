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
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        @yield('styles')
    </style>
</head>
<body class="bg-surface text-on-surface font-body-md antialiased min-h-screen flex flex-col">

    <!-- TopNavBar -->
    <header class="bg-surface-container-lowest border-b border-outline-variant shadow-sm sticky top-0 w-full z-50">
        <div class="flex justify-between items-center px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
            <div class="flex items-center gap-4">
                <a class="font-headline-md text-headline-md font-bold text-primary" href="{{ route('home') }}">
                    Bappeda PPID
                </a>
            </div>
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex gap-6 items-center">
                <a class="text-on-surface-variant font-medium hover:text-primary transition-colors duration-200 font-label-md text-label-md {{ request()->routeIs('home') ? 'text-primary border-b-2 border-primary pb-1' : '' }}" href="{{ route('home') }}">Beranda</a>
                <a class="text-on-surface-variant font-medium hover:text-primary transition-colors duration-200 font-label-md text-label-md" href="#">Kategori Informasi</a>
                <a class="text-on-surface-variant font-medium hover:text-primary transition-colors duration-200 font-label-md text-label-md {{ request()->routeIs('permohonan.lacak') ? 'text-primary border-b-2 border-primary pb-1' : '' }}" href="{{ route('permohonan.lacak') }}">Lacak Status</a>
                <a class="text-on-surface-variant font-medium hover:text-primary transition-colors duration-200 font-label-md text-label-md" href="#">FAQ</a>
                <button class="ml-4 px-6 py-2 bg-primary text-on-primary rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors">
                    Hubungi Kami
                </button>
            </nav>
            <!-- Mobile Menu Button -->
            <button class="md:hidden text-on-surface-variant">
                <span class="material-symbols-outlined" data-icon="menu">menu</span>
            </button>
        </div>
    </header>

    @yield('content')

    <!-- Footer -->
    <footer class="w-full pt-stack-lg pb-stack-md px-margin-mobile md:px-margin-desktop mt-stack-lg bg-primary dark:bg-tertiary-container">
        <div class="max-w-container-max mx-auto flex flex-col md:flex-row justify-between items-start gap-8">
            <div class="flex flex-col gap-4">
                <span class="font-headline-md text-headline-md font-bold text-secondary-container dark:text-secondary-fixed">Bappeda PPID</span>
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

    @yield('scripts')
</body>
</html>
