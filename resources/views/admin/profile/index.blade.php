@extends('admin.layouts.settings')

@section('title', 'Profil Pengguna')

@section('settings_content')
<div>
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow-sm">
        <div class="flex items-center">
            <span class="material-symbols-outlined text-green-500 mr-2">check_circle</span>
            <p class="text-green-700 text-sm font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Profile Update Form -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-gray-600">manage_accounts</span>
                    Informasi Dasar
                </h3>
            </div>
            
            <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#03224d] focus:border-[#03224d] outline-none transition-all @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#03224d] focus:border-[#03224d] outline-none transition-all @error('email') border-red-500 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-gray-100 pt-6 mt-6">
                    <h4 class="font-bold text-gray-800 mb-4 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-600 text-[18px]">lock</span>
                        Ganti Password (Opsional)
                    </h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Password Saat Ini</label>
                            <input type="password" name="current_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#03224d] focus:border-[#03224d] outline-none transition-all @error('current_password') border-red-500 @enderror" placeholder="Kosongkan jika tidak ingin mengubah password">
                            @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Password Baru</label>
                            <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#03224d] focus:border-[#03224d] outline-none transition-all @error('password') border-red-500 @enderror">
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#03224d] focus:border-[#03224d] outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2.5 bg-[#03224d] text-white rounded-lg font-semibold hover:bg-[#1f3864] transition-colors flex items-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Role Information Side Card -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-[#03224d] rounded-xl shadow-sm border border-[#1f3864] overflow-hidden text-center p-6 text-white">
                <div class="w-20 h-20 bg-white/20 rounded-full mx-auto flex items-center justify-center text-3xl font-bold mb-4">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h3 class="font-bold text-lg mb-1">{{ $user->name }}</h3>
                <span class="inline-block px-3 py-1 bg-[#ffc329] text-yellow-900 rounded-full text-xs font-bold uppercase tracking-wider mb-4">
                    {{ $user->role->name ?? 'Admin' }}
                </span>
                <p class="text-xs text-blue-200">Terdaftar sejak: {{ $user->created_at->format('d M Y') }}</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h4 class="font-bold text-gray-800 mb-3 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">security</span>
                    Status Keamanan
                </h4>
                <div class="flex items-center justify-between text-sm py-2 border-b border-gray-100">
                    <span class="text-gray-600">Autentikasi 2FA</span>
                    @if($user->google2fa_secret)
                        <span class="text-green-600 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">check_circle</span> Aktif</span>
                    @else
                        <span class="text-red-500 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">cancel</span> Nonaktif</span>
                    @endif
                </div>
                @if(!$user->google2fa_secret)
                <div class="mt-4">
                    <a href="{{ route('admin.2fa.setup') }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Atur 2FA Sekarang
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
