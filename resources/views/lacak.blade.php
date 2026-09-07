@extends('layouts.public')

@section('title', 'Lacak Status Permohonan - Bappeda PPID')

@section('content')
<main class="flex-grow w-full max-w-container-max mx-auto px-6 py-12 md:py-20 flex flex-col gap-12">

@if(!isset($searched) || (isset($searched) && !$permohonan))
    <!-- Hero Section -->
    <section class="text-center flex flex-col items-center max-w-2xl mx-auto w-full">
        <div class="bg-blue-50 text-blue-600 rounded-2xl w-20 h-20 flex items-center justify-center mb-6 shadow-sm border border-blue-100">
            <span class="material-symbols-outlined text-4xl" data-icon="search" style="font-variation-settings: 'FILL' 1;">search</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-[#0B1B3D] mb-4">Lacak Status Permohonan</h1>
        <p class="text-gray-500 text-lg max-w-xl leading-relaxed">
            Masukkan nomor pendaftaran atau tiket Anda untuk mengetahui status terkini dari permohonan informasi publik yang telah diajukan.
        </p>
    </section>

    <!-- Tracking Input Section -->
    <section class="w-full max-w-3xl mx-auto">
        <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <form action="{{ route('permohonan.lacak') }}" class="flex flex-col gap-6" method="GET">
                <label class="text-sm font-semibold text-gray-700" for="tracking-number">Nomor Pendaftaran / Tiket</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-gray-400" data-icon="tag">tag</span>
                    <input class="w-full pl-14 pr-4 py-4 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-800 transition-all placeholder:text-gray-400 font-medium text-lg outline-none" id="tracking-number" name="tracking_id" value="{{ request('tracking_id') }}" placeholder="Contoh: REG-20240831123456-1234" required="" type="text">
                </div>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-2">
                    <a class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors" href="#">Lupa nomor pendaftaran?</a>
                    <button class="w-full sm:w-auto bg-[#03224d] text-white px-8 py-3.5 rounded-xl font-semibold hover:bg-[#0B1B3D] hover:-translate-y-0.5 transition-all shadow-[0_8px_16px_-4px_rgba(3,34,77,0.3)] flex items-center justify-center gap-2" type="submit">
                        <span class="material-symbols-outlined text-[20px]" data-icon="manage_search">manage_search</span>
                        Cari Permohonan
                    </button>
                </div>
            </form>
        </div>
    </section>

    @if(isset($searched) && !$permohonan)
    <section class="w-full max-w-3xl mx-auto mt-2">
        <div class="bg-red-50 border border-red-100 rounded-2xl p-8 text-center shadow-sm flex flex-col items-center">
            <span class="material-symbols-outlined text-4xl text-red-500 mb-3 bg-red-100 p-4 rounded-full">search_off</span>
            <h3 class="text-lg font-bold text-red-800 mb-2">Permohonan Tidak Ditemukan</h3>
            <p class="text-sm text-red-600 max-w-lg">Kami tidak dapat menemukan data permohonan dengan Nomor Registrasi <strong class="bg-white px-2 py-0.5 rounded shadow-sm border border-red-200 mx-1">{{ request('tracking_id') }}</strong>. Pastikan nomor yang dimasukkan sudah benar.</p>
        </div>
    </section>
    @endif

    <!-- FAQ Section -->
    <section class="w-full max-w-4xl mx-auto mt-12 pt-12 border-t border-gray-200/60">
        <div class="flex items-center justify-center gap-3 mb-8">
            <span class="material-symbols-outlined text-blue-600" data-icon="help" style="font-variation-settings: 'FILL' 1;">help</span>
            <h2 class="text-2xl font-bold text-[#0B1B3D]">Bantuan &amp; FAQ</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                <h3 class="text-base font-bold text-gray-800 mb-3">Berapa lama proses permohonan?</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Sesuai UU KIP, proses standar adalah 10 hari kerja, dan dapat diperpanjang 7 hari kerja dengan pemberitahuan.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                <h3 class="text-base font-bold text-gray-800 mb-3">Status menunjukkan "Ditangguhkan", apa maksudnya?</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Informasi yang diminta mungkin memerlukan klarifikasi lebih lanjut. Silakan cek email Anda untuk detail dari petugas.</p>
            </div>
        </div>
    </section>

@elseif(isset($searched) && $permohonan)
    <!-- Header Title -->
    <div class="text-center mb-2 mt-4">
        <h2 class="text-3xl font-bold text-[#0B1B3D] mb-2">Status Permohonan</h2>
        <p class="text-gray-500">Detail dan pelacakan tiket permohonan informasi Anda.</p>
    </div>

    <section class="w-full max-w-3xl mx-auto space-y-6">
        <!-- Card 1: Ticket Info -->
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start mb-6 gap-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">ID TIKET</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $permohonan->nomor_registrasi }}</h3>
                </div>
                <div>
                    @php
                        $statusClass = $permohonan->status->badgeClass();
                        $statusText = $permohonan->status->label();
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-bold shadow-sm {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-8 border-t border-gray-100 pt-6 mt-2">
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal Permohonan</p>
                    <p class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($permohonan->created_at)->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Nama Pemohon</p>
                    <p class="text-sm font-medium text-gray-800">{{ $permohonan->nama_pemohon }}</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Detail Permohonan -->
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8">
            <h3 class="text-lg font-bold text-[#0B1B3D] mb-5">Detail Permohonan</h3>
            <div class="space-y-6 border-t border-gray-100 pt-5">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Subjek Informasi</p>
                    <p class="text-[15px] font-medium text-gray-800 leading-relaxed">{{ $permohonan->subjek ?? 'Permohonan Informasi' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tujuan Penggunaan</p>
                    <p class="text-[15px] text-gray-700 leading-relaxed">{{ $permohonan->tujuan_penggunaan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Card 3: Riwayat Status -->
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-[#0B1B3D] mb-8">Riwayat Status</h3>
            <div class="relative ml-4 border-l-2 border-dashed border-gray-200 space-y-10">
                @forelse($permohonan->logs->reverse() as $index => $log)
                    <div class="relative pl-8 group">
                        @php
                            $iconBg = 'bg-[#03224d] text-white shadow-[0_0_0_4px_rgba(255,255,255,1),0_0_0_6px_rgba(3,34,77,0.1)]';
                        @endphp
                        
                        <span class="absolute flex items-center justify-center w-5 h-5 rounded-full -left-[11px] top-1 {{ $iconBg }} transition-transform group-hover:scale-125">
                            <span class="w-2 h-2 bg-white rounded-full"></span>
                        </span>
                        
                        <div class="bg-gray-50 border border-gray-100 p-4 rounded-2xl">
                            <h4 class="text-[15px] font-bold text-gray-900">{{ $log->aksi }}</h4>
                            <p class="text-xs font-medium text-gray-500 mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                {{ \Carbon\Carbon::parse($log->created_at)->isoFormat('D MMM Y, HH:mm') }} WIB
                            </p>
                            @if($log->catatan && $log->catatan != '-')
                                <div class="mt-3 bg-white p-3 rounded-xl border border-gray-100 text-[13px] text-gray-700 leading-relaxed shadow-sm">
                                    {{ $log->catatan }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="relative pl-8">
                        <span class="absolute flex items-center justify-center w-5 h-5 bg-gray-200 rounded-full -left-[11px] top-1 ring-4 ring-white"></span>
                        <div class="-mt-1">
                            <h4 class="text-[15px] font-semibold text-gray-500">Belum ada riwayat aktivitas.</h4>
                        </div>
                    </div>
                @endforelse
                
                @if(!$permohonan->status->isTerminal())
                <!-- Next Step Placeholder -->
                <div class="relative pl-8 opacity-60">
                    <span class="absolute flex items-center justify-center w-5 h-5 bg-gray-200 rounded-full -left-[11px] top-1 ring-4 ring-white animate-pulse"></span>
                    <div class="-mt-1 bg-gray-50/50 p-4 rounded-2xl border border-gray-100 border-dashed">
                        <h4 class="text-[15px] font-semibold text-gray-500">Menunggu Proses Selanjutnya</h4>
                        <p class="text-xs text-gray-400 mt-1">Sedang berlangsung...</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-center pt-4 mb-8 gap-4">
            <a href="{{ route('permohonan.lacak') }}" class="flex items-center gap-2 px-6 py-3 border border-gray-300 bg-white rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:-translate-y-0.5 transition-all w-full sm:w-auto text-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali
            </a>
            <a href="{{ route('permohonan.tanda_terima', $permohonan->nomor_registrasi) }}" target="_blank" class="flex items-center gap-2 px-6 py-3 bg-[#03224d] text-white rounded-xl text-sm font-semibold hover:bg-[#0B1B3D] hover:-translate-y-0.5 transition-all w-full sm:w-auto text-center justify-center shadow-[0_8px_16px_-4px_rgba(3,34,77,0.3)]">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Unduh Tanda Terima
            </a>
        </div>
        
        <div class="text-center mt-12 pb-12">
            <p class="text-sm text-gray-500">Ada kendala? <a href="#" class="text-blue-600 font-bold hover:underline hover:text-blue-800 transition-colors">Hubungi kami</a></p>
        </div>
    </section>
@endif

</main>
@endsection
