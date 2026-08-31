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
<body class="bg-background text-on-surface antialiased min-h-screen flex flex-col">
<!-- TopNavBar -->
<header class="bg-surface-container-lowest border-b border-outline-variant shadow-sm w-full sticky top-0 z-50">
    <div class="sticky top-0 w-full z-50 flex justify-between items-center px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('ppid_logo.png') }}" alt="Logo Bappeda PPID" class="h-10 w-auto">
            </a>
        </div>
        <nav class="hidden md:flex items-center gap-6">
            <a class="{{ request()->routeIs('home') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant font-medium' }} font-label-md text-label-md hover:text-primary transition-colors duration-200" href="{{ route('home') }}">Beranda</a>
            <a class="text-on-surface-variant font-medium font-label-md text-label-md hover:text-primary transition-colors duration-200" href="#">Kategori Informasi</a>
            <a class="{{ request()->routeIs('permohonan.lacak') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant font-medium' }} font-label-md text-label-md hover:text-primary transition-colors duration-200" href="{{ route('permohonan.lacak') }}">Lacak Status</a>
            <a class="text-on-surface-variant font-medium font-label-md text-label-md hover:text-primary transition-colors duration-200" href="#">FAQ</a>
        </nav>
        <div class="flex items-center gap-4">
            <button class="hidden md:flex bg-primary-container text-on-primary font-label-md text-label-md px-6 py-2.5 rounded-full hover:opacity-90 transition-opacity">
                Hubungi Kami
            </button>
            <button class="md:hidden text-primary">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
    </div>
</header>

@yield('content')

<!-- Footer -->
<footer class="w-full pt-stack-lg pb-stack-md px-margin-mobile md:px-margin-desktop mt-stack-lg bg-primary dark:bg-tertiary-container">
    <div class="max-w-container-max mx-auto flex flex-col md:flex-row justify-between items-start gap-8">
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

@yield('scripts')
</body>
</html>
