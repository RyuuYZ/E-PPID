@extends('layouts.public')

@section('title', 'Permohonan Berhasil Dikirim - Bappeda PPID')

@section('content')
<main class="flex-grow py-12 px-4 md:px-8 bg-[#f4f6f9] min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        
        <!-- Top Section: Success Banner -->
        <div class="bg-[#1f3864] py-10 px-6 text-center">
            <div class="w-16 h-16 bg-[#ffc329] rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
                <span class="material-symbols-outlined text-[#1f3864] text-4xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-3">Permohonan Berhasil Dikirim</h1>
            <p class="text-blue-100 text-sm md:text-base max-w-lg mx-auto">
                Terima kasih. Permohonan informasi publik Anda telah kami terima dan sedang dalam proses verifikasi.
            </p>
        </div>

        <!-- Middle Section: Registration Number -->
        <div class="py-8 px-6 text-center border-b border-gray-100">
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Nomor Pendaftaran Anda</p>
            <h2 class="text-3xl md:text-4xl font-bold text-[#03224d] mb-6" id="nomorRegistrasi">{{ $nomor_registrasi }}</h2>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button onclick="copyToClipboard()" class="flex items-center justify-center gap-2 px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto" id="copyBtn">
                    <span class="material-symbols-outlined text-[18px]">content_copy</span>
                    <span>Salin Nomor</span>
                </button>
                <a href="#" onclick="alert('Fitur cetak tanda terima sedang dalam pengembangan.')" class="flex items-center justify-center gap-2 px-6 py-2.5 bg-[#03224d] rounded-lg text-sm font-semibold text-white hover:bg-[#1f3864] transition-colors w-full sm:w-auto shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Unduh Tanda Terima</span>
                </a>
            </div>
        </div>

        <!-- Bottom Section: Next Steps -->
        <div class="py-8 px-6 md:px-12">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Langkah Selanjutnya</h3>
            
            <div class="relative border-l-2 border-gray-100 ml-3 space-y-8">
                <!-- Step 1 -->
                <div class="relative pl-6">
                    <span class="absolute flex items-center justify-center w-3 h-3 bg-[#ffc329] rounded-full -left-[7px] top-1 ring-4 ring-white"></span>
                    <h4 class="text-sm font-bold text-gray-800 mb-1">Verifikasi Administrasi</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Tim PPID akan memverifikasi kelengkapan persyaratan permohonan Anda (1-3 hari kerja).</p>
                </div>

                <!-- Step 2 -->
                <div class="relative pl-6">
                    <span class="absolute flex items-center justify-center w-3 h-3 bg-gray-200 rounded-full -left-[7px] top-1 ring-4 ring-white"></span>
                    <h4 class="text-sm font-bold text-gray-800 mb-1">Pencarian Informasi</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Berkoordinasi dengan bidang terkait untuk mengumpulkan informasi yang diminta.</p>
                </div>

                <!-- Step 3 -->
                <div class="relative pl-6">
                    <span class="absolute flex items-center justify-center w-3 h-3 bg-gray-200 rounded-full -left-[7px] top-1 ring-4 ring-white"></span>
                    <h4 class="text-sm font-bold text-gray-800 mb-1">Pengiriman Jawaban</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Informasi akan dikirimkan melalui portal ini atau metode yang Anda pilih.</p>
                </div>
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-[#03224d] transition-colors group">
                    <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    Kembali ke Beranda
                </a>
            </div>
        </div>

    </div>
</main>

@endsection

@section('scripts')
<script>
    function copyToClipboard() {
        var copyText = document.getElementById("nomorRegistrasi").innerText;
        navigator.clipboard.writeText(copyText).then(function() {
            var btn = document.getElementById("copyBtn");
            var originalText = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px] text-green-600">check</span> <span class="text-green-600">Berhasil Disalin</span>';
            btn.classList.add('border-green-300', 'bg-green-50');
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.classList.remove('border-green-300', 'bg-green-50');
            }, 2000);
        }, function(err) {
            console.error('Async: Could not copy text: ', err);
        });
    }
</script>
@endsection
