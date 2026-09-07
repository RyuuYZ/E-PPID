@extends('layouts.public')

@section('title', 'Daftar Informasi Publik (DIP) - Bapperida Kabupaten Ciamis')

@section('content')
<main class="flex-grow w-full py-10 md:py-16">
    <!-- Header / Hero Section -->
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop mb-10">
        <div class="bg-gradient-to-br from-[#0B1B3D] via-[#0f285a] to-[#03224d] rounded-3xl p-8 md:p-12 text-white shadow-xl relative overflow-hidden">
            <!-- Background Decorative Blurs -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-blue-200 text-xs font-semibold mb-4 shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-cyan-300">verified</span>
                    Katalog Resmi UU KIP No. 14/2008 &bull; Bapperida Ciamis
                </div>
                <h1 class="text-3xl md:text-5xl font-bold tracking-tight mb-4 leading-tight">
                    Daftar Informasi Publik (DIP)
                </h1>
                <p class="text-slate-300 text-base md:text-lg leading-relaxed mb-8">
                    Akses dan unduh langsung dokumen resmi perencanaan pembangunan daerah (RPJPD, RPJMD, RKPD), penataan ruang (RTRW, RDTR), hasil riset kajian, serta regulasi tanpa perlu pengajuan permohonan tertulis.
                </p>

                <!-- Quick Stats Badges -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 border-t border-white/10">
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                        <p class="text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Total Dokumen</p>
                        <p class="text-2xl font-bold text-white mt-0.5">{{ $totalDokumen }}</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                        <p class="text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Info Berkala</p>
                        <p class="text-2xl font-bold text-cyan-300 mt-0.5">
                            {{ $kategoriList->firstWhere('nama_kategori', 'Informasi Berkala')?->informasiPubliks()->count() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                        <p class="text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Setiap Saat</p>
                        <p class="text-2xl font-bold text-emerald-300 mt-0.5">
                            {{ $kategoriList->firstWhere('nama_kategori', 'Setiap Saat')?->informasiPubliks()->count() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                        <p class="text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Total Unduhan</p>
                        <p class="text-2xl font-bold text-amber-300 mt-0.5">{{ number_format($totalUnduhan) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
        <!-- Filter & Search Section -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm mb-8">
            <!-- UU KIP Tabs -->
            <div class="flex flex-wrap items-center gap-2 pb-5 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Regulasi UU KIP:</span>
                
                <a href="{{ route('informasi-publik.index', array_merge(request()->except(['kategori', 'page']))) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request('kategori') ? 'bg-[#0B1B3D] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Kategori
                </a>

                @foreach($kategoriList as $kat)
                <a href="{{ route('informasi-publik.index', array_merge(request()->except(['page']), ['kategori' => $kat->id])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('kategori') == $kat->id ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span class="material-symbols-outlined text-[16px]">{{ $kat->icon }}</span>
                    <span>{{ $kat->nama_kategori }}</span>
                    <span class="ml-1 text-[10px] px-1.5 py-0.2 rounded-full {{ request('kategori') == $kat->id ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $kat->informasiPubliks()->count() }}
                    </span>
                </a>
                @endforeach
            </div>

            <!-- Search and Dropdown Filters Form -->
            <form action="{{ route('informasi-publik.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 pt-5 items-end">
                @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif

                <!-- Keyword Search -->
                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Cari Dokumen</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="text" 
                               name="q" 
                               value="{{ request('q') }}" 
                               placeholder="Ketik judul, RPJMD, RKPD, RTRW, Kajian..." 
                               class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm font-medium outline-none transition-all">
                    </div>
                </div>

                <!-- Jenis Dokumen Perencanaan Bapperida -->
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Produk Dokumen Perencanaan</label>
                    <select name="jenis" 
                            data-placeholder="Semua Jenis Dokumen"
                            data-search-placeholder="Cari jenis dokumen..."
                            class="custom-select w-full text-sm font-medium">
                        <option value="all">Semua Jenis Dokumen</option>
                        <optgroup label="Jangka Panjang & Menengah">
                            <option value="RPJPD" {{ request('jenis') == 'RPJPD' ? 'selected' : '' }}>RPJPD (Rencana 20 Tahun)</option>
                            <option value="RPJMD" {{ request('jenis') == 'RPJMD' ? 'selected' : '' }}>RPJMD (Rencana 5 Tahun)</option>
                            <option value="Renstra" {{ request('jenis') == 'Renstra' ? 'selected' : '' }}>Renstra Bapperida</option>
                        </optgroup>
                        <optgroup label="Dokumen Kerja Tahunan">
                            <option value="RKPD" {{ request('jenis') == 'RKPD' ? 'selected' : '' }}>RKPD (Rencana Kerja Tahunan Pemda)</option>
                            <option value="Renja" {{ request('jenis') == 'Renja' ? 'selected' : '' }}>Renja Perangkat Daerah</option>
                            <option value="Musrenbang" {{ request('jenis') == 'Musrenbang' ? 'selected' : '' }}>Dokumen Musrenbang</option>
                        </optgroup>
                        <optgroup label="Tata Ruang & Wilayah">
                            <option value="RTRW" {{ request('jenis') == 'RTRW' ? 'selected' : '' }}>RTRW Kabupaten Ciamis</option>
                            <option value="RDTR" {{ request('jenis') == 'RDTR' ? 'selected' : '' }}>RDTR Kawasan Perkotaan</option>
                        </optgroup>
                        <optgroup label="Riset, Evaluasi & Inovasi (Bapperida)">
                            <option value="Kajian & Riset" {{ request('jenis') == 'Kajian & Riset' ? 'selected' : '' }}>Kajian Makroekonomi & Sosial</option>
                            <option value="Roadmap SIDa" {{ request('jenis') == 'Roadmap SIDa' ? 'selected' : '' }}>Roadmap Sistem Inovasi Daerah (SIDa)</option>
                            <option value="Evaluasi Pembangunan" {{ request('jenis') == 'Evaluasi Pembangunan' ? 'selected' : '' }}>Evaluasi Rencana Pembangunan</option>
                        </optgroup>
                        <optgroup label="Lainnya">
                            <option value="Regulasi Daerah" {{ request('jenis') == 'Regulasi Daerah' ? 'selected' : '' }}>Regulasi / Perbup</option>
                            <option value="Laporan Kinerja" {{ request('jenis') == 'Laporan Kinerja' ? 'selected' : '' }}>Laporan Kinerja (LKjIP)</option>
                            <option value="Laporan Keuangan" {{ request('jenis') == 'Laporan Keuangan' ? 'selected' : '' }}>Laporan Keuangan</option>
                            <option value="Daftar Aset" {{ request('jenis') == 'Daftar Aset' ? 'selected' : '' }}>Daftar Aset Lembaga</option>
                            <option value="Profil Lembaga" {{ request('jenis') == 'Profil Lembaga' ? 'selected' : '' }}>Profil Lembaga</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Filter Tahun -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun</label>
                    <select name="tahun" 
                            data-placeholder="Semua Tahun"
                            data-no-search="true"
                            class="custom-select w-full text-sm font-medium">
                        <option value="all">Semua Tahun</option>
                        @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit & Reset Buttons -->
                <div class="md:col-span-1 flex gap-1.5">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-2.5 rounded-xl flex items-center justify-center transition-colors shadow-sm" title="Terapkan Filter">
                        <span class="material-symbols-outlined text-[20px]">filter_alt</span>
                    </button>
                    @if(request()->hasAny(['q', 'jenis', 'tahun', 'kategori']))
                    <a href="{{ route('informasi-publik.index') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors" title="Reset Semua Filter">
                        <span class="material-symbols-outlined text-[20px]">refresh</span>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Document Results Grid -->
        @if($dokumenList->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @foreach($dokumenList as $dokumen)
            <div class="bg-white rounded-2xl border border-slate-200/80 hover:border-blue-300 hover:shadow-lg transition-all duration-300 flex flex-col justify-between p-6 group">
                <div>
                    <!-- Card Top Badges -->
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <!-- Kategori UU KIP Badge -->
                            @php
                                $badgeColor = match($dokumen->kategori?->nama_kategori) {
                                    'Informasi Berkala' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'Setiap Saat' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Serta Merta' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border {{ $badgeColor }}">
                                {{ $dokumen->kategori?->nama_kategori ?? 'Umum' }}
                            </span>

                            <!-- Jenis Dokumen Perencanaan Badge -->
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $dokumen->jenis_dokumen }}
                            </span>
                        </div>
                        <span class="text-xs font-bold text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200">
                            {{ $dokumen->tahun }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-700 transition-colors leading-snug mb-3">
                        {{ $dokumen->judul }}
                    </h3>

                    <!-- Summary -->
                    <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed mb-4">
                        {{ $dokumen->ringkasan ?? 'Dokumen informasi publik resmi Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah (Bapperida) Kabupaten Ciamis.' }}
                    </p>
                </div>

                <div>
                    <!-- Meta Info (Penanggung Jawab) -->
                    <div class="pt-4 border-t border-slate-100 flex items-center gap-2 text-slate-500 text-xs mb-4">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">domain</span>
                        <span class="truncate font-medium">{{ $dokumen->penanggung_jawab }}</span>
                    </div>

                    <!-- Card Actions -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100/60">
                        <div class="text-[11px] font-semibold text-slate-400 flex items-center gap-3">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">description</span>
                                {{ $dokumen->file_size ?? 'PDF' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">download</span>
                                {{ $dokumen->download_count }}
                            </span>
                        </div>

                        <a href="{{ route('informasi-publik.download', $dokumen->id) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white font-bold text-xs transition-all shadow-xs group-hover:scale-[1.02]">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            <span>Unduh PDF</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 mb-12">
            {{ $dokumenList->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm max-w-lg mx-auto my-8">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">find_in_page</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Dokumen Tidak Ditemukan</h3>
            <p class="text-sm text-slate-500 mb-6">
                Tidak ada dokumen yang sesuai dengan kata kunci atau kriteria filter yang Anda pilih. Coba sesuaikan kata kunci pencarian Anda.
            </p>
            <a href="{{ route('informasi-publik.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition-colors">
                <span class="material-symbols-outlined text-[16px]">refresh</span>
                Reset Semua Filter
            </a>
        </div>
        @endif

        <!-- Callout: Butuh Informasi Lainnya? -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-3xl p-8 my-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold mb-2">
                    <span class="material-symbols-outlined text-[14px]">contact_support</span>
                    Belum Menemukan Informasi yang Dibutuhkan?
                </div>
                <h3 class="text-xl font-bold text-[#0B1B3D] mb-1">
                    Ajukan Permohonan Informasi Tertulis
                </h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Jika dokumen atau data statistik perencanaan yang Anda perlukan belum dipublikasikan secara terbuka, Anda dapat mengajukan permohonan resmi kepada PPID Pelaksana Bapperida Kabupaten Ciamis.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('permohonan.create') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#03224d] text-white hover:bg-[#0B1B3D] font-bold text-sm shadow-md hover:-translate-y-0.5 transition-all">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    Form Permohonan Baru
                </a>
            </div>
        </div>
    </div>
</main>
@endsection
