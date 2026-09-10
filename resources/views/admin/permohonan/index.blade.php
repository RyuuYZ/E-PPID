@extends('admin.layouts.app')

@section('title', 'Daftar Permohonan Informasi - Admin E-PPID')

@section('content')
@php
    $itemsPayload = $permohonan->map(function($p) {
        return [
            'id' => $p->id,
            'nomor_registrasi' => $p->nomor_registrasi,
            'nama_pemohon' => $p->nama_pemohon,
            'nik_atau_no_badan_hukum' => $p->nik_atau_no_badan_hukum ?? '-',
            'email' => $p->email ?? '-',
            'no_telp' => $p->no_telp ?? '-',
            'pekerjaan' => $p->pekerjaan ?? '-',
            'alamat' => $p->alamat ?? '-',
            'kategori_pemohon_id' => $p->kategori_pemohon_id,
            'kategori_pemohon' => $p->kategori_pemohon?->nama_kategori ?? '-',
            'cara_memperoleh_id' => $p->cara_memperoleh_informasi_id,
            'cara_memperoleh' => $p->cara_memperoleh_informasi?->nama_cara ?? '-',
            'cara_salinan' => $p->cara_mendapatkan_salinan ?? '-',
            'subjek_informasi' => $p->subjek_informasi ?? '-',
            'rincian_informasi' => $p->rincian_informasi ?? '-',
            'tujuan_penggunaan' => $p->tujuan_penggunaan ?? '-',
            'status_value' => $p->status->value,
            'status_label' => $p->status->label(),
            'status_badge_class' => $p->status->badgeClass(),
            'created_timestamp' => $p->created_at->timestamp,
            'tanggal_masuk_date' => $p->created_at->format('d M Y'),
            'tanggal_masuk_time' => $p->created_at->format('H:i') . ' WIB',
            'tanggal_masuk' => $p->created_at->format('d M Y, H:i') . ' WIB',
            'file_identitas' => $p->file_identitas ? route('admin.permohonan.file-identitas', $p->id) : null,
            'url_show' => route('admin.permohonan.show', $p->id),
            'url_tanda_terima' => route('permohonan.tanda_terima', $p->nomor_registrasi),
        ];
    });

    $kategoriOptions = $kategoriPemohons->map(function($kp) {
        return [
            'value' => (string) $kp->id,
            'label' => $kp->nama_kategori,
            'icon' => 'account_circle',
        ];
    })->prepend([
        'value' => 'all',
        'label' => 'Semua Kategori Pemohon',
        'icon' => 'groups',
    ]);

    $caraOptions = $caraMemperoleh->map(function($cm) {
        return [
            'value' => (string) $cm->id,
            'label' => $cm->nama_cara,
            'icon' => 'contact_support',
        ];
    })->prepend([
        'value' => 'all',
        'label' => 'Semua Metode',
        'icon' => 'hub',
    ]);

    $totalSemua = $permohonan->count();
    $totalMasuk = $permohonan->where('status.value', 'diajukan')->count();
    $totalProses = $permohonan->filter(fn($p) => in_array($p->status->value, [
        'diajukan', 'menunggu_kelengkapan', 'diverifikasi', 'ditugaskan', 'menunggu_data', 'data_diuji', 'menunggu_tanda_tangan', 'ditandatangani'
    ]))->count();
    $totalSelesai = $permohonan->where('status.value', 'selesai')->count();
@endphp

