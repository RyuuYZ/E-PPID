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
            
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                
                <div class="flex flex-col md:flex-row gap-6 mb-6 pb-6 border-b border-gray-100">
                    <div class="flex-shrink-0 text-center">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Profil</label>
                        <div class="relative w-32 h-32 mx-auto mb-3 border-2 border-gray-200 border-dashed rounded-full overflow-hidden bg-gray-50 flex items-center justify-center cursor-pointer hover:bg-gray-100 transition-colors" onclick="document.getElementById('profile_photo').click()">
                            @if($user->profile_photo_path)
                                <img id="photo_preview" src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <div id="photo_preview_container" class="text-gray-400 flex flex-col items-center">
                                    <span class="material-symbols-outlined text-3xl mb-1">add_a_photo</span>
                                    <span class="text-[10px] uppercase font-bold tracking-wider">Unggah Foto</span>
                                </div>
                                <img id="photo_preview" class="w-full h-full object-cover hidden" alt="Preview">
                            @endif
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*" onchange="previewPhoto(this)">
                        <p class="text-[10px] text-gray-500">Maksimal 2MB (JPG, PNG)</p>
                        @error('profile_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="flex-grow space-y-6">
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
                    </div>
                </div>

                <div>
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
            
            <script>
                function previewPhoto(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var preview = document.getElementById('photo_preview');
                            var container = document.getElementById('photo_preview_container');
                            
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            
                            if (container) {
                                container.classList.add('hidden');
                            }
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>
        </div>

        <!-- Role Information Side Card -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-[#03224d] rounded-xl shadow-sm border border-[#1f3864] overflow-hidden text-center p-6 text-white">
                @if($user->profile_photo_path)
                    <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-white/20">
                @else
                    <div class="w-20 h-20 bg-white/20 rounded-full mx-auto flex items-center justify-center text-3xl font-bold mb-4">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
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

            <!-- Signature Information (For Atasan PPID or users who sign) -->
            @if(auth()->user()->hasRole('Atasan PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <h4 class="font-bold text-gray-800 mb-3 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">draw</span>
                    Tanda Tangan Elektronik (TTE)
                </h4>
                <p class="text-xs text-gray-500 mb-4">Tanda tangan yang tersimpan akan digunakan sebagai opsi default saat Anda mengesahkan Permohonan Informasi.</p>
                
                @if($user->signature_path)
                    <div class="border border-gray-200 rounded p-3 mb-4 bg-gray-50 text-center">
                        <img src="{{ Storage::url($user->signature_path) }}" alt="Tanda Tangan Tersimpan" class="max-h-24 mx-auto">
                    </div>
                @else
                    <div class="border border-gray-200 border-dashed rounded p-4 mb-4 bg-gray-50 text-center text-gray-400 text-xs">
                        Belum ada tanda tangan tersimpan.
                    </div>
                @endif

                <div>
                    <button type="button" onclick="document.getElementById('signatureModal').classList.remove('hidden')" class="block w-full text-center px-4 py-2 border border-blue-600 text-blue-600 rounded text-sm font-semibold hover:bg-blue-50 transition-colors">
                        {{ $user->signature_path ? 'Perbarui Tanda Tangan' : 'Buat Tanda Tangan' }}
                    </button>
                </div>
            </div>

            <!-- Signature Modal -->
            <div id="signatureModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white rounded-xl shadow-xl w-[90%] max-w-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-800 text-sm">Papan Tanda Tangan</h3>
                        <button type="button" onclick="document.getElementById('signatureModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.profile.signature') }}" method="POST" id="profileTteForm">
                        @csrf
                        <div class="p-6">
                            <input type="hidden" name="signature_data" id="profile_signature_data">
                            <div class="border border-gray-300 rounded overflow-hidden bg-white mb-2">
                                <div class="bg-gray-50 border-b border-gray-200 px-3 py-2 flex justify-between items-center">
                                    <span class="text-[10px] text-gray-500 font-semibold uppercase">Gambar Disini:</span>
                                    <button type="button" id="profile_clear_signature" class="text-[10px] text-red-600 hover:text-red-800 font-bold uppercase">Bersihkan (Clear)</button>
                                </div>
                                <canvas id="profile-signature-pad" class="w-full h-40 touch-none cursor-crosshair" width="400" height="160"></canvas>
                            </div>
                        </div>
                        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('signatureModal').classList.add('hidden')" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">Simpan Tanda Tangan</button>
                        </div>
                    </form>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const canvas = document.getElementById('profile-signature-pad');
                    if(canvas) {
                        const signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgb(255, 255, 255)',
                            penColor: 'rgb(0, 0, 100)'
                        });

                        function resizeCanvas() {
                            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
                            canvas.width = canvas.offsetWidth * ratio;
                            canvas.height = canvas.offsetHeight * ratio;
                            canvas.getContext("2d").scale(ratio, ratio);
                            signaturePad.clear(); 
                        }
                        
                        // We need to resize when modal opens because canvas width is 0 when hidden
                        const observer = new MutationObserver((mutations) => {
                            mutations.forEach((mutation) => {
                                if (!document.getElementById('signatureModal').classList.contains('hidden')) {
                                    resizeCanvas();
                                }
                            });
                        });
                        observer.observe(document.getElementById('signatureModal'), { attributes: true, attributeFilter: ['class'] });

                        document.getElementById('profile_clear_signature').addEventListener('click', function () {
                            signaturePad.clear();
                        });

                        document.getElementById('profileTteForm').addEventListener('submit', function(e) {
                            if (signaturePad.isEmpty()) {
                                e.preventDefault();
                                alert("Mohon gambar tanda tangan Anda terlebih dahulu.");
                            } else {
                                document.getElementById('profile_signature_data').value = signaturePad.toDataURL('image/png');
                            }
                        });
                    }
                });
            </script>
            @endif
        </div>
    </div>
</div>
@endsection
