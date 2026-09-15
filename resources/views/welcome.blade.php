@extends('layouts.public')

@section('content')
<!-- Main Content -->
<main x-data="{
    previewOpen: false,
    previewUrl: '',
    previewTitle: '',
    previewKategori: '',
    previewJenis: '',
    previewTahun: '',
    previewDownloadUrl: '',
    previewPenanggungJawab: '',
    previewFileSize: '',
    isLoading: true,
    openPreview(url, title, kategori, jenis, tahun, downloadUrl, penanggungJawab, fileSize) {
        this.previewUrl = url;
        this.previewTitle = title;
        this.previewKategori = kategori;
        this.previewJenis = jenis;
        this.previewTahun = tahun;
        this.previewDownloadUrl = downloadUrl;
        this.previewPenanggungJawab = penanggungJawab;
        this.previewFileSize = fileSize;
        this.isLoading = true;
        this.previewOpen = true;
        document.body.classList.add('overflow-hidden');
    },
    closePreview() {
        this.previewOpen = false;
        document.body.classList.remove('overflow-hidden');
        setTimeout(() => {
            this.previewUrl = '';
            this.isLoading = true;
        }, 300);
    }
}" @keydown.escape.window="closePreview()" class="flex-grow flex flex-col items-center w-full">
<!-- Hero Carousel Section -->
@if(isset($carousels) && $carousels->count() > 0)
<section class="relative w-full pt-4 sm:pt-6 pb-8 sm:pb-12 px-4 sm:px-6 max-w-container-max mx-auto"
         x-data="{
             current: 0,
             total: {{ $carousels->count() }},
             timer: null,
             isPaused: false,
             touchStartX: 0,
             touchEndX: 0,
             init() {
                 this.startAutoplay();
             },
             startAutoplay() {
                 this.stopAutoplay();
                 this.timer = setInterval(() => {
                     if (!this.isPaused) {
                         this.next();
                     }
                 }, 6000);
             },
             stopAutoplay() {
                 if (this.timer) clearInterval(this.timer);
             },
             next() {
                 this.current = (this.current + 1) % this.total;
             },
             prev() {
                 this.current = (this.current - 1 + this.total) % this.total;
             },
             goTo(index) {
                 this.current = index;
                 this.startAutoplay();
             },
             handleTouchStart(e) {
                 this.touchStartX = e.changedTouches[0].screenX;
             },
             handleTouchEnd(e) {
                 this.touchEndX = e.changedTouches[0].screenX;
                 if (this.touchStartX - this.touchEndX > 50) {
                     this.next();
                 } else if (this.touchEndX - this.touchStartX > 50) {
                     this.prev();
                 }
             }
         }"
         @mouseenter="isPaused = true"
         @mouseleave="isPaused = false"
         @touchstart="handleTouchStart($event)"
         @touchend="handleTouchEnd($event)">
    
    <!-- Carousel Stage Container -->
    <div class="relative w-full rounded-2xl sm:rounded-3xl overflow-hidden shadow-[0_20px_50px_-12px_rgba(3,34,77,0.25)] border border-slate-200/80 bg-[#031b3d] min-h-[460px] sm:min-h-[500px] md:min-h-[540px] flex items-center">
        
        @foreach($carousels as $index => $slide)
        <!-- Slide Item -->
        <div x-show="current === {{ $index }}"
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 scale-105"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-500"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute inset-0 w-full h-full flex flex-col justify-center"
             style="{{ $index === 0 ? '' : 'display: none;' }}">
            
            <!-- Slide Background Artwork -->
            <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}" class="absolute inset-0 w-full h-full object-cover select-none pointer-events-none">
            
            <!-- High-End Multi-Layer Gradient Overlays for Crystal Clear Typography -->
            <div class="absolute inset-0 bg-gradient-to-t sm:bg-gradient-to-r from-[#021533]/95 via-[#03224d]/85 sm:via-[#03224d]/75 to-[#03224d]/30 sm:to-transparent z-10"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-blue-900/40 via-transparent to-transparent z-10"></div>
            
            <!-- Slide Content Box -->
            <div class="relative z-20 max-w-3xl px-6 sm:px-12 md:px-16 py-12 flex flex-col items-start text-left">
                <!-- Headline Title -->
                <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-[50px] font-bold text-white tracking-tight leading-[1.15] mb-4 drop-shadow-sm">
                    {{ $slide->title }}
                </h1>

                <!-- Subtitle / Description -->
                @if($slide->subtitle)
                <p class="text-sm sm:text-base md:text-lg text-slate-200/90 max-w-2xl leading-relaxed mb-6 sm:mb-8 font-normal drop-shadow-xs">
                    {{ $slide->subtitle }}
                </p>
                @endif

                <!-- Action CTA Buttons -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 w-full sm:w-auto">
                    @if($slide->button_text && $slide->button_url)
                    <a href="{{ $slide->button_url }}" 
                       target="{{ $slide->button_target }}"
                       class="group bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm px-6 py-3.5 rounded-xl shadow-[0_10px_25px_-5px_rgba(37,99,235,0.4)] hover:shadow-[0_14px_28px_-6px_rgba(37,99,235,0.6)] hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 w-full sm:w-auto cursor-pointer">
                        <span>{{ $slide->button_text }}</span>
                        <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                    @endif

                    <a href="{{ route('permohonan.lacak') }}" 
                       class="group bg-white/10 hover:bg-white/20 border border-white/25 text-white font-semibold text-xs sm:text-sm px-5 py-3.5 rounded-xl backdrop-blur-md shadow-xs hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 w-full sm:w-auto">
                        <span class="material-symbols-outlined text-[18px]">travel_explore</span>
                        <span>Lacak Status</span>
                    </a>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Navigation Arrows (Desktop / Tablet) -->
        <div class="absolute inset-y-0 left-3 sm:left-4 z-30 flex items-center">
            <button type="button" 
                    @click="prev()" 
                    aria-label="Slide Sebelumnya"
                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-slate-900/40 hover:bg-white/90 text-white hover:text-slate-900 border border-white/20 hover:border-white flex items-center justify-center backdrop-blur-md transition-all duration-200 shadow-md cursor-pointer hover:scale-105 active:scale-95">
                <span class="material-symbols-outlined text-[20px] sm:text-[22px]">chevron_left</span>
            </button>
        </div>

        <div class="absolute inset-y-0 right-3 sm:right-4 z-30 flex items-center">
            <button type="button" 
                    @click="next()" 
                    aria-label="Slide Selanjutnya"
                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-slate-900/40 hover:bg-white/90 text-white hover:text-slate-900 border border-white/20 hover:border-white flex items-center justify-center backdrop-blur-md transition-all duration-200 shadow-md cursor-pointer hover:scale-105 active:scale-95">
                <span class="material-symbols-outlined text-[20px] sm:text-[22px]">chevron_right</span>
            </button>
        </div>

        <!-- Bottom Controls Bar (Pagination Indicators & Counter) -->
        <div class="absolute bottom-4 sm:bottom-6 inset-x-0 z-30 flex items-center justify-between px-6 sm:px-12 pointer-events-none">
            <!-- Indicator Dots -->
            <div class="flex items-center gap-2 pointer-events-auto bg-slate-950/40 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                @foreach($carousels as $index => $slide)
                <button type="button" 
                        @click="goTo({{ $index }})" 
                        :aria-label="'Buka Slide ' + ({{ $index }} + 1)"
                        class="transition-all duration-300 rounded-full cursor-pointer h-2"
                        :class="current === {{ $index }} ? 'w-7 bg-blue-400 shadow-sm' : 'w-2 bg-white/40 hover:bg-white/70'"></button>
                @endforeach
            </div>

            <!-- Slide Counter Badge -->
            <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-mono font-bold text-white/80 bg-slate-950/40 backdrop-blur-md px-3 py-1 rounded-full border border-white/10">
                <span class="text-blue-300" x-text="String(current + 1).padStart(2, '0')">01</span>
                <span class="text-white/40">/</span>
                <span x-text="String(total).padStart(2, '0')">{{ str_pad($carousels->count(), 2, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>
</section>
@else
<!-- Fallback Hero Section -->
<section class="relative w-full py-12 sm:py-20 md:py-32 flex flex-col items-center text-center px-4 sm:px-6 border-b border-gray-200/50 overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_center,_var(--tw-gradient-stops))] from-blue-100/50 via-slate-50/20 to-transparent -z-10"></div>
    <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-blue-200 to-transparent opacity-50"></div>
    
    <div class="max-w-container-max mx-auto flex flex-col items-center gap-6 sm:gap-8 relative z-10">
        <h1 class="text-[30px] sm:text-[42px] md:text-[56px] leading-[1.15] font-bold text-[#0B1B3D] max-w-4xl tracking-tight">
            Ajukan &amp; Pantau Permohonan <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-blue-500">Informasi Publik</span>
        </h1>
        <p class="text-sm sm:text-base md:text-xl text-gray-600 max-w-2xl leading-relaxed px-2">
            Layanan informasi publik yang transparan, akuntabel, dan mudah diakses. Kami berkomitmen menyediakan informasi pemerintahan daerah secara cepat dan tepat saji.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-2 sm:mt-6 w-full sm:w-auto max-w-md sm:max-w-none">
            <a href="{{ route('permohonan.create') }}" class="group bg-[#03224d] text-white font-semibold text-sm px-6 py-3.5 sm:px-8 sm:py-4 rounded-xl shadow-[0_8px_20px_-6px_rgba(3,34,77,0.4)] hover:shadow-[0_12px_24px_-8px_rgba(3,34,77,0.5)] hover:bg-[#0B1B3D] hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 w-full sm:w-auto">
                <span class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform">add_circle</span>
                Ajukan Permohonan Baru
            </a>
            <a href="{{ route('permohonan.lacak') }}" class="group bg-white border border-gray-200 text-gray-700 font-semibold text-sm px-6 py-3.5 sm:px-8 sm:py-4 rounded-xl hover:border-blue-200 hover:bg-blue-50/50 hover:text-blue-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 w-full sm:w-auto">
                <span class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform">search</span>
                Lacak Status Permohonan
            </a>
        </div>
    </div>
</section>
@endif

<!-- Categories Section -->
<section class="w-full py-12 sm:py-20 px-4 sm:px-6 max-w-container-max mx-auto flex flex-col gap-8 sm:gap-12">
    <div class="text-center flex flex-col gap-2 sm:gap-3">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#0B1B3D]">Kategori Informasi Publik</h2>
        <p class="text-sm sm:text-base text-gray-500 max-w-xl mx-auto">Pilih kategori yang sesuai dengan informasi yang Anda butuhkan.</p>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-2 sm:mt-4">
        @foreach($kategoriInformasi as $kategori)
        @php
            $targetUrl = $kategori->nama_kategori == 'Keberatan' 
                ? route('permohonan.lacak') 
                : route('informasi-publik.index', ['kategori' => $kategori->id]);
        @endphp
        <!-- Card -->
        <a href="{{ $targetUrl }}" class="bg-white border border-gray-100 p-6 sm:p-8 rounded-2xl flex flex-col items-center text-center gap-4 sm:gap-5 hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.06)] hover:border-blue-200 hover:-translate-y-1.5 transition-all duration-300 group cursor-pointer relative overflow-hidden active:scale-[0.98]">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 rounded-bl-full transition-opacity duration-500 -z-0"></div>
            
            <div class="relative z-10 w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[28px] sm:text-[32px]">{{ $kategori->icon }}</span>
            </div>
            
            <div class="relative z-10">
                <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-1.5 sm:mb-2 group-hover:text-blue-700 transition-colors">{{ $kategori->nama_kategori }}</h3>
                <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">{{ $kategori->deskripsi }}</p>
                <div class="mt-3 sm:mt-4 flex items-center justify-center gap-1 text-xs font-bold text-blue-600 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                    <span>Lihat Dokumen</span>
                    <span class="material-symbols-outlined text-[15px] sm:text-[16px]">arrow_forward</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Quick Access: Dokumen Perencanaan Utama Bapperida -->
    <div class="mt-6 sm:mt-12 p-5 sm:p-8 rounded-2xl sm:rounded-3xl bg-slate-50 border border-slate-200/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 mb-5 sm:mb-6">
            <div>
                <span class="text-[11px] sm:text-xs font-bold text-blue-600 uppercase tracking-wider">Produk Utama Bapperida</span>
                <h3 class="text-lg sm:text-xl font-bold text-[#0B1B3D] mt-0.5">Dokumen Perencanaan Paling Sering Dicari</h3>
            </div>
            <a href="{{ route('informasi-publik.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 self-start sm:self-auto">
                <span>Lihat Semua Dokumen</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 sm:gap-3">
            <a href="{{ route('informasi-publik.index', ['jenis' => 'RPJPD']) }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">RPJPD Ciamis</span>
                <span class="text-[10px] text-slate-400">Rencana 20 Tahun</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'RPJMD']) }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">RPJMD Ciamis</span>
                <span class="text-[10px] text-slate-400">Rencana 5 Tahun</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'RKPD']) }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">RKPD 2025</span>
                <span class="text-[10px] text-slate-400">Rencana Tahunan</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'RTRW']) }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">RTRW & RDTR</span>
                <span class="text-[10px] text-slate-400">Tata Ruang Daerah</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'Kajian & Riset']) }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">Kajian & Riset</span>
                <span class="text-[10px] text-slate-400">Makro & Sosial</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'Roadmap SIDa']) }}" class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">Roadmap SIDa</span>
                <span class="text-[10px] text-slate-400">Sistem Inovasi</span>
            </a>
        </div>

        @if(isset($dokumenDIP) && $dokumenDIP->count() > 0)
        <!-- Featured DIP Documents with Direct Preview & Download -->
        <div class="mt-8 pt-6 border-t border-slate-200/80">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600 text-[18px]">verified</span>
                    <h4 class="text-sm font-bold text-slate-800">Katalog Dokumen Resmi Terpopuler</h4>
                </div>
                <span class="text-xs text-slate-400">Siap diakses &amp; dipratinjau langsung</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($dokumenDIP as $doc)
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-blue-300 hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-2.5">
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $doc->kategori?->nama_kategori ?? 'DIP' }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                {{ $doc->tahun }}
                            </span>
                        </div>
                        <h5 @click="openPreview('{{ route('informasi-publik.download', $doc->id) }}?inline=1', '{{ addslashes($doc->judul) }}', '{{ $doc->kategori?->nama_kategori }}', '{{ $doc->jenis_dokumen }}', '{{ $doc->tahun }}', '{{ route('informasi-publik.download', $doc->id) }}', '{{ addslashes($doc->penanggung_jawab) }}', '{{ $doc->file_size ?? 'PDF' }}')"
                            class="text-xs font-bold text-slate-800 group-hover:text-blue-600 line-clamp-2 leading-snug mb-2 cursor-pointer transition-colors"
                            title="Klik untuk preview dokumen">
                            {{ $doc->judul }}
                        </h5>
                        <p class="text-[11px] text-slate-400 line-clamp-2 leading-relaxed mb-3">
                            {{ $doc->ringkasan ?? 'Dokumen resmi perencanaan pembangunan dan publikasi Bapperida Kabupaten Ciamis.' }}
                        </p>
                    </div>

                    <div class="mt-2">
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 font-medium mb-3">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">description</span>
                                <span class="whitespace-nowrap">{{ $doc->file_size ?? 'PDF' }}</span>
                            </span>
                            <span class="truncate max-w-[150px] text-slate-400 font-normal">
                                {{ $doc->penanggung_jawab }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" 
                                    @click="openPreview('{{ route('informasi-publik.download', $doc->id) }}?inline=1', '{{ addslashes($doc->judul) }}', '{{ $doc->kategori?->nama_kategori }}', '{{ $doc->jenis_dokumen }}', '{{ $doc->tahun }}', '{{ route('informasi-publik.download', $doc->id) }}', '{{ addslashes($doc->penanggung_jawab) }}', '{{ $doc->file_size ?? 'PDF' }}')"
                                    class="w-full inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200/80 hover:border-blue-200 transition-all cursor-pointer shadow-2xs">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                <span>Preview</span>
                            </button>
                            <a href="{{ route('informasi-publik.download', $doc->id) }}" 
                               class="w-full inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] transition-all shadow-2xs hover:scale-[1.02]">
                                <span class="material-symbols-outlined text-[14px]">download</span>
                                <span>Unduh</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

<!-- PDF Preview Modal -->
<div x-show="previewOpen" 
     style="display: none;" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     aria-labelledby="modal-preview-title-home" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop with blur -->
    <div x-show="previewOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" 
         @click="closePreview()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-3 sm:p-6 text-center">
            
            <!-- Modal Dialog Box -->
            <div x-show="previewOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl md:rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-5xl flex flex-col h-[88vh] border border-slate-200">
                
                <!-- Modal Header -->
                <div class="bg-white px-5 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3 min-w-0 pr-4">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-100 shadow-2xs">
                            <span class="material-symbols-outlined text-[22px]">picture_as_pdf</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider" x-text="previewKategori"></span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200" x-text="previewJenis"></span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-50 text-slate-500 border border-slate-200" x-text="'Tahun ' + previewTahun"></span>
                            </div>
                            <h3 class="text-sm md:text-base font-bold text-slate-900 truncate" id="modal-preview-title-home" x-text="previewTitle"></h3>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 shrink-0">
                        <a :href="previewUrl" target="_blank" 
                           class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 text-xs font-semibold transition-colors" title="Buka di tab baru">
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                            <span>Tab Baru</span>
                        </a>
                        <button type="button" @click="closePreview()" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer" title="Tutup Modal (ESC)">
                            <span class="material-symbols-outlined text-[22px]">close</span>
                        </button>
                    </div>
                </div>

                <!-- Modal Body (Iframe) -->
                <div class="flex-1 bg-slate-100 p-2 md:p-3 overflow-hidden relative">
                    <!-- Loading Spinner -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50/80 z-20 transition-opacity" x-show="isLoading">
                        <div class="w-10 h-10 border-4 border-slate-200 border-t-blue-600 rounded-full animate-spin mb-3"></div>
                        <p class="text-xs font-semibold text-slate-600">Memuat berkas dokumen...</p>
                    </div>
                    
                    <iframe :src="previewUrl" 
                            @load="isLoading = false"
                            class="w-full h-full rounded-xl border border-slate-200 bg-white shadow-xs relative z-10" 
                            title="Preview Dokumen PDF"></iframe>
                </div>

                <!-- Modal Footer -->
                <div class="bg-slate-50/90 px-5 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100 shrink-0">
                    <div class="flex items-center gap-2 text-xs text-slate-500 w-full sm:w-auto truncate">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">domain</span>
                        <span class="truncate font-medium" x-text="previewPenanggungJawab"></span>
                    </div>

                    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                        <button type="button" @click="closePreview()" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200 transition-colors cursor-pointer">
                            Tutup
                        </button>
                        <a :href="previewDownloadUrl" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-sm hover:scale-[1.02] cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            <span>Unduh Dokumen</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</main>
@endsection