<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen"
      x-data="{
          items: {{ Js::from($itemsPayload) }},
          kategoriOptions: {{ Js::from($kategoriOptions) }},
          caraOptions: {{ Js::from($caraOptions) }},
          statusOptions: [
              { value: 'all', label: 'Semua Status Permohonan', icon: 'all_inclusive', iconBg: 'bg-slate-100 text-slate-700' },
              { value: 'diajukan', label: 'Permohonan Masuk (Diajukan)', icon: 'inbox', iconBg: 'bg-blue-50 text-blue-700' },
              { value: 'menunggu_kelengkapan', label: 'Menunggu Kelengkapan Berkas', icon: 'hourglass_top', iconBg: 'bg-amber-50 text-amber-700' },
              { value: 'diverifikasi', label: 'Diverifikasi (Siap Disposisi)', icon: 'verified', iconBg: 'bg-indigo-50 text-indigo-700' },
              { value: 'ditugaskan', label: 'Koordinasi / Ditugaskan ke Unit', icon: 'forward_to_inbox', iconBg: 'bg-sky-50 text-sky-700' },
              { value: 'menunggu_data', label: 'Menunggu Pengunggahan Data', icon: 'cloud_upload', iconBg: 'bg-cyan-50 text-cyan-700' },
              { value: 'data_diuji', label: 'Uji & Validasi Data', icon: 'rule', iconBg: 'bg-violet-50 text-violet-700' },
              { value: 'menunggu_tanda_tangan', label: 'Menunggu Pengesahan TTE', icon: 'draw', iconBg: 'bg-purple-50 text-purple-700' },
              { value: 'ditandatangani', label: 'Telah Ditandatangani (Siap Kirim)', icon: 'verified_user', iconBg: 'bg-teal-50 text-teal-700' },
              { value: 'selesai', label: 'Selesai (Tuntas)', icon: 'task_alt', iconBg: 'bg-emerald-50 text-emerald-700' },
              { value: 'ditolak', label: 'Ditolak', icon: 'cancel', iconBg: 'bg-rose-50 text-rose-700' },
              { value: 'ditutup_tidak_lengkap', label: 'Ditutup Tidak Lengkap', icon: 'block', iconBg: 'bg-slate-100 text-slate-600' }
          ],
          sortOptions: [
              { value: 'latest', label: 'Terbaru Masuk', icon: 'arrow_downward' },
              { value: 'oldest', label: 'Terlama Masuk', icon: 'arrow_upward' },
              { value: 'name_asc', label: 'Nama A-Z', icon: 'sort_by_alpha' }
          ],

          search: '',
          statusFilter: '{{ $currentStatus }}',
          kategoriFilter: 'all',
          caraFilter: 'all',
          sortBy: 'latest',
          openDropdown: null,
          perPage: 10,
          currentPage: 1,
          targetPageInput: 1,
          selectedItem: null,
          modalOpen: false,
          copied: false,

          init() {
              if (window.location.search) {
                  window.history.replaceState({}, document.title, window.location.pathname);
              }
              this.targetPageInput = this.currentPage;
          },

          get selectedStatusObj() {
              return this.statusOptions.find(opt => opt.value === this.statusFilter) || this.statusOptions[0];
          },

          get selectedKategoriObj() {
              return this.kategoriOptions.find(opt => String(opt.value) === String(this.kategoriFilter)) || this.kategoriOptions[0];
          },

          get selectedCaraObj() {
              return this.caraOptions.find(opt => String(opt.value) === String(this.caraFilter)) || this.caraOptions[0];
          },

          get selectedSortObj() {
              return this.sortOptions.find(opt => opt.value === this.sortBy) || this.sortOptions[0];
          },

          get hasActiveFilters() {
              return this.search.trim() !== '' || 
                     this.statusFilter !== 'all' || 
                     this.kategoriFilter !== 'all' || 
                     this.caraFilter !== 'all' || 
                     this.sortBy !== 'latest';
          },

          resetFilters() {
              this.search = '';
              this.statusFilter = 'all';
              this.kategoriFilter = 'all';
              this.caraFilter = 'all';
              this.sortBy = 'latest';
              this.openDropdown = null;
              this.currentPage = 1;
              this.targetPageInput = 1;
          },

          get filteredItems() {
              const q = this.search.toLowerCase().trim();
              let result = this.items.filter(item => {
                  const matchStatus = (this.statusFilter === 'all' || !this.statusFilter) 
                      ? true 
                      : (item.status_value === this.statusFilter);

                  const matchKategori = (this.kategoriFilter === 'all') 
                      ? true 
                      : (String(item.kategori_pemohon_id) === String(this.kategoriFilter));

                  const matchCara = (this.caraFilter === 'all') 
                      ? true 
                      : (String(item.cara_memperoleh_id) === String(this.caraFilter));

                  const matchSearch = !q ? true : (
                      item.nomor_registrasi.toLowerCase().includes(q) ||
                      item.nama_pemohon.toLowerCase().includes(q) ||
                      (item.email && item.email.toLowerCase().includes(q)) ||
                      (item.nik_atau_no_badan_hukum && item.nik_atau_no_badan_hukum.toLowerCase().includes(q)) ||
                      (item.rincian_informasi && item.rincian_informasi.toLowerCase().includes(q)) ||
                      (item.kategori_pemohon && item.kategori_pemohon.toLowerCase().includes(q))
                  );

                  return matchStatus && matchKategori && matchCara && matchSearch;
              });

              if (this.sortBy === 'latest') {
                  result.sort((a, b) => b.created_timestamp - a.created_timestamp);
              } else if (this.sortBy === 'oldest') {
                  result.sort((a, b) => a.created_timestamp - b.created_timestamp);
              } else if (this.sortBy === 'name_asc') {
                  result.sort((a, b) => a.nama_pemohon.localeCompare(b.nama_pemohon));
              }

              return result;
          },

          get totalPages() {
              return Math.max(1, Math.ceil(this.filteredItems.length / this.perPage));
          },

          get paginatedItems() {
              const start = (this.currentPage - 1) * this.perPage;
              return this.filteredItems.slice(start, start + this.perPage);
          },

          onFilterChange() {
              this.currentPage = 1;
              this.targetPageInput = 1;
          },

          setPage(p) {
              let page = parseInt(p);
              if (isNaN(page)) page = 1;
              if (page < 1) page = 1;
              if (page > this.totalPages) page = this.totalPages;
              this.currentPage = page;
              this.targetPageInput = page;
          },

          prevPage() {
              if (this.currentPage > 1) {
                  this.setPage(this.currentPage - 1);
              }
          },

          nextPage() {
              if (this.currentPage < this.totalPages) {
                  this.setPage(this.currentPage + 1);
              }
          },

          openDetail(item) {
              this.selectedItem = item;
              this.modalOpen = true;
          },

          closeDetail() {
              this.modalOpen = false;
              setTimeout(() => { this.selectedItem = null; }, 200);
          },

          copyNomorRegistrasi(no) {
              navigator.clipboard.writeText(no);
              this.copied = true;
              setTimeout(() => { this.copied = false; }, 2000);
          }
      }"
      @keydown.escape.window="closeDetail(); openDropdown = null">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">assignment</span>
                </div>
                <h1 class="text-xl md:text-2xl font-bold text-slate-900 m-0 tracking-tight">Permohonan Informasi</h1>
            </div>
            <p class="text-xs text-slate-500 ml-10">Kelola, verifikasi berkas, disposisi unit, pengesahan TTE, dan selesaikan permohonan informasi publik.</p>
        </div>
        
        <div class="flex items-center gap-2.5 self-stretch md:self-auto justify-end">
            <a href="{{ route('admin.permohonan.create') }}" class="inline-flex items-center gap-1.5 bg-[#03224d] text-white px-3.5 py-2 rounded-xl shadow-xs text-xs font-bold hover:bg-[#0B1B3D] transition-all">
                <span class="material-symbols-outlined text-[16px]">add</span> 
                <span>Tambah Permohonan</span>
            </a>
        </div>
    </div>

    <!-- Bento Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-5 mb-6">
        <!-- Card 1: Total Permohonan -->
        <div @click="statusFilter = 'all'; onFilterChange()" 
             class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-blue-300 transition-all cursor-pointer group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Permohonan</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $totalSemua }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">folder_copy</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Registrasi Masuk:</span>
                <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">Semua Data</span>
            </div>
        </div>

        <!-- Card 2: Dalam Proses -->
        <div @click="statusFilter = 'diajukan'; onFilterChange()" 
             class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-amber-300 transition-all cursor-pointer group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Sedang Diproses</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-amber-600 m-0 tracking-tight">{{ $totalProses }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">pending_actions</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Permohonan Baru:</span>
                <span class="inline-flex items-center gap-1 font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> {{ $totalMasuk }} Baru Masuk
                </span>
            </div>
        </div>

        <!-- Card 3: Selesai -->
        <div @click="statusFilter = 'selesai'; onFilterChange()" 
             class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-all cursor-pointer group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Permohonan Selesai</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-emerald-600 m-0 tracking-tight">{{ $totalSelesai }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">task_alt</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Jawaban Terkirim:</span>
                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">Tuntas</span>
            </div>
        </div>
    </div>

    <!-- FILTER TOOLBAR CONTAINER (CUSTOM MODERN DROPDOWNS) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 mb-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400 text-[18px]">tune</span>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Filter &amp; Pencarian Permohonan</h3>
            </div>
            <button type="button" 
                    x-show="hasActiveFilters" 
                    x-cloak
                    @click="resetFilters()"
                    class="text-[11px] font-semibold text-rose-600 hover:text-rose-800 flex items-center gap-1 hover:underline cursor-pointer">
                <span class="material-symbols-outlined text-[14px]">refresh</span>
                <span>Reset Semua Filter</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <!-- 1. Search Box -->
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian Kata Kunci:</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" 
                           x-model="search" 
                           @input="onFilterChange()"
                           placeholder="No. reg, nama, NIK, rincian..." 
                           class="w-full h-10 pl-9 pr-3 bg-white border border-slate-200/90 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all placeholder-slate-400 shadow-2xs">
                    <button type="button" x-show="search" x-cloak @click="search = ''; onFilterChange()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
            </div>

            <!-- 2. Status Workflow Filter (Custom Modern Select) -->
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status Alur Layanan:</label>
                <!-- Trigger Button -->
                <button type="button" 
                        @click="openDropdown = (openDropdown === 'status' ? null : 'status')" 
                        class="w-full h-10 px-3 bg-white border border-slate-200/90 hover:border-slate-300 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs flex items-center justify-between transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    <div class="flex items-center gap-2 truncate min-w-0">
                        <span class="w-5 h-5 rounded-md flex items-center justify-center shrink-0 text-[12px]" :class="selectedStatusObj.iconBg">
                            <span class="material-symbols-outlined text-[14px]" x-text="selectedStatusObj.icon"></span>
                        </span>
                        <span class="truncate text-xs font-semibold text-slate-800" x-text="selectedStatusObj.label"></span>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-slate-400 transition-transform duration-200 ml-1 shrink-0" :class="openDropdown === 'status' ? 'rotate-180 text-blue-600' : ''">expand_more</span>
                </button>

                <!-- Custom Dropdown Menu -->
                <div x-show="openDropdown === 'status'" 
                     x-cloak
                     @click.outside="if (openDropdown === 'status') openDropdown = null"
                     x-transition:enter="transition ease-out duration-100" 
                     x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1" 
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
                     x-transition:leave="transition ease-in duration-75" 
                     x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
                     x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1" 
                     class="absolute left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-1.5 max-h-72 overflow-y-auto space-y-0.5">
                    <template x-for="opt in statusOptions" :key="opt.value">
                        <div @click="statusFilter = opt.value; onFilterChange(); openDropdown = null"
                             class="flex items-center justify-between px-2.5 py-2 rounded-xl text-xs cursor-pointer transition-colors"
                             :class="statusFilter === opt.value ? 'bg-blue-50 text-blue-900 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-medium'">
                            <div class="flex items-center gap-2 truncate min-w-0">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 border border-current/10" :class="opt.iconBg">
                                    <span class="material-symbols-outlined text-[15px]" x-text="opt.icon"></span>
                                </span>
                                <span class="truncate" x-text="opt.label"></span>
                            </div>
                            <span class="material-symbols-outlined text-[16px] text-blue-600 shrink-0 ml-1" x-show="statusFilter === opt.value">check</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 3. Kategori Pemohon Filter (Custom Modern Select) -->
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kategori Pemohon:</label>
                <!-- Trigger Button -->
                <button type="button" 
                        @click="openDropdown = (openDropdown === 'kategori' ? null : 'kategori')" 
                        class="w-full h-10 px-3 bg-white border border-slate-200/90 hover:border-slate-300 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs flex items-center justify-between transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    <div class="flex items-center gap-2 truncate min-w-0">
                        <span class="w-5 h-5 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[14px]" x-text="selectedKategoriObj.icon"></span>
                        </span>
                        <span class="truncate text-xs font-semibold text-slate-800" x-text="selectedKategoriObj.label"></span>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-slate-400 transition-transform duration-200 ml-1 shrink-0" :class="openDropdown === 'kategori' ? 'rotate-180 text-blue-600' : ''">expand_more</span>
                </button>

                <!-- Custom Dropdown Menu -->
                <div x-show="openDropdown === 'kategori'" 
                     x-cloak
                     @click.outside="if (openDropdown === 'kategori') openDropdown = null"
                     x-transition:enter="transition ease-out duration-100" 
                     x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1" 
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
                     x-transition:leave="transition ease-in duration-75" 
                     x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
                     x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1" 
                     class="absolute left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-1.5 max-h-64 overflow-y-auto space-y-0.5">
                    <template x-for="opt in kategoriOptions" :key="opt.value">
                        <div @click="kategoriFilter = opt.value; onFilterChange(); openDropdown = null"
                             class="flex items-center justify-between px-2.5 py-2 rounded-xl text-xs cursor-pointer transition-colors"
                             :class="String(kategoriFilter) === String(opt.value) ? 'bg-blue-50 text-blue-900 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-medium'">
                            <div class="flex items-center gap-2 truncate min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[15px]" x-text="opt.icon"></span>
                                </span>
                                <span class="truncate" x-text="opt.label"></span>
                            </div>
                            <span class="material-symbols-outlined text-[16px] text-blue-600 shrink-0 ml-1" x-show="String(kategoriFilter) === String(opt.value)">check</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 4. Cara Memperoleh & Sort (Custom Modern Select) -->
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Urutan &amp; Metode:</label>
                <div class="grid grid-cols-2 gap-2">
                    <!-- Metode Dropdown -->
                    <div class="relative">
                        <button type="button" 
                                @click="openDropdown = (openDropdown === 'cara' ? null : 'cara')" 
                                class="w-full h-10 px-2.5 bg-white border border-slate-200/90 hover:border-slate-300 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs flex items-center justify-between transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                            <span class="truncate text-xs font-semibold text-slate-800" x-text="selectedCaraObj.label"></span>
                            <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-200 shrink-0" :class="openDropdown === 'cara' ? 'rotate-180 text-blue-600' : ''">expand_more</span>
                        </button>

                        <div x-show="openDropdown === 'cara'" 
                             x-cloak
                             @click.outside="if (openDropdown === 'cara') openDropdown = null"
                             x-transition:enter="transition ease-out duration-100" 
                             x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1" 
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
                             x-transition:leave="transition ease-in duration-75" 
                             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
                             x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1" 
                             class="absolute left-0 right-0 sm:right-auto sm:w-60 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-1.5 max-h-60 overflow-y-auto space-y-0.5">
                            <template x-for="opt in caraOptions" :key="opt.value">
                                <div @click="caraFilter = opt.value; onFilterChange(); openDropdown = null"
                                     class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs cursor-pointer transition-colors"
                                     :class="String(caraFilter) === String(opt.value) ? 'bg-blue-50 text-blue-900 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-medium'">
                                    <span class="truncate" x-text="opt.label"></span>
                                    <span class="material-symbols-outlined text-[14px] text-blue-600 shrink-0 ml-1" x-show="String(caraFilter) === String(opt.value)">check</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="relative">
                        <button type="button" 
                                @click="openDropdown = (openDropdown === 'sort' ? null : 'sort')" 
                                class="w-full h-10 px-2.5 bg-white border border-slate-200/90 hover:border-slate-300 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs flex items-center justify-between transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                            <span class="truncate text-xs font-semibold text-slate-800" x-text="selectedSortObj.label"></span>
                            <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-200 shrink-0" :class="openDropdown === 'sort' ? 'rotate-180 text-blue-600' : ''">expand_more</span>
                        </button>

                        <div x-show="openDropdown === 'sort'" 
                             x-cloak
                             @click.outside="if (openDropdown === 'sort') openDropdown = null"
                             x-transition:enter="transition ease-out duration-100" 
                             x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1" 
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
                             x-transition:leave="transition ease-in duration-75" 
                             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
                             x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1" 
                             class="absolute right-0 w-44 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-1.5 space-y-0.5">
                            <template x-for="opt in sortOptions" :key="opt.value">
                                <div @click="sortBy = opt.value; onFilterChange(); openDropdown = null"
                                     class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs cursor-pointer transition-colors"
                                     :class="sortBy === opt.value ? 'bg-blue-50 text-blue-900 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-medium'">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <span class="material-symbols-outlined text-[14px] text-slate-400" x-text="opt.icon"></span>
                                        <span class="truncate" x-text="opt.label"></span>
                                    </div>
                                    <span class="material-symbols-outlined text-[14px] text-blue-600 shrink-0" x-show="sortBy === opt.value">check</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400 text-[18px]">table_rows</span>
                <h2 class="text-sm font-bold text-slate-800 m-0">Katalog Permohonan Terdaftar</h2>
            </div>
            <span class="text-xs text-slate-500 font-medium">
                Menampilkan <span class="font-bold text-slate-800" x-text="filteredItems.length"></span> dari {{ $totalSemua }} permohonan
            </span>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full table-fixed text-left border-collapse text-sm text-slate-600">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200">
                        <th class="w-[20%] px-4 py-3.5">No. Registrasi</th>
                        <th class="w-[24%] px-4 py-3.5">Pemohon</th>
                        <th class="w-[18%] px-4 py-3.5">Kategori &amp; Tanggal</th>
                        <th class="w-[26%] px-4 py-3.5">Status Alur Layanan</th>
                        <th class="w-[12%] px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <template x-for="item in paginatedItems" :key="item.id">
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <!-- Nomor Registrasi -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="bg-slate-100 text-slate-700 font-mono px-2.5 py-1 rounded-lg text-xs font-semibold border border-slate-200/80 shadow-2xs"
                                      x-text="item.nomor_registrasi">
                                </span>
                            </td>

                            <!-- Pemohon & Kontak -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800 truncate" :title="item.nama_pemohon" x-text="item.nama_pemohon"></div>
                                <div class="text-xs text-slate-400 truncate" x-text="item.email"></div>
                            </td>

                            <!-- Kategori & Tanggal Masuk -->
                            <td class="px-4 py-3.5 text-slate-500 whitespace-nowrap text-xs">
                                <div class="font-semibold text-slate-700 truncate" x-text="item.kategori_pemohon"></div>
                                <div class="text-[11px] text-slate-400 mt-0.5" x-text="item.tanggal_masuk_date"></div>
                            </td>

                            <!-- Status Terkini -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border border-current/20 max-w-full" 
                                      :class="item.status_badge_class"
                                      :title="item.status_label">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75 shrink-0"></span>
                                    <span class="truncate" x-text="item.status_label"></span>
                                </span>
                            </td>

                            <!-- Aksi (3-dot Dropdown / Modal) -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="openDetail(item)"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition-all shadow-2xs cursor-pointer"
                                        title="Lihat Rincian & Aksi (Popup)">
                                    <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                </button>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredItems.length === 0">
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">folder_off</span>
                                    <p class="font-medium text-slate-600">Tidak ada permohonan yang sesuai filter</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Silakan sesuaikan kata kunci pencarian atau reset filter di atas.</p>
                                    <button type="button" @click="resetFilters()" class="mt-3 text-xs bg-blue-50 text-blue-600 font-bold px-3 py-1.5 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors">
                                        Reset Filter
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Bottom Bar (Client-Side & URL-Preserving) -->
        <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50 rounded-b-2xl">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
                <!-- Total Pill Badge (Left) -->
                <div class="flex items-center">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-slate-100/90 border border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider shadow-2xs">
                        <span>TOTAL:</span>
                        <span class="font-extrabold text-slate-800 text-xs" x-text="filteredItems.length"></span>
                        <span>PERMOHONAN</span>
                    </span>
                </div>

                <!-- Navigation Controls (Right) -->
                <div class="flex items-center gap-2.5 flex-wrap justify-center sm:justify-end">
                    <!-- Prev Button -->
                    <button type="button"
                            @click="prevPage()"
                            :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'bg-slate-50 border-slate-200/60 text-slate-400 cursor-not-allowed opacity-60' : 'bg-white border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-2xs cursor-pointer'"
                            class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full border text-xs font-semibold transition-colors select-none">
                        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        <span>Prev</span>
                    </button>

                    <!-- Jump to Page Box (KE HAL: [ 1 ] / X Go ->) -->
                    <div class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3.5 py-1 shadow-2xs">
                        <form @submit.prevent="setPage(targetPageInput)" class="flex items-center gap-2 m-0">
                            <span class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">KE HAL:</span>
                            <input type="number" 
                                   min="1" 
                                   :max="totalPages" 
                                   x-model="targetPageInput" 
                                   class="w-12 text-center py-0.5 px-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-2xs">
                            <span class="text-slate-400 font-medium text-xs">/ <span x-text="totalPages"></span></span>

                            <button type="submit" 
                                    class="inline-flex items-center gap-1 bg-[#00875a] hover:bg-[#00714c] text-white px-2.5 py-1 rounded-full text-xs font-bold transition-all shadow-2xs cursor-pointer ml-1">
                                <span>Go</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </button>
                        </form>
                    </div>

                    <!-- Next Button -->
                    <button type="button"
                            @click="nextPage()"
                            :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'bg-slate-50 border-slate-200/60 text-slate-400 cursor-not-allowed opacity-60' : 'bg-white border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-2xs cursor-pointer'"
                            class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full border text-xs font-semibold transition-colors select-none">
                        <span>Next</span>
                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- POPUP MODAL (Rincian Permohonan & Aksi)     -->
    <!-- ========================================== -->
    <div x-show="modalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop Blur Overlay -->
        <div x-show="modalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
             @click="closeDetail()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <!-- Modal Content Card -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl border border-slate-200 transition-all sm:my-8 sm:w-full sm:max-w-2xl flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-slate-900 to-[#03224d] px-6 py-4 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-blue-300 border border-white/10">
                            <span class="material-symbols-outlined text-[20px]">assignment</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white tracking-tight" id="modal-title">Rincian Permohonan Informasi</h3>
                            <p class="text-xs text-blue-200/80">Informasi ringkas dan pintasan aksi permohonan</p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="closeDetail()" 
                            class="text-white/70 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 overflow-y-auto space-y-5 text-slate-700 text-xs" x-if="selectedItem">
                    <!-- Status & No. Registrasi Banner -->
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">No. Registrasi</div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-mono text-sm font-bold text-slate-800" x-text="selectedItem?.nomor_registrasi"></span>
                                <button type="button" 
                                        @click="copyNomorRegistrasi(selectedItem?.nomor_registrasi)" 
                                        class="text-slate-400 hover:text-blue-600 transition-colors p-1 rounded hover:bg-white cursor-pointer"
                                        title="Salin Nomor Registrasi">
                                    <span class="material-symbols-outlined text-[16px]" x-show="!copied">content_copy</span>
                                    <span class="material-symbols-outlined text-[16px] text-emerald-600" x-show="copied" style="display: none;">check</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-col sm:items-end">
                            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Terkini</div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-current/20"
                                  :class="selectedItem?.status_badge_class">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75"></span>
                                <span x-text="selectedItem?.status_label"></span>
                            </span>
                        </div>
                    </div>

                    <!-- 2-Columns Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Identitas Pemohon -->
                        <div class="border border-slate-200/80 rounded-xl p-4 bg-white space-y-2.5">
                            <div class="flex items-center gap-1.5 font-bold text-slate-800 text-xs pb-2 border-b border-slate-100">
                                <span class="material-symbols-outlined text-[16px] text-blue-600">person</span>
                                <span>Identitas Pemohon</span>
                            </div>
                            
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Nama Pemohon</span>
                                <span class="font-bold text-slate-800 text-xs" x-text="selectedItem?.nama_pemohon"></span>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">NIK / No. Badan Hukum</span>
                                <span class="font-medium text-slate-700" x-text="selectedItem?.nik_atau_no_badan_hukum"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Kategori</span>
                                    <span class="font-medium text-slate-700" x-text="selectedItem?.kategori_pemohon"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Pekerjaan</span>
                                    <span class="font-medium text-slate-700" x-text="selectedItem?.pekerjaan"></span>
                                </div>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Email & Kontak</span>
                                <span class="font-medium text-slate-700" x-text="selectedItem?.email + ' / ' + selectedItem?.no_telp"></span>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Alamat</span>
                                <span class="font-medium text-slate-700" x-text="selectedItem?.alamat"></span>
                            </div>
                        </div>

                        <!-- Rincian Permohonan -->
                        <div class="border border-slate-200/80 rounded-xl p-4 bg-white space-y-2.5">
                            <div class="flex items-center gap-1.5 font-bold text-slate-800 text-xs pb-2 border-b border-slate-100">
                                <span class="material-symbols-outlined text-[16px] text-blue-600">description</span>
                                <span>Rincian Permohonan</span>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tanggal Masuk</span>
                                <span class="font-medium text-slate-700" x-text="selectedItem?.tanggal_masuk"></span>
                            </div>

                            <template x-if="selectedItem?.subjek_informasi && selectedItem?.subjek_informasi !== '-'">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Judul / Subjek Informasi</span>
                                    <p class="font-bold text-slate-900 bg-blue-50/60 p-2 rounded-lg border border-blue-100 text-xs leading-relaxed" 
                                       x-text="selectedItem?.subjek_informasi"></p>
                                </div>
                            </template>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Isi / Uraian Rincian Informasi</span>
                                <p class="font-medium text-slate-700 bg-slate-50 p-2 rounded-lg border border-slate-100 max-h-24 overflow-y-auto leading-relaxed" 
                                   x-text="selectedItem?.rincian_informasi"></p>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tujuan Penggunaan</span>
                                <p class="font-medium text-slate-700 bg-slate-50 p-2 rounded-lg border border-slate-100 max-h-20 overflow-y-auto leading-relaxed" 
                                   x-text="selectedItem?.tujuan_penggunaan"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Cara Memperoleh</span>
                                    <span class="font-medium text-slate-700" x-text="selectedItem?.cara_memperoleh"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Bentuk Salinan</span>
                                    <span class="font-medium text-slate-700" x-text="selectedItem?.cara_salinan"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Berkas Identitas jika ada -->
                    <template x-if="selectedItem?.file_identitas">
                        <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 text-[20px]">badge</span>
                                <div>
                                    <div class="font-semibold text-slate-800 text-xs">Berkas Identitas Pemohon (KTP / Akta)</div>
                                    <div class="text-[11px] text-slate-500">Tersedia dokumen lampiran identitas</div>
                                </div>
                            </div>
                            <a :href="selectedItem?.file_identitas" target="_blank" 
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-blue-200 text-blue-700 hover:bg-blue-50 font-semibold text-xs transition-colors shadow-2xs">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                <span>Lihat Berkas</span>
                            </a>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer Actions -->
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="w-full sm:w-auto">
                        <template x-if="selectedItem?.url_tanda_terima">
                            <a :href="selectedItem?.url_tanda_terima" target="_blank"
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-semibold text-xs transition-colors shadow-2xs">
                                <span class="material-symbols-outlined text-[16px]">print</span>
                                <span>Cetak Tanda Terima</span>
                            </a>
                        </template>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <button type="button" 
                                @click="closeDetail()"
                                class="flex-1 sm:flex-initial px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs transition-colors shadow-2xs cursor-pointer">
                            Tutup
                        </button>
                        
                        <a :href="selectedItem?.url_show"
                           class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-[#03224d] text-white hover:bg-[#0B1B3D] font-semibold text-xs transition-all shadow-xs">
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            <span>Proses & Detail Lengkap</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</main>
@endsection
