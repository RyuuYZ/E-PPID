@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#03224d]">Pengaturan</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola profil, preferensi sistem, dan pantau log aktivitas.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Settings Sidebar/Tabs -->
        <div class="w-full md:w-64 shrink-0">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <nav class="flex flex-col">
                    <a href="{{ route('admin.profile.index') }}" class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.profile.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent' }}">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Profil Saya
                    </a>
                    @if(auth()->user()->hasRole('Super Admin'))
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold transition-colors border-t border-gray-100 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent' }}">
                        <span class="material-symbols-outlined text-[18px]">tune</span>
                        Konfigurasi Sistem
                    </a>
                    <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold transition-colors border-t border-gray-100 {{ request()->routeIs('admin.logs.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent' }}">
                        <span class="material-symbols-outlined text-[18px]">history</span>
                        Log Keamanan
                    </a>
                    @endif
                </nav>
            </div>
        </div>
        
        <!-- Settings Content -->
        <div class="flex-1">
            @yield('settings_content')
        </div>
    </div>
</main>
@endsection
