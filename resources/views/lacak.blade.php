@extends('layouts.public')

@section('title', 'Lacak Status Permohonan - Bappeda PPID')

@section('content')
<main class="flex-grow w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-stack-lg flex flex-col gap-stack-lg">

@if(!isset($searched) || (isset($searched) && !$permohonan))
    <!-- Hero Section -->
    <section class="text-center py-stack-lg flex flex-col items-center max-w-2xl mx-auto w-full">
        <div class="bg-primary-container text-on-primary-container rounded-full w-16 h-16 flex items-center justify-center mb-stack-md">
            <span class="material-symbols-outlined text-3xl" data-icon="search" style="font-variation-settings: 'FILL' 1;">search</span>
        </div>
        <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-stack-sm">Lacak Status Permohonan</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
            Masukkan nomor pendaftaran atau tiket Anda untuk mengetahui status terkini dari permohonan informasi publik yang telah diajukan.
        </p>
    </section>

    <!-- Tracking Input Section -->
    <section class="w-full max-w-3xl mx-auto">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-stack-lg shadow-sm">
            <form action="{{ route('permohonan.lacak') }}" class="flex flex-col gap-stack-md" method="GET">
                <label class="font-label-md text-label-md text-on-surface font-semibold" for="tracking-number">Nomor Pendaftaran / Tiket</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline" data-icon="tag">tag</span>
                    <input class="w-full pl-12 pr-4 py-4 rounded-lg border border-outline-variant bg-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container font-body-md text-body-md text-on-surface transition-colors" id="tracking-number" name="tracking_id" value="{{ request('tracking_id') }}" placeholder="Contoh: REG-20240831123456-1234" required="" type="text">
                </div>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-unit">
                    <a class="font-label-sm text-label-sm text-primary underline hover:text-primary-container transition-colors" href="#">Lupa nomor pendaftaran?</a>
                    <button class="w-full sm:w-auto bg-primary-container text-on-primary px-8 py-3 rounded-lg font-label-md text-label-md hover:bg-primary transition-colors flex items-center justify-center gap-2 shadow-sm" type="submit">
                        <span class="material-symbols-outlined text-[18px]" data-icon="manage_search">manage_search</span>
                        Cari Permohonan
                    </button>
                </div>
            </form>
        </div>
    </section>

    @if(isset($searched) && !$permohonan)
    <section class="w-full max-w-3xl mx-auto mt-6">
        <div class="bg-red-50 border border-red-200 rounded-xl p-8 text-center shadow-sm">
            <span class="material-symbols-outlined text-4xl text-red-400 mb-3">search_off</span>
            <h3 class="text-lg font-bold text-red-800 mb-2">Permohonan Tidak Ditemukan</h3>
            <p class="text-sm text-red-600">Kami tidak dapat menemukan data permohonan dengan Nomor Registrasi <strong>{{ request('tracking_id') }}</strong>. Pastikan nomor yang dimasukkan sudah benar.</p>
        </div>
    </section>
    @endif

    <!-- FAQ Section -->
    <section class="w-full max-w-4xl mx-auto mt-stack-lg pt-stack-lg border-t border-surface-container-high">
        <div class="flex items-center gap-3 mb-stack-md">
            <span class="material-symbols-outlined text-primary" data-icon="help" style="font-variation-settings: 'FILL' 1;">help</span>
            <h2 class="font-headline-md text-headline-md text-primary">Bantuan &amp; FAQ</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
            <div class="bg-surface-container-lowest p-stack-md rounded-lg border border-outline-variant hover:shadow-sm transition-shadow">
                <h3 class="font-label-md text-label-md font-semibold text-on-surface mb-unit">Berapa lama proses permohonan?</h3>
                <p class="font-body-md text-body-md text-on-surface-variant text-sm">Sesuai UU KIP, proses standar adalah 10 hari kerja, dan dapat diperpanjang 7 hari kerja dengan pemberitahuan.</p>
            </div>
            <div class="bg-surface-container-lowest p-stack-md rounded-lg border border-outline-variant hover:shadow-sm transition-shadow">
                <h3 class="font-label-md text-label-md font-semibold text-on-surface mb-unit">Status menunjukkan "Ditangguhkan", apa maksudnya?</h3>
                <p class="font-body-md text-body-md text-on-surface-variant text-sm">Informasi yang diminta mungkin memerlukan klarifikasi lebih lanjut. Silakan cek email Anda untuk detail dari petugas.</p>
            </div>
        </div>
    </section>

