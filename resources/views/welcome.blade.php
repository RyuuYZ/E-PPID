@extends('layouts.public')

@section('content')
<!-- Main Content -->
<main class="flex-grow flex flex-col items-center w-full">
<!-- Hero Section -->
<section class="relative w-full py-20 md:py-32 flex flex-col items-center text-center px-6 border-b border-gray-200/50 overflow-hidden">
    <!-- Subtle Background Gradient -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_center,_var(--tw-gradient-stops))] from-blue-100/50 via-slate-50/20 to-transparent -z-10"></div>
    <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-blue-200 to-transparent opacity-50"></div>
    
    <div class="max-w-container-max mx-auto flex flex-col items-center gap-8 relative z-10">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold tracking-wide mb-2 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            Layanan Informasi Publik Terpadu
        </div>
        <h1 class="font-display-lg text-[36px] md:text-[56px] leading-[1.1] font-bold text-[#0B1B3D] max-w-4xl tracking-tight">
            Ajukan & Pantau Permohonan <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-blue-500">Informasi Publik</span>
        </h1>
        <p class="font-body-lg text-lg md:text-xl text-gray-600 max-w-2xl leading-relaxed">
            Layanan informasi publik yang transparan, akuntabel, dan mudah diakses. Kami berkomitmen menyediakan informasi pemerintahan daerah secara cepat dan tepat saji.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 mt-6 w-full sm:w-auto">
            <a href="{{ route('permohonan.create') }}" class="group bg-[#03224d] text-white font-semibold text-sm px-8 py-4 rounded-xl shadow-[0_8px_20px_-6px_rgba(3,34,77,0.4)] hover:shadow-[0_12px_24px_-8px_rgba(3,34,77,0.5)] hover:bg-[#0B1B3D] hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform">add_circle</span>
                Ajukan Permohonan Baru
            </a>
            <a href="{{ route('permohonan.lacak') }}" class="group bg-white border border-gray-200 text-gray-700 font-semibold text-sm px-8 py-4 rounded-xl hover:border-blue-200 hover:bg-blue-50/50 hover:text-blue-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform">search</span>
                Lacak Status Permohonan
            </a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="w-full py-20 px-6 max-w-container-max mx-auto flex flex-col gap-12">
    <div class="text-center flex flex-col gap-3">
        <h2 class="text-3xl md:text-4xl font-bold text-[#0B1B3D]">Kategori Informasi Publik</h2>
        <p class="text-gray-500 max-w-xl mx-auto">Pilih kategori yang sesuai dengan informasi yang Anda butuhkan.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
        @foreach($kategoriInformasi as $kategori)
        @php
            $targetUrl = $kategori->nama_kategori == 'Keberatan' 
                ? route('permohonan.lacak') 
                : route('informasi-publik.index', ['kategori' => $kategori->id]);
        @endphp
        <!-- Card -->
        <a href="{{ $targetUrl }}" class="bg-white border border-gray-100 p-8 rounded-2xl flex flex-col items-center text-center gap-5 hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.06)] hover:border-blue-200 hover:-translate-y-1.5 transition-all duration-300 group cursor-pointer relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 rounded-bl-full transition-opacity duration-500 -z-0"></div>
            
            <div class="relative z-10 w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[32px]">{{ $kategori->icon }}</span>
            </div>
            
            <div class="relative z-10">
                <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-blue-700 transition-colors">{{ $kategori->nama_kategori }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $kategori->deskripsi }}</p>
                <div class="mt-4 flex items-center justify-center gap-1 text-xs font-bold text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                    <span>Lihat Dokumen</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Quick Access: Dokumen Perencanaan Utama Bapperida -->
    <div class="mt-12 p-8 rounded-3xl bg-slate-50 border border-slate-200/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Produk Utama Bapperida</span>
                <h3 class="text-xl font-bold text-[#0B1B3D] mt-0.5">Dokumen Perencanaan Paling Sering Dicari</h3>
            </div>
            <a href="{{ route('informasi-publik.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                <span>Lihat Semua Dokumen</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('informasi-publik.index', ['jenis' => 'RPJPD']) }}" class="bg-white p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">RPJPD Ciamis</span>
                <span class="text-[10px] text-slate-400">Rencana 20 Tahun</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'RPJMD']) }}" class="bg-white p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">RPJMD Ciamis</span>
                <span class="text-[10px] text-slate-400">Rencana 5 Tahun</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'RKPD']) }}" class="bg-white p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">RKPD 2025</span>
                <span class="text-[10px] text-slate-400">Rencana Tahunan</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'RTRW']) }}" class="bg-white p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">RTRW & RDTR</span>
                <span class="text-[10px] text-slate-400">Tata Ruang Daerah</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'Kajian & Riset']) }}" class="bg-white p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">Kajian & Riset</span>
                <span class="text-[10px] text-slate-400">Makro & Sosial</span>
            </a>
            <a href="{{ route('informasi-publik.index', ['jenis' => 'Roadmap SIDa']) }}" class="bg-white p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-400 hover:shadow-sm text-center group transition-all">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700 block">Roadmap SIDa</span>
                <span class="text-[10px] text-slate-400">Sistem Inovasi</span>
            </a>
        </div>
    </div>
</section>
</main>
@endsection
