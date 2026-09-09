@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden" x-data="passwordValidator()">
        
        <div class="bg-[#1a2b42] p-6 text-center">
            <div class="w-12 h-12 mx-auto mb-3 bg-white/10 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-[24px]">lock_reset</span>
            </div>
            <h2 class="text-xl font-bold text-white m-0">Wajib Ganti Kata Sandi</h2>
            <p class="text-xs text-blue-200 mt-1">Demi keamanan, Anda wajib mengganti kata sandi bawaan dengan kata sandi baru yang lebih aman.</p>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded shadow-sm text-xs font-medium flex items-start gap-2">
                    <span class="material-symbols-outlined text-[16px] text-red-500 shrink-0">error</span>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.process-force-change-password') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="password" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <span class="material-symbols-outlined text-[16px]">key</span>
                        </div>
                        <input type="password" id="password" name="password" x-model="password" @input="validate" required autofocus
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                            placeholder="Masukkan kata sandi baru">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <span class="material-symbols-outlined text-[16px]">password</span>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" x-model="confirmPassword" @input="validate" required
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                            placeholder="Ulangi kata sandi baru">
                    </div>
                </div>

                <!-- Keamanan Password Checklist -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-[11px] font-medium text-gray-600 mt-4">
                    <p class="mb-2 font-bold text-gray-700">Ketentuan Kata Sandi:</p>
                    <ul class="space-y-1.5">
                        <li class="flex items-center gap-2" :class="rules.length ? 'text-green-600' : ''">
                            <span class="material-symbols-outlined text-[14px]" x-text="rules.length ? 'check_circle' : 'radio_button_unchecked'"></span>
                            Minimal 8 karakter
                        </li>
                        <li class="flex items-center gap-2" :class="rules.uppercase ? 'text-green-600' : ''">
                            <span class="material-symbols-outlined text-[14px]" x-text="rules.uppercase ? 'check_circle' : 'radio_button_unchecked'"></span>
                            Mengandung huruf besar (A-Z)
                        </li>
                        <li class="flex items-center gap-2" :class="rules.lowercase ? 'text-green-600' : ''">
                            <span class="material-symbols-outlined text-[14px]" x-text="rules.lowercase ? 'check_circle' : 'radio_button_unchecked'"></span>
                            Mengandung huruf kecil (a-z)
                        </li>
                        <li class="flex items-center gap-2" :class="rules.number ? 'text-green-600' : ''">
                            <span class="material-symbols-outlined text-[14px]" x-text="rules.number ? 'check_circle' : 'radio_button_unchecked'"></span>
                            Mengandung angka (0-9)
                        </li>
                        <li class="flex items-center gap-2" :class="rules.special ? 'text-green-600' : ''">
                            <span class="material-symbols-outlined text-[14px]" x-text="rules.special ? 'check_circle' : 'radio_button_unchecked'"></span>
                            Mengandung karakter spesial (!@#$%^&*)
                        </li>
                        <li class="flex items-center gap-2" :class="rules.match ? 'text-green-600' : ''">
                            <span class="material-symbols-outlined text-[14px]" x-text="rules.match ? 'check_circle' : 'radio_button_unchecked'"></span>
                            Kedua kata sandi cocok
                        </li>
                    </ul>
                </div>

                <div class="pt-4">
                    <button type="submit" :disabled="!isValid" 
                        class="w-full text-white font-bold rounded-lg py-2.5 px-4 transition-all duration-200 text-sm shadow-sm"
                        :class="isValid ? 'bg-[#1a2b42] hover:bg-[#121c2e]' : 'bg-gray-400 cursor-not-allowed opacity-70'">
                        <span class="flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Simpan & Lanjutkan
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('passwordValidator', () => ({
        password: '',
        confirmPassword: '',
        rules: {
            length: false,
            uppercase: false,
            lowercase: false,
            number: false,
            special: false,
            match: false
        },
        get isValid() {
            return this.rules.length && 
                   this.rules.uppercase && 
                   this.rules.lowercase && 
                   this.rules.number && 
                   this.rules.special && 
                   this.rules.match;
        },
        validate() {
            this.rules.length = this.password.length >= 8;
            this.rules.uppercase = /[A-Z]/.test(this.password);
            this.rules.lowercase = /[a-z]/.test(this.password);
            this.rules.number = /[0-9]/.test(this.password);
            this.rules.special = /[@$!%*#?&]/.test(this.password);
            this.rules.match = this.password.length > 0 && this.password === this.confirmPassword;
        }
    }));
});
</script>
@endsection