@elseif(isset($searched) && $permohonan)
    <!-- Header Title -->
    <div class="text-center mb-4 mt-6">
        <h2 class="text-[28px] font-bold text-[#03224d] mb-2">Status Permohonan</h2>
        <p class="text-[15px] text-gray-500">Detail dan pelacakan tiket permohonan informasi Anda.</p>
    </div>

    <section class="w-full max-w-3xl mx-auto space-y-6">
        <!-- Card 1: Ticket Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start mb-6 gap-4">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">ID TIKET</p>
                    <h3 class="text-[22px] font-bold text-[#03224d]">{{ $permohonan->nomor_registrasi }}</h3>
                </div>
                <div>
                    @php
                        $statusClass = 'bg-gray-100 text-gray-700';
                        $statusText = $permohonan->status;
                        
                        if (in_array($permohonan->status, ['Masuk', 'Diproses'])) {
                            $statusClass = 'bg-[#ffc329] text-yellow-900';
                            $statusText = 'Sedang Diproses';
                        } elseif ($permohonan->status == 'Selesai') {
                            $statusClass = 'bg-green-100 text-green-700';
                        } elseif ($permohonan->status == 'Ditolak') {
                            $statusClass = 'bg-red-100 text-red-700';
                        }
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-[13px] font-semibold {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-8 border-t border-gray-100 pt-5">
                <div class="flex-1">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Permohonan</p>
                    <p class="text-[14px] text-gray-800">{{ \Carbon\Carbon::parse($permohonan->created_at)->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
                </div>
                <div class="flex-1">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Pemohon</p>
                    <p class="text-[14px] text-gray-800">{{ $permohonan->nama_pemohon }}</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Detail Permohonan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-[#03224d] mb-4">Detail Permohonan</h3>
            <div class="space-y-5 border-t border-gray-100 pt-5">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Subjek Informasi</p>
                    <p class="text-[15px] text-gray-800 leading-relaxed">{{ $permohonan->subjek ?? 'Permohonan Informasi' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tujuan Penggunaan</p>
                    <p class="text-[15px] text-gray-800 leading-relaxed">{{ $permohonan->tujuan_penggunaan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Card 4: Riwayat Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h3 class="text-[20px] font-bold text-[#03224d] mb-8">Riwayat Status</h3>
            <div class="relative ml-4 border-l-[3px] border-gray-100 space-y-8">
                @forelse($permohonan->logs->reverse() as $index => $log)
                    <div class="relative pl-8">
                        @php
                            // The last log is the newest because of reverse(), or first is newest?
                            // Actually permohonan->logs is ordered by created_at desc (from model)
                            // We want chronological order (oldest first) in timeline? The design shows newest on top or oldest?
                            // Design shows: "Permohonan Diterima" 15 Okt, "Verifikasi Administrasi" 16 Okt. (Oldest first)
                            // So let's use reverse() to make it chronological if it was desc.
                            
                            $isDone = true; 
                            $icon = 'check';
                            $iconBg = 'bg-[#03224d] text-white';
                        @endphp
                        
                        <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-[14px] top-0 ring-[6px] ring-white {{ $iconBg }}">
                            <span class="material-symbols-outlined text-[14px] font-bold">{{ $icon }}</span>
                        </span>
                        
                        <div class="-mt-1">
                            <h4 class="text-[15px] font-semibold text-[#03224d]">{{ $log->aksi }}</h4>
                            <p class="text-[13px] text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($log->created_at)->isoFormat('D MMM Y, HH:mm') }} WIB</p>
                            @if($log->catatan && $log->catatan != '-')
                                <p class="text-[13px] text-gray-600 mt-2">{{ $log->catatan }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="relative pl-8">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-200 rounded-full -left-[14px] top-0 ring-[6px] ring-white"></span>
                        <div class="-mt-1">
                            <h4 class="text-[15px] font-semibold text-gray-500">Belum ada riwayat aktivitas.</h4>
                        </div>
                    </div>
                @endforelse
                
                @if(in_array($permohonan->status, ['Masuk', 'Diproses']))
                <!-- Next Step Placeholder (Sedang berlangsung) -->
                <div class="relative pl-8 opacity-60">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-200 text-gray-500 rounded-full -left-[14px] top-0 ring-[6px] ring-white">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                    </span>
                    <div class="-mt-1">
                        <h4 class="text-[15px] font-semibold text-gray-500">Menunggu Proses Selanjutnya</h4>
                        <p class="text-[13px] text-gray-400 mt-0.5">Sedang berlangsung...</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-center pt-6 mb-8">
            <a href="{{ route('permohonan.lacak') }}" class="flex items-center gap-2 px-6 py-2.5 border border-gray-300 bg-white rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto text-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali
            </a>
            <a href="{{ route('permohonan.tanda_terima', $permohonan->nomor_registrasi) }}" target="_blank" class="flex items-center gap-2 px-6 py-2.5 bg-[#03224d] text-white rounded-lg text-sm font-semibold hover:bg-[#1f3864] transition-colors w-full sm:w-auto mt-4 sm:mt-0 text-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Unduh Tanda Terima
            </a>
        </div>
        
        <div class="text-center mt-12 pb-12">
            <p class="text-[14px] text-gray-600">Ada kendala? <a href="#" class="text-[#03224d] font-bold hover:underline">Hubungi kami</a></p>
        </div>
    </section>
@endif

</main>
@endsection
