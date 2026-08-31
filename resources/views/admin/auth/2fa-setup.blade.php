@extends('admin.layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-4">Pengaturan Two-Factor Authentication (2FA)</h2>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if(!Auth::user()->google2fa_enabled)
                    <div class="mb-6">
                        <p class="mb-4 text-gray-600">
                            Untuk mengaktifkan 2FA, silakan pindai kode QR di bawah ini menggunakan aplikasi authenticator Anda (seperti Google Authenticator atau Authy), atau masukkan kunci rahasia secara manual.
                        </p>
                        <div class="flex justify-center mb-4">
                            {!! $QR_Image !!}
                        </div>
                        <p class="text-center mb-6 font-mono text-gray-700 bg-gray-100 p-2 rounded w-fit mx-auto">
                            Kunci Rahasia: {{ $secret }}
                        </p>

                        <form method="POST" action="{{ route('admin.2fa.enable') }}" class="max-w-sm mx-auto">
                            @csrf
                            <div class="mb-4">
                                <label for="one_time_password" class="block text-sm font-medium text-gray-700">Masukkan 6-digit OTP dari aplikasi Anda</label>
                                <input type="text" name="one_time_password" id="one_time_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required autofocus autocomplete="off">
                            </div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Aktifkan 2FA
                            </button>
                        </form>
                    </div>
                @else
                    <div class="mb-6">
                        <p class="mb-4 text-gray-600">
                            Two-Factor Authentication saat ini <strong class="text-green-600">aktif</strong> di akun Anda.
                        </p>
                        <form method="POST" action="{{ route('admin.2fa.disable') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Nonaktifkan 2FA
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
