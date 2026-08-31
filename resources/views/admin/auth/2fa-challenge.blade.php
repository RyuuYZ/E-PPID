<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>2FA Verification - E-PPID Bappeda</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#0b162c",
                        "primary-hover": "#1a2a4a",
                        "brand-blue": "#1d4ed8",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="flex h-screen overflow-hidden items-center justify-center p-4">
    
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-full">
        
        <div class="p-6 md:p-8 flex flex-col justify-center bg-white relative">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[48px] text-brand-blue">lock_person</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Two-Factor Authentication</h2>
                <p class="text-sm text-gray-500 font-medium">Buka aplikasi authenticator Anda dan masukkan kode 6-digit.</p>
            </div>

            @error('one_time_password')
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                    {{ $message }}
                </div>
            @enderror

            <form action="{{ route('admin.2fa.verify') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="one_time_password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">KODE OTP</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <span class="material-symbols-outlined text-[20px]">pin</span>
                        </div>
                        <input type="text" id="one_time_password" name="one_time_password" required autofocus autocomplete="off"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-center text-xl tracking-widest text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                            placeholder="• • • • • •">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary text-white font-semibold rounded-lg py-3 px-4 hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors text-base shadow-sm">
                        Verifikasi Kode
                    </button>
                </div>
                
                <div class="text-center mt-4">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-sm text-gray-500 hover:text-gray-700 underline">
                        Batal dan Logout
                    </a>
                </div>
            </form>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            
        </div>
    </div>

</body>
</html>
