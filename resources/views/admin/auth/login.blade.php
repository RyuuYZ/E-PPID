<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login Admin - E-PPID Bappeda</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#0b162c", // Very dark blue for button
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
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="flex h-screen overflow-hidden items-center justify-center p-4">
    
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl flex flex-col md:flex-row overflow-hidden max-h-full">
        
        <!-- Left Side: Image and Welcome Text -->
        <div class="hidden md:flex md:w-1/2 relative bg-gray-900 items-center">
            <!-- Background Image -->
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80" alt="Building" class="absolute inset-0 w-full h-full object-cover opacity-50 mix-blend-overlay">
            
            <!-- Content overlay -->
            <div class="relative z-10 px-12 text-white">
                <h1 class="text-5xl font-bold mb-4 tracking-tight">Welcome</h1>
                <p class="text-lg text-gray-200 leading-relaxed max-w-md font-medium">
                    E-PPID Bappeda menyediakan layanan informasi publik yang transparan dan akuntabel sesuai dengan standar pelayanan instansi pemerintah.
                </p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-6 md:p-8 lg:px-12 flex flex-col justify-center bg-white relative">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                    <img alt="Logo" class="w-full h-full object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAtX_U2XLH_I1i15-52gLHX6xO-ZMvIWSEXuwC0Gmqe9FUcJUw02S-fvYKmCl8m5lNarR7LyGNFYzw3C80fG3bLDiLuRnQ_tKrdJgcSt14YJwvn1SDIaPD4lLbpACkSKqDRajmLT0hTdC7q7hOxe2xm7Kwo5VtzSPmlHdRIB5tN_lCE8k42SCkmMmJHeWOayGOsVETHk0HTdmnA8fhIR089BrqE1gkSrqbkEBgtQpw3bHDEhhKbKKJA">
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Login</h2>
                <p class="text-sm text-gray-500 font-medium">Enter your email and password to continue.</p>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                    {{ session('error') }}
                </div>
            @endif
            @error('email')
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                    {{ $message }}
                </div>
            @enderror
            @error('cf-turnstile-response')
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                    {{ $message }}
                </div>
            @enderror

            <form action="{{ route('admin.authenticate') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">EMAIL</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                            placeholder="Enter your email">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">PASSWORD</label>
                        <a href="#" class="text-sm font-medium text-brand-blue hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <span class="material-symbols-outlined text-[20px]">lock</span>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                            placeholder="Enter your password">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer hover:text-gray-600" id="toggle-password">
                            <span class="material-symbols-outlined text-[20px]" id="toggle-icon">visibility</span>
                        </div>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-2">
                    <input id="remember" name="remember" type="checkbox" class="h-5 w-5 text-brand-blue border-gray-300 rounded focus:ring-brand-blue cursor-pointer">
                    <label for="remember" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                        Remember me for 30 days
                    </label>
                </div>

                <!-- Cloudflare Turnstile -->
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-primary text-white font-semibold rounded-lg py-3 px-4 hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors text-base shadow-sm">
                        Login
                    </button>
                </div>
            </form>
            
        </div>
    </div>

    <script>
        document.getElementById('toggle-password').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggle-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    </script>
</body>
</html>
