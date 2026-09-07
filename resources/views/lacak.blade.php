@extends('layouts.public')

@section('title', 'Lacak Status Permohonan - Bappeda PPID')

@section('content')
<main class="flex-grow w-full max-w-container-max mx-auto px-6 py-12 md:py-20 flex flex-col gap-8" x-data="{ openKeberatanModal: false }">

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="w-full max-w-3xl mx-auto bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-start gap-3 shadow-xs">
        <span class="material-symbols-outlined text-emerald-600 text-2xl shrink-0 mt-0.5">check_circle</span>
        <div class="text-sm">
            <p class="font-bold">Berhasil!</p>
            <p class="mt-0.5 leading-relaxed">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="w-full max-w-3xl mx-auto bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl flex items-start gap-3 shadow-xs">
        <span class="material-symbols-outlined text-red-600 text-2xl shrink-0 mt-0.5">error</span>
        <div class="text-sm">
            <p class="font-bold">Perhatian</p>
            <p class="mt-0.5 leading-relaxed">{{ session('error') }}</p>
        </div>
    </div>
    @endif

@if(!isset($searched) || (isset($searched) && !$permohonan))
    <!-- Hero Section -->
    <section class="text-center flex flex-col items-center max-w-2xl mx-auto w-full">
        <div class="bg-blue-50 text-blue-600 rounded-2xl w-20 h-20 flex items-center justify-center mb-6 shadow-sm border border-blue-100">
            <span class="material-symbols-outlined text-4xl" data-icon="search" style="font-variation-settings: 'FILL' 1;">search</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-[#0B1B3D] mb-4">Lacak Status Permohonan</h1>
        <p class="text-gray-500 text-lg max-w-xl leading-relaxed">
            Masukkan nomor registrasi atau tiket Anda untuk mengetahui status terkini dari permohonan informasi publik yang telah diajukan.
        </p>
    </section>

    <!-- Tracking Input Section -->
    <section class="w-full max-w-3xl mx-auto">
        <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <form action="{{ route('permohonan.lacak') }}" class="flex flex-col gap-6" method="GET">
                <label class="text-sm font-semibold text-gray-700" for="tracking-number">Nomor Registrasi / Tiket</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-gray-400" data-icon="tag">tag</span>
                    <input class="w-full pl-14 pr-4 py-4 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-800 transition-all placeholder:text-gray-400 font-medium text-lg outline-none" id="tracking-number" name="tracking_id" value="{{ request('tracking_id') }}" placeholder="Contoh: REG-20240831123456-1234" required="" type="text">
                </div>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-2">
                    <a class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors" href="#">Lupa nomor registrasi?</a>
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
                <p class="text-sm text-gray-500 leading-relaxed">Sesuai UU KIP No. 14/2008, proses standar adalah 10 hari kerja, dan dapat diperpanjang 7 hari kerja dengan pemberitahuan tertulis.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                <h3 class="text-base font-bold text-gray-800 mb-3">Kapan saya dapat mengajukan keberatan?</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Sesuai UU KIP Pasal 35, pemohon berhak mengajukan keberatan jika permohonan ditolak, tidak ditanggapi melewati batas waktu, atau biaya tidak wajar.</p>
            </div>
        </div>
    </section>

@elseif(isset($searched) && $permohonan)
    <!-- Header Title -->
    <div class="text-center mb-2 mt-4">
        <h2 class="text-3xl font-bold text-[#0B1B3D] mb-2">Status Permohonan Informasi</h2>
        <p class="text-gray-500">Detail dan pelacakan tiket permohonan informasi Anda secara real-time.</p>
    </div>

    <section class="w-full max-w-3xl mx-auto space-y-6">
        <!-- Card 1: Ticket Info -->
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start mb-6 gap-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">NOMOR REGISTRASI</p>
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
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Batas Waktu Jawaban</p>
                    <p class="text-sm font-medium {{ $permohonan->batas_waktu_jawaban && now()->gt($permohonan->batas_waktu_jawaban) ? 'text-red-600 font-bold' : 'text-gray-800' }}">
                        {{ $permohonan->batas_waktu_jawaban ? \Carbon\Carbon::parse($permohonan->batas_waktu_jawaban)->isoFormat('D MMMM Y') : 'Menunggu Verifikasi' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2: Detail Permohonan -->
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8">
            <h3 class="text-lg font-bold text-[#0B1B3D] mb-5">Detail Permohonan Informasi</h3>
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

        <!-- ── SECTION MEKANISME KEBERATAN (UU KIP NO. 14/2008) ── -->
        @if($permohonan->keberatan)
        <!-- Card: Status Keberatan Aktif -->
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border-2 border-red-200/80 p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-2 h-full bg-red-600"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-2xl">gavel</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-gray-900">Sengketa &amp; Pengajuan Keberatan</h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-800">UU KIP</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Diajukan pada {{ \Carbon\Carbon::parse($permohonan->keberatan->created_at)->isoFormat('D MMMM Y, HH:mm') }} WIB
                        </p>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $permohonan->keberatan->status->badgeClass() }}">
                        Status: {{ $permohonan->keberatan->status->label() }}
                    </span>
                </div>
            </div>

            <div class="space-y-4 pt-6 text-sm">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Alasan Keberatan</p>
                    <p class="font-bold text-gray-800">{{ $permohonan->keberatan->alasan_keberatan }}</p>
                </div>

                @if($permohonan->keberatan->keterangan_tambahan)
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Keterangan / Kronologi Pemohon</p>
                    <p class="text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100 text-xs leading-relaxed">
                        {{ $permohonan->keberatan->keterangan_tambahan }}
                    </p>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100">
                        <p class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Batas Waktu Respon Atasan</p>
                        <p class="text-sm font-bold text-blue-900 mt-1">
                            {{ $permohonan->keberatan->batas_waktu_respon ? \Carbon\Carbon::parse($permohonan->keberatan->batas_waktu_respon)->isoFormat('D MMMM Y') : '30 Hari Kerja' }}
                        </p>
                        <p class="text-[11px] text-blue-600 mt-0.5">Sesuai Pasal 37 UU KIP No. 14/2008</p>
                    </div>

                    @if($permohonan->keberatan->bukti_pendukung_path)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Berkas Pendukung</p>
                            <p class="text-xs font-medium text-slate-700 mt-1">Bukti lampiran keberatan</p>
                        </div>
                        <a href="{{ Storage::url($permohonan->keberatan->bukti_pendukung_path) }}" target="_blank"
                           class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:underline">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            Lihat
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Tanggapan Atasan PPID -->
                @if($permohonan->keberatan->tanggapan_atasan)
                <div class="mt-4 p-5 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs mb-2">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        Keputusan / Tanggapan Atasan PPID Pelaksana:
                    </div>
                    <p class="text-xs text-emerald-950 leading-relaxed">
                        {{ $permohonan->keberatan->tanggapan_atasan }}
                    </p>
                    @if($permohonan->keberatan->diputuskan_at)
                    <p class="text-[10px] text-emerald-700 mt-2">
                        Diputuskan pada: {{ \Carbon\Carbon::parse($permohonan->keberatan->diputuskan_at)->isoFormat('D MMMM Y, HH:mm') }} WIB
                    </p>
                    @endif
                </div>
                @else
                <div class="mt-2 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-amber-600">hourglass_top</span>
                    <span>Keberatan Anda saat ini sedang dalam proses pemeriksaan dan telaah oleh <strong>Atasan PPID Pelaksana</strong>.</span>
                </div>
                @endif
            </div>
        </div>
        @else
        <!-- Banner Opsi Pengajuan Keberatan Mandiri Pemohon -->
        <div class="bg-gradient-to-r from-slate-50 via-blue-50/50 to-indigo-50/50 rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-[11px] font-bold mb-2">
                    <span class="material-symbols-outlined text-[14px]">shield_person</span>
                    Mekanisme Keberatan &bull; UU KIP No. 14/2008
                </div>
                <h4 class="text-base font-bold text-[#0B1B3D]">Ada Masalah / Ketidaksesuaian Layanan?</h4>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Sesuai Pasal 35 UU No. 14 Tahun 2008, Anda berhak mengajukan keberatan tertulis kepada Atasan PPID jika permohonan ditolak, tidak ditanggapi melewati batas waktu, atau materi yang diberikan tidak sesuai.
                </p>
            </div>
            <button @click="openKeberatanModal = true" 
                    type="button"
                    class="shrink-0 inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition-all shadow-sm shadow-red-600/25">
                <span class="material-symbols-outlined text-[18px]">gavel</span>
                Ajukan Keberatan
            </button>
        </div>
        @endif

        <!-- Card 3: Riwayat Status -->
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-[#0B1B3D] mb-8">Riwayat Status Permohonan</h3>
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

    <!-- ── MODAL PENGAJUAN KEBERATAN MANDIRI ── -->
    <div x-show="openKeberatanModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 md:p-8 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto"
             @click.away="openKeberatanModal = false">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">gavel</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Formulir Pengajuan Keberatan</h3>
                        <p class="text-xs text-slate-500">Sesuai UU KIP No. 14 Tahun 2008 Pasal 35</p>
                    </div>
                </div>
                <button @click="openKeberatanModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <form action="{{ route('permohonan.keberatan.store', $permohonan->nomor_registrasi) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Registrasi Permohonan</label>
                    <input type="text" value="{{ $permohonan->nomor_registrasi }}" readonly 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 text-xs font-bold outline-none">
                </div>

                <!-- 7 Klausul Resmi UU KIP Pasal 35 -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alasan Keberatan (UU KIP Pasal 35) <span class="text-red-500">*</span>
                    </label>
                    <select name="alasan_keberatan" required 
                            data-placeholder="-- Pilih Alasan Keberatan --"
                            data-search-placeholder="Cari alasan keberatan UU KIP..."
                            class="custom-select w-full text-xs font-medium">
                        <option value="">-- Pilih Alasan Keberatan --</option>
                        <option value="Penolakan atas permohonan informasi">Penolakan atas permohonan informasi</option>
                        <option value="Tidak disediakannya informasi berkala">Tidak disediakannya informasi berkala</option>
                        <option value="Tidak ditanggapinya permohonan informasi">Tidak ditanggapinya permohonan informasi</option>
                        <option value="Permohonan informasi ditanggapi tidak sebagaimana yang diminta">Permohonan informasi ditanggapi tidak sebagaimana yang diminta</option>
                        <option value="Tidak dipenuhinya permohonan informasi">Tidak dipenuhinya permohonan informasi</option>
                        <option value="Pengenaan biaya yang tidak wajar">Pengenaan biaya yang tidak wajar</option>
                        <option value="Penyampaian informasi yang melebihi waktu yang diatur">Penyampaian informasi yang melebihi waktu yang diatur (10+7 hari)</option>
                    </select>
                </div>

                <!-- Keterangan Tambahan / Kronologi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Penjelasan / Kronologi Keberatan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keterangan_tambahan" rows="4" required 
                              placeholder="Uraikan secara jelas keberatan Anda, fakta yang terjadi, dan tanggapan yang diharapkan dari Atasan PPID..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none"></textarea>
                </div>

                <!-- Unggah Berkas Pendukung -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Unggah Bukti / Dokumen Pendukung (Opsional)
                    </label>
                    <input type="file" name="file_pendukung" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Maksimal 10 MB (Format: PDF, JPG, PNG)</p>
                </div>

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-800 leading-relaxed">
                    <strong>Catatan Regulasi:</strong> Atasan PPID Pelaksana berkewajiban memberikan tanggapan tertulis atas keberatan ini dalam waktu maksimal <strong>30 hari kerja</strong> sejak pengajuan diterima.
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="openKeberatanModal = false" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-colors shadow-sm shadow-red-600/25">
                        Kirim Pengajuan Keberatan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

</main>
@endsection
