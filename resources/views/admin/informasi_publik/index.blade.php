@extends('admin.layouts.app')

@section('title', 'Daftar Informasi Publik (DIP) - Admin E-PPID')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">auto_stories</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Daftar Informasi Publik (DIP)</h1>
            </div>
            <p class="text-xs text-slate-500 ml-10">Kelola repositori dokumen perencanaan &amp; informasi publik terbuka Bapperida Ciamis</p>
        </div>

        <div class="flex items-center gap-2.5 self-stretch sm:self-auto justify-end">
            <a href="{{ route('informasi-publik.index') }}" target="_blank" class="inline-flex items-center gap-1.5 bg-white text-slate-700 hover:text-blue-600 hover:bg-slate-50 px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-200/80 transition-colors shadow-2xs">
                <span class="material-symbols-outlined text-[16px]">visibility</span>
                <span>Portal Publik</span>
            </a>
            <a href="{{ route('admin.informasi-publik.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition-colors shadow-xs">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>Tambah Dokumen</span>
            </a>
        </div>
    </div>

    <!-- Bento Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-5 mb-6">
        <!-- Card 1: Total Dokumen -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Dokumen DIP</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $totalDokumen }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">auto_stories</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Repositori Bapperida:</span>
                <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">Terkatalogisasi</span>
            </div>
        </div>

        <!-- Card 2: Dokumen Aktif -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Terpublikasi Terbuka</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-emerald-600 m-0 tracking-tight">{{ $totalAktif }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">verified</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Siap diunduh masyarakat:</span>
                <span class="inline-flex items-center gap-1 font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live Portal
                </span>
            </div>
        </div>

        <!-- Card 3: Total Unduhan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Unduhan Dokumen</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-amber-600 m-0 tracking-tight">{{ number_format($totalUnduhan) }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">downloading</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Aksesibilitas Informasi:</span>
                <span class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100">Tinggi</span>
            </div>
        </div>
    </div>

    <!-- Client-Side Filter + Table (Alpine.js) -->
    <div x-data="{
        search: '',
        kategori: '',
        jenis: 'all',
        tahun: 'all',
        previewOpen: false,
        previewUrl: '',
        previewTitle: '',
        previewKategori: '',
        previewJenis: '',
        previewTahun: '',
        previewPenanggungJawab: '',
        previewFileSize: '',
        previewIsActive: true,
        previewDownloadUrl: '',
        isLoading: true,
        openPreview(url, title, kategori, jenis, tahun, penanggungJawab, fileSize, isActive, downloadUrl) {
            this.previewUrl = url;
            this.previewTitle = title;
            this.previewKategori = kategori || '';
            this.previewJenis = jenis || '';
            this.previewTahun = tahun || '';
            this.previewPenanggungJawab = penanggungJawab || 'Bapperida Ciamis';
            this.previewFileSize = fileSize || 'PDF';
            this.previewIsActive = isActive;
            this.previewDownloadUrl = downloadUrl || url.replace('?inline=1', '');
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
        },
        applyFilters() {
            const rows = document.querySelectorAll('#dokumenTable tr.doc-row');
            const q = this.search.toLowerCase().trim();
            let visibleCount = 0;
            rows.forEach(row => {
                const rowKategori = row.dataset.kategori || '';
                const rowJenis = row.dataset.jenis || '';
                const rowTahun = row.dataset.tahun || '';
                const rowText = row.dataset.searchtext || '';

                let show = true;
                if (this.kategori && rowKategori !== this.kategori) show = false;
                if (this.jenis !== 'all' && rowJenis !== this.jenis) show = false;
                if (this.tahun !== 'all' && rowTahun !== this.tahun) show = false;
                if (q && !rowText.includes(q)) show = false;

                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            const counter = document.getElementById('filterCounter');
            const emptyRow = document.getElementById('emptyRow');
            if (counter) counter.textContent = 'Menampilkan ' + visibleCount + ' dari {{ $dokumen->count() }} dokumen';
            if (emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        },
        resetFilters() {
            this.search = '';
            this.kategori = '';
            this.jenis = 'all';
            this.tahun = 'all';
            this.$nextTick(() => this.applyFilters());
            document.querySelectorAll('#filterToolbar select.custom-select').forEach(el => {
                if (el.tomselect) {
                    el.tomselect.setValue(el.options[0].value, true);
                }
            });
        },
        get hasActiveFilter() {
            return this.search !== '' || this.kategori !== '' || this.jenis !== 'all' || this.tahun !== 'all';
        }
    }" x-init="$nextTick(() => applyFilters())">

    <!-- Filter Toolbar Card -->
    <div id="filterToolbar" class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs mb-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <!-- Search Input -->
            <div class="md:col-span-4">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">search</span>
                    <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Cari judul, kata kunci, atau ringkasan..." 
                           class="w-full h-10 pl-9 pr-3 text-xs bg-slate-50/60 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 font-medium text-slate-800 transition-all">
                </div>
            </div>

            <!-- Kategori UU KIP -->
            <div class="md:col-span-3">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kategori UU KIP</label>
                <select x-model="kategori" @change="applyFilters()"
                        data-placeholder="Semua Kategori"
                        data-search-placeholder="Cari kategori..."
                        class="custom-select ts-compact w-full text-xs font-medium">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jenis Dokumen Perencanaan Bapperida -->
            <div class="md:col-span-3">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Dokumen Bapperida</label>
                <select x-model="jenis" @change="applyFilters()"
                        data-placeholder="Semua Jenis Dokumen"
                        data-search-placeholder="Cari jenis dokumen..."
                        class="custom-select ts-compact w-full text-xs font-medium">
                    <option value="all">Semua Jenis Dokumen</option>
                    @foreach($jenisOptions as $key => $label)
                    <option value="{{ $key }}">{{ $key }} - {{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="md:col-span-1">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tahun</label>
                <select x-model="tahun" @change="applyFilters()"
                        data-placeholder="Semua"
                        data-no-search="true"
                        class="custom-select ts-compact w-full text-xs font-medium">
                    <option value="all">Semua</option>
                    @foreach($availableYears as $yr)
                    <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Reset -->
            <div class="md:col-span-1 flex items-end">
                <button type="button" x-show="hasActiveFilter" x-cloak @click="resetFilters()"
                        class="w-full h-10 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl flex items-center justify-center gap-1.5 transition-colors text-xs font-semibold cursor-pointer" title="Reset Filter">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    <span>Reset</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400 text-[18px]">table_rows</span>
                <h2 class="text-sm font-bold text-slate-800 m-0">Katalog Dokumen Terdaftar</h2>
            </div>
            <span id="filterCounter" class="text-xs text-slate-500 font-medium">Menampilkan {{ $dokumen->count() }} dari {{ $dokumen->count() }} dokumen</span>
        </div>

        <div class="w-full overflow-hidden">
            <table class="w-full table-fixed text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/60 text-[11px] text-slate-500 uppercase tracking-wider">
                        <th class="w-[32%] px-4 py-3.5 font-bold">Judul Dokumen &amp; Klasifikasi</th>
                        <th class="w-[16%] px-3 py-3.5 font-bold whitespace-nowrap">Kategori UU KIP</th>
                        <th class="w-[7%] px-2 py-3.5 font-bold text-center whitespace-nowrap">Tahun</th>
                        <th class="w-[19%] px-3 py-3.5 font-bold whitespace-nowrap">Penanggung Jawab</th>
                        <th class="w-[8%] px-2 py-3.5 font-bold text-center whitespace-nowrap">Unduhan</th>
                        <th class="w-[8%] px-2 py-3.5 font-bold text-center whitespace-nowrap">Status</th>
                        <th class="w-[10%] px-3 py-3.5 font-bold text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dokumenTable" class="divide-y divide-slate-100 text-slate-600 text-xs">
                    @foreach($dokumen as $item)
                    <tr class="doc-row hover:bg-slate-50/80 transition-colors"
                        data-kategori="{{ $item->kategori_informasi_publik_id }}"
                        data-jenis="{{ $item->jenis_dokumen }}"
                        data-tahun="{{ $item->tahun }}"
                        data-searchtext="{{ strtolower($item->judul . ' ' . $item->jenis_dokumen . ' ' . ($item->ringkasan ?? '') . ' ' . ($item->penanggung_jawab ?? '') . ' ' . ($item->kategori?->nama_kategori ?? '')) }}">
                        <!-- Judul & Klasifikasi -->
                        <td class="px-4 py-3">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-100 shadow-2xs mt-0.5" title="Berkas PDF">
                                    <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('admin.informasi-publik.edit', $item->id) }}" 
                                       class="font-bold text-slate-900 hover:text-blue-600 transition-colors leading-snug line-clamp-1"
                                       title="Edit Dokumen">
                                        {{ $item->judul }}
                                    </a>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $item->jenis_dokumen }}
                                        </span>
                                        @if($item->file_size)
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            {{ $item->file_size }}
                                        </span>
                                        @endif
                                    </div>
                                    @if($item->ringkasan)
                                    <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5 font-normal">{{ $item->ringkasan }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Kategori UU KIP -->
                        <td class="px-3 py-3 whitespace-nowrap">
                            @php
                                $badgeClass = match($item->kategori?->nama_kategori) {
                                    'Informasi Berkala' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'Setiap Saat' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Serta Merta' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $badgeClass }} shadow-2xs">
                                {{ $item->kategori?->nama_kategori ?? '-' }}
                            </span>
                        </td>

                        <!-- Tahun -->
                        <td class="px-2 py-3 whitespace-nowrap text-center">
                            <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-xs border border-slate-200/80">
                                {{ $item->tahun }}
                            </span>
                        </td>

                        <!-- Penanggung Jawab -->
                        <td class="px-3 py-3">
                            <span class="font-bold text-slate-800 text-xs block truncate" title="{{ $item->penanggung_jawab }}">
                                {{ $item->penanggung_jawab }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium block truncate">
                                {{ $item->unitPengolah?->nama_bidang ?? 'Bapperida Ciamis' }}
                            </span>
                        </td>

                        <!-- Unduhan -->
                        <td class="px-2 py-3 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-0.5 font-bold text-slate-700 text-xs bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/80">
                                <span class="material-symbols-outlined text-[13px] text-amber-500">download</span>
                                {{ number_format($item->download_count) }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="px-2 py-3 text-center whitespace-nowrap">
                            @if($item->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px] border border-slate-200 shadow-2xs">
                                Draft
                            </span>
                            @endif
                        </td>

                        <!-- Aksi (Direct Preview + 3-dot dropdown) -->
                        <td class="px-3 py-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Direct Preview Button -->
                                <button type="button" 
                                        @click="openPreview('{{ route('informasi-publik.download', $item->id) }}?inline=1', '{{ addslashes($item->judul) }}', '{{ $item->kategori?->nama_kategori }}', '{{ $item->jenis_dokumen }}', '{{ $item->tahun }}', '{{ addslashes($item->penanggung_jawab) }}', '{{ $item->file_size ?? 'PDF' }}', {{ $item->is_active ? 'true' : 'false' }}, '{{ route('informasi-publik.download', $item->id) }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-100 transition-all shadow-2xs cursor-pointer"
                                        title="Preview Dokumen PDF">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </button>

                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" @click.away="open = false" type="button" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-slate-500 hover:text-slate-800 hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs cursor-pointer"
                                            title="Menu Aksi Lainnya">
                                        <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                    </button>
                                    <div x-show="open" x-cloak
                                         x-transition:enter="transition ease-out duration-100" 
                                         x-transition:enter-start="transform opacity-0 scale-95" 
                                         x-transition:enter-end="transform opacity-100 scale-100" 
                                         x-transition:leave="transition ease-in duration-75" 
                                         x-transition:leave-start="transform opacity-100 scale-100" 
                                         x-transition:leave-end="transform opacity-0 scale-95" 
                                         class="origin-top-right absolute right-0 mt-1.5 w-44 rounded-xl shadow-lg bg-white ring-1 ring-slate-200 z-50 py-1.5 border border-slate-100">
                                        <a href="{{ route('informasi-publik.download', $item->id) }}" target="_blank" 
                                           class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors font-medium">
                                            <span class="material-symbols-outlined text-[16px]">download</span>
                                            Unduh Berkas
                                        </a>
                                        <a href="{{ route('admin.informasi-publik.edit', $item->id) }}" 
                                           class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-amber-600 transition-colors font-medium">
                                            <span class="material-symbols-outlined text-[16px]">edit</span>
                                            Edit Dokumen
                                        </a>
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <form action="{{ route('admin.informasi-publik.destroy', $item->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors font-medium cursor-pointer">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                                Hapus Dokumen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <!-- Empty state row (shown by JS when no results match) -->
                    <tr id="emptyRow" style="display: none;">
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl text-slate-300">find_in_page</span>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada dokumen yang cocok dengan filter.</p>
                                <p class="text-xs text-slate-400">Coba sesuaikan kata kunci pencarian atau ubah filter Anda.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer info -->
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/40 text-xs text-slate-500 font-medium">
            Total {{ $dokumen->count() }} dokumen terdaftar
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="previewOpen" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" @keydown.escape.window="closePreview()">
        <!-- Background overlay -->
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
                <!-- Modal panel -->
                <div x-show="previewOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl md:rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-5xl flex flex-col h-[88vh] border border-slate-200">
                    
                    <!-- Header -->
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
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" 
                                          :class="previewIsActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                                          x-text="previewIsActive ? 'Live Publik' : 'Draft'"></span>
                                </div>
                                <h3 class="text-sm md:text-base font-bold leading-6 text-slate-900 truncate" id="modal-title" x-text="previewTitle"></h3>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 shrink-0">
                            <a :href="previewUrl" target="_blank" 
                               class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 text-xs font-semibold transition-colors" title="Buka di tab baru">
                                <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                <span>Tab Baru</span>
                            </a>
                            <button type="button" @click="closePreview()" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer" title="Tutup (ESC)">
                                <span class="material-symbols-outlined text-[22px]">close</span>
                            </button>
                        </div>
                    </div>

                    <!-- Body (Iframe) -->
                    <div class="flex-1 bg-slate-100 p-2 md:p-3 overflow-hidden relative">
                        <!-- Loading Spinner -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50/80 z-20 transition-opacity" x-show="isLoading">
                            <div class="w-10 h-10 border-4 border-slate-200 border-t-blue-600 rounded-full animate-spin mb-3"></div>
                            <p class="text-xs font-semibold text-slate-600">Memuat berkas dokumen PDF...</p>
                        </div>
                        
                        <iframe :src="previewUrl" 
                                @load="isLoading = false"
                                class="w-full h-full rounded-xl border border-slate-200 bg-white shadow-xs relative z-10" 
                                title="Preview Dokumen PDF"></iframe>
                    </div>

                    <!-- Footer -->
                    <div class="bg-slate-50 px-5 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100 shrink-0">
                        <div class="flex items-center gap-2 text-xs text-slate-500 w-full sm:w-auto truncate">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">domain</span>
                            <span class="truncate font-medium" x-text="previewPenanggungJawab"></span>
                        </div>

                        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                            <button type="button" @click="closePreview()" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200 transition-colors cursor-pointer">
                                Tutup
                            </button>
                            <a :href="previewDownloadUrl" target="_blank" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-sm hover:scale-[1.02] cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">download</span>
                                <span>Unduh Dokumen</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>{{-- end x-data --}}

</main>
@endsection
