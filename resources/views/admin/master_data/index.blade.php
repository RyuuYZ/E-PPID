@extends('admin.layouts.app')

@section('title', 'Master Data - Admin E-PPID')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen" 
      x-data="{ 
          activeTab: '{{ $initialTab ?? 'bidang' }}',
          search: '',

          // Deletion Confirmation Modal State
          deleteModalOpen: false,
          deleteUrl: '',
          deleteTitle: '',
          deleteType: '',

          openDelete(url, title, type) {
              this.deleteUrl = url;
              this.deleteTitle = title;
              this.deleteType = type;
              this.deleteModalOpen = true;
          },
          closeDelete() {
              this.deleteModalOpen = false;
              this.deleteUrl = '';
              this.deleteTitle = '';
              this.deleteType = '';
          },

          // CRUD Modals State
          createBidangModal: false,
          editBidangModal: false,
          editBidangData: { id: '', nama_bidang: '', deskripsi: '' },

          createKategoriPemohonModal: false,
          editKategoriPemohonModal: false,
          editKategoriPemohonData: { id: '', nama_kategori: '' },

          createCaraModal: false,
          editCaraModal: false,
          editCaraData: { id: '', nama_cara: '', deskripsi: '' },

          createKategoriInfoModal: false,
          editKategoriInfoModal: false,
          editKategoriInfoData: { id: '', nama_kategori: '', deskripsi: '', icon: 'folder', bg_color: 'bg-blue-50', text_color: 'text-blue-600' },

          // Client-side instant filter (like DIP)
          applyFilters() {
              const q = this.search.toLowerCase().trim();
              
              // Filter active tab rows
              const rows = document.querySelectorAll('.master-row-' + this.activeTab);
              let visibleCount = 0;
              rows.forEach(row => {
                  const text = (row.dataset.searchtext || '').toLowerCase();
                  const show = !q || text.includes(q);
                  row.style.display = show ? '' : 'none';
                  if (show) visibleCount++;
              });

              const emptyRow = document.getElementById('emptyRow-' + this.activeTab);
              if (emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';

              const counter = document.getElementById('counter-' + this.activeTab);
              if (counter) counter.textContent = 'Menampilkan ' + visibleCount + ' data';
          },

          setTab(tab) {
              this.activeTab = tab;
              this.search = '';
              this.$nextTick(() => this.applyFilters());
          },

          openCreateForCurrentTab() {
              if (this.activeTab === 'bidang') this.createBidangModal = true;
              else if (this.activeTab === 'kategori-pemohon') this.createKategoriPemohonModal = true;
              else if (this.activeTab === 'cara-memperoleh') this.createCaraModal = true;
              else if (this.activeTab === 'kategori-informasi') this.createKategoriInfoModal = true;
          }
      }"
      x-init="$nextTick(() => applyFilters())">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">dataset</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Master Data Terpadu</h1>
            </div>
            <p class="text-xs text-slate-500 ml-10">Kelola master referensi bidang unit pengolah, kategori pemohon, cara perolehan, dan klasifikasi informasi</p>
        </div>

        <div class="flex items-center gap-2.5 self-stretch sm:self-auto justify-end">
            <button type="button" @click="openCreateForCurrentTab()" 
                    class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition-colors shadow-xs cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span x-text="activeTab === 'bidang' ? 'Tambah Bidang' : (activeTab === 'kategori-pemohon' ? 'Tambah Kategori Pemohon' : (activeTab === 'cara-memperoleh' ? 'Tambah Cara Perolehan' : 'Tambah Kategori Info'))">Tambah Data</span>
            </button>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs shadow-2xs">
            <div class="flex items-center gap-2 font-bold mb-1">
                <span class="material-symbols-outlined text-[18px] text-rose-600">warning</span>
                <span>Terjadi kesalahan pada input data:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-1 text-slate-700">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Bento Metric Cards Grid (4 columns) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Master Bidang -->
        <div @click="setTab('bidang')" 
             :class="activeTab === 'bidang' ? 'border-blue-500 ring-2 ring-blue-500/10 shadow-sm' : 'border-slate-200/80 hover:border-slate-300 shadow-2xs'"
             class="bg-white rounded-2xl p-4 md:p-5 border transition-all cursor-pointer group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Master Bidang</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $stats['bidang'] }}</h2>
                </div>
                <div :class="activeTab === 'bidang' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-600 group-hover:scale-105'"
                     class="w-11 h-11 rounded-xl border border-blue-100 flex items-center justify-center transition-all">
                    <span class="material-symbols-outlined text-[22px]">corporate_fare</span>
                </div>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Unit Pengolah:</span>
                <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">Bapperida</span>
            </div>
        </div>

        <!-- Card 2: Kategori Pemohon -->
        <div @click="setTab('kategori-pemohon')" 
             :class="activeTab === 'kategori-pemohon' ? 'border-indigo-500 ring-2 ring-indigo-500/10 shadow-sm' : 'border-slate-200/80 hover:border-slate-300 shadow-2xs'"
             class="bg-white rounded-2xl p-4 md:p-5 border transition-all cursor-pointer group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Kategori Pemohon</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $stats['kategori_pemohon'] }}</h2>
                </div>
                <div :class="activeTab === 'kategori-pemohon' ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600 group-hover:scale-105'"
                     class="w-11 h-11 rounded-xl border border-indigo-100 flex items-center justify-center transition-all">
                    <span class="material-symbols-outlined text-[22px]">groups</span>
                </div>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Segmentasi:</span>
                <span class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">Pemohon</span>
            </div>
        </div>

        <!-- Card 3: Cara Perolehan -->
        <div @click="setTab('cara-memperoleh')" 
             :class="activeTab === 'cara-memperoleh' ? 'border-emerald-500 ring-2 ring-emerald-500/10 shadow-sm' : 'border-slate-200/80 hover:border-slate-300 shadow-2xs'"
             class="bg-white rounded-2xl p-4 md:p-5 border transition-all cursor-pointer group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Cara Perolehan</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $stats['cara_memperoleh'] }}</h2>
                </div>
                <div :class="activeTab === 'cara-memperoleh' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-600 group-hover:scale-105'"
                     class="w-11 h-11 rounded-xl border border-emerald-100 flex items-center justify-center transition-all">
                    <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                </div>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Kanal Layanan:</span>
                <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">Metode</span>
            </div>
        </div>

        <!-- Card 4: Kategori Info Publik -->
        <div @click="setTab('kategori-informasi')" 
             :class="activeTab === 'kategori-informasi' ? 'border-violet-500 ring-2 ring-violet-500/10 shadow-sm' : 'border-slate-200/80 hover:border-slate-300 shadow-2xs'"
             class="bg-white rounded-2xl p-4 md:p-5 border transition-all cursor-pointer group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Kategori Info DIP</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $stats['kategori_informasi'] }}</h2>
                </div>
                <div :class="activeTab === 'kategori-informasi' ? 'bg-violet-600 text-white' : 'bg-violet-50 text-violet-600 group-hover:scale-105'"
                     class="w-11 h-11 rounded-xl border border-violet-100 flex items-center justify-center transition-all">
                    <span class="material-symbols-outlined text-[22px]">category</span>
                </div>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Klasifikasi DIP:</span>
                <span class="font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-md border border-violet-100">UU KIP</span>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar Card (Like DIP) -->
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200/80 shadow-xs mb-6">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3.5">
            <!-- Segmented Tab Switcher (Inside Toolbar) -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100/80 rounded-xl overflow-x-auto scrollbar-none shrink-0">
                <button type="button" @click="setTab('bidang')" 
                        :class="activeTab === 'bidang' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer whitespace-nowrap">
                    <span class="material-symbols-outlined text-[16px]">corporate_fare</span>
                    <span>Bidang / Unit</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold"
                          :class="activeTab === 'bidang' ? 'bg-blue-50 text-blue-600' : 'bg-slate-200 text-slate-600'">{{ $stats['bidang'] }}</span>
                </button>

                <button type="button" @click="setTab('kategori-pemohon')" 
                        :class="activeTab === 'kategori-pemohon' ? 'bg-white text-indigo-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer whitespace-nowrap">
                    <span class="material-symbols-outlined text-[16px]">groups</span>
                    <span>Kategori Pemohon</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold"
                          :class="activeTab === 'kategori-pemohon' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-200 text-slate-600'">{{ $stats['kategori_pemohon'] }}</span>
                </button>

                <button type="button" @click="setTab('cara-memperoleh')" 
                        :class="activeTab === 'cara-memperoleh' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer whitespace-nowrap">
                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                    <span>Cara Perolehan</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold"
                          :class="activeTab === 'cara-memperoleh' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-200 text-slate-600'">{{ $stats['cara_memperoleh'] }}</span>
                </button>

                <button type="button" @click="setTab('kategori-informasi')" 
                        :class="activeTab === 'kategori-informasi' ? 'bg-white text-violet-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer whitespace-nowrap">
                    <span class="material-symbols-outlined text-[16px]">category</span>
                    <span>Kategori Info</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold"
                          :class="activeTab === 'kategori-informasi' ? 'bg-violet-50 text-violet-600' : 'bg-slate-200 text-slate-600'">{{ $stats['kategori_informasi'] }}</span>
                </button>
            </div>

            <!-- Instant Search Input -->
            <div class="relative flex-1 max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">search</span>
                <input type="text" x-model="search" @input.debounce.200ms="applyFilters()" 
                       placeholder="Cari data pada tabel ini..." 
                       class="w-full h-9 pl-9 pr-8 text-xs bg-slate-50/70 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 font-medium text-slate-800 transition-all">
                <button type="button" x-show="search" @click="search = ''; applyFilters()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined text-[15px]">close</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Card (Like DIP) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Table Header Info Bar -->
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400 text-[18px]">table_rows</span>
                <h2 class="text-sm font-bold text-slate-800 m-0" 
                    x-text="activeTab === 'bidang' ? 'Master Data Bidang / Unit Pengolah' : (activeTab === 'kategori-pemohon' ? 'Master Data Kategori Pemohon' : (activeTab === 'cara-memperoleh' ? 'Master Data Cara Memperoleh Informasi' : 'Master Data Kategori Informasi Publik'))">
                    Daftar Data
                </h2>
            </div>
            <span :id="'counter-' + activeTab" class="text-xs text-slate-500 font-medium">Memuat data...</span>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL 1: MASTER BIDANG                                                    -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'bidang'" class="w-full overflow-x-auto">
            <table class="w-full table-fixed text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/60 text-[11px] text-slate-500 uppercase tracking-wider font-bold">
                        <th class="w-[8%] px-4 py-3.5">ID</th>
                        <th class="w-[36%] px-4 py-3.5">Nama Bidang / Unit Pengolah</th>
                        <th class="w-[34%] px-4 py-3.5">Deskripsi / Ruang Lingkup</th>
                        <th class="w-[12%] px-3 py-3.5 text-center whitespace-nowrap">Relasi Data</th>
                        <th class="w-[10%] px-3 py-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600 text-xs">
                    @forelse($unitPengolahList as $up)
                    <tr class="master-row-bidang hover:bg-slate-50/80 transition-colors"
                        data-searchtext="{{ strtolower($up->id . ' ' . $up->nama_bidang . ' ' . ($up->deskripsi ?? '')) }}">
                        <!-- ID -->
                        <td class="px-4 py-3.5 text-slate-400 font-mono font-medium">
                            #{{ $up->id }}
                        </td>
                        <!-- Nama Bidang -->
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100 shadow-2xs">
                                    <span class="material-symbols-outlined text-[16px]">corporate_fare</span>
                                </div>
                                <span class="font-bold text-slate-900 leading-snug truncate" title="{{ $up->nama_bidang }}">{{ $up->nama_bidang }}</span>
                            </div>
                        </td>
                        <!-- Deskripsi -->
                        <td class="px-4 py-3.5 text-slate-500 truncate" title="{{ $up->deskripsi ?? '-' }}">
                            {{ $up->deskripsi ?? '-' }}
                        </td>
                        <!-- Relasi Data -->
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100" title="Jumlah Pengguna">
                                    {{ $up->users_count }} User
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100" title="Jumlah Permohonan">
                                    {{ $up->permohonan_informasis_count }} Permohonan
                                </span>
                            </div>
                        </td>
                        <!-- Aksi (3-dot dropdown) -->
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click="open = !open" @click.away="open = false" type="button" 
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-slate-500 hover:text-slate-800 hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                </button>
                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-100" 
                                     x-transition:enter-start="transform opacity-0 scale-95" 
                                     x-transition:enter-end="transform opacity-100 scale-100" 
                                     x-transition:leave="transition ease-in duration-75" 
                                     x-transition:leave-start="transform opacity-100 scale-100" 
                                     x-transition:leave-end="transform opacity-0 scale-95" 
                                     class="origin-top-right absolute right-0 mt-1.5 w-40 rounded-xl shadow-lg bg-white ring-1 ring-slate-200 z-50 py-1.5 border border-slate-100">
                                    <button type="button" @click="open = false; editBidangData = { id: '{{ $up->id }}', nama_bidang: '{{ addslashes($up->nama_bidang) }}', deskripsi: '{{ addslashes($up->deskripsi ?? '') }}' }; editBidangModal = true;" 
                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-amber-600 transition-colors font-medium cursor-pointer text-left">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Edit Bidang
                                    </button>
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <button type="button" @click="open = false; openDelete('{{ route('admin.unit-pengolah.destroy', $up->id) }}', '{{ addslashes($up->nama_bidang) }}', 'Bidang / Unit Pengolah')" 
                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors font-medium cursor-pointer text-left">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                        Hapus Bidang
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                    <tr id="emptyRow-bidang" style="display: none;">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl text-slate-300">find_in_page</span>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada data bidang yang sesuai.</p>
                                <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian Anda atau tambah bidang baru.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL 2: KATEGORI PEMOHON                                                 -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'kategori-pemohon'" class="w-full overflow-x-auto" style="display: none;">
            <table class="w-full table-fixed text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/60 text-[11px] text-slate-500 uppercase tracking-wider font-bold">
                        <th class="w-[10%] px-4 py-3.5">ID</th>
                        <th class="w-[60%] px-4 py-3.5">Nama Kategori Pemohon</th>
                        <th class="w-[20%] px-3 py-3.5 text-center whitespace-nowrap">Permohonan Terkait</th>
                        <th class="w-[10%] px-3 py-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600 text-xs">
                    @forelse($kategoriPemohonList as $kp)
                    <tr class="master-row-kategori-pemohon hover:bg-slate-50/80 transition-colors"
                        data-searchtext="{{ strtolower($kp->id . ' ' . $kp->nama_kategori) }}">
                        <td class="px-4 py-3.5 text-slate-400 font-mono font-medium">
                            #{{ $kp->id }}
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-900">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100 shadow-2xs">
                                    <span class="material-symbols-outlined text-[16px]">groups</span>
                                </div>
                                <span>{{ $kp->nama_kategori }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ $kp->permohonan_informasis_count }} Permohonan
                            </span>
                        </td>
                        <!-- Aksi (3-dot dropdown) -->
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click="open = !open" @click.away="open = false" type="button" 
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-slate-500 hover:text-slate-800 hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs cursor-pointer">
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
                                    <button type="button" @click="open = false; editKategoriPemohonData = { id: '{{ $kp->id }}', nama_kategori: '{{ addslashes($kp->nama_kategori) }}' }; editKategoriPemohonModal = true;" 
                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-amber-600 transition-colors font-medium cursor-pointer text-left">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Edit Kategori
                                    </button>
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <button type="button" @click="open = false; openDelete('{{ route('admin.kategori-pemohon.destroy', $kp->id) }}', '{{ addslashes($kp->nama_kategori) }}', 'Kategori Pemohon')" 
                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors font-medium cursor-pointer text-left">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                        Hapus Kategori
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                    <tr id="emptyRow-kategori-pemohon" style="display: none;">
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl text-slate-300">find_in_page</span>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada kategori pemohon yang cocok.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL 3: CARA MEMPEROLEH INFORMASI                                        -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'cara-memperoleh'" class="w-full overflow-x-auto" style="display: none;">
            <table class="w-full table-fixed text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/60 text-[11px] text-slate-500 uppercase tracking-wider font-bold">
                        <th class="w-[8%] px-4 py-3.5">ID</th>
                        <th class="w-[36%] px-4 py-3.5">Nama Cara Perolehan</th>
                        <th class="w-[34%] px-4 py-3.5">Keterangan / Detail Layanan</th>
                        <th class="w-[12%] px-3 py-3.5 text-center whitespace-nowrap">Permohonan</th>
                        <th class="w-[10%] px-3 py-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600 text-xs">
                    @forelse($caraMemperolehList as $cm)
                    <tr class="master-row-cara-memperoleh hover:bg-slate-50/80 transition-colors"
                        data-searchtext="{{ strtolower($cm->id . ' ' . $cm->nama_cara . ' ' . ($cm->deskripsi ?? '')) }}">
                        <td class="px-4 py-3.5 text-slate-400 font-mono font-medium">
                            #{{ $cm->id }}
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-900">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 shadow-2xs">
                                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                </div>
                                <span>{{ $cm->nama_cara }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-500 truncate" title="{{ $cm->deskripsi ?? '-' }}">
                            {{ $cm->deskripsi ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $cm->permohonan_informasis_count }} Permohonan
                            </span>
                        </td>
                        <!-- Aksi (3-dot dropdown) -->
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click="open = !open" @click.away="open = false" type="button" 
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-slate-500 hover:text-slate-800 hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs cursor-pointer">
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
                                    <button type="button" @click="open = false; editCaraData = { id: '{{ $cm->id }}', nama_cara: '{{ addslashes($cm->nama_cara) }}', deskripsi: '{{ addslashes($cm->deskripsi ?? '') }}' }; editCaraModal = true;" 
                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-amber-600 transition-colors font-medium cursor-pointer text-left">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Edit Cara
                                    </button>
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <button type="button" @click="open = false; openDelete('{{ route('admin.cara-memperoleh-informasi.destroy', $cm->id) }}', '{{ addslashes($cm->nama_cara) }}', 'Cara Perolehan Informasi')" 
                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors font-medium cursor-pointer text-left">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                        Hapus Cara
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                    <tr id="emptyRow-cara-memperoleh" style="display: none;">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl text-slate-300">find_in_page</span>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada cara perolehan yang cocok.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL 4: KATEGORI INFORMASI PUBLIK                                        -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'kategori-informasi'" class="w-full overflow-x-auto" style="display: none;">
            <table class="w-full table-fixed text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/60 text-[11px] text-slate-500 uppercase tracking-wider font-bold">
                        <th class="w-[8%] px-4 py-3.5">ID</th>
                        <th class="w-[36%] px-4 py-3.5">Nama Kategori & Tampilan</th>
                        <th class="w-[34%] px-4 py-3.5">Deskripsi Ketentuan</th>
                        <th class="w-[12%] px-3 py-3.5 text-center whitespace-nowrap">Dokumen DIP</th>
                        <th class="w-[10%] px-3 py-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600 text-xs">
                    @forelse($kategoriInformasiList as $ki)
                    <tr class="master-row-kategori-informasi hover:bg-slate-50/80 transition-colors"
                        data-searchtext="{{ strtolower($ki->id . ' ' . $ki->nama_kategori . ' ' . ($ki->deskripsi ?? '')) }}">
                        <td class="px-4 py-3.5 text-slate-400 font-mono font-medium">
                            #{{ $ki->id }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg {{ $ki->bg_color ?? 'bg-blue-50' }} {{ $ki->text_color ?? 'text-blue-600' }} border border-slate-200/60 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[17px]">{{ $ki->icon ?? 'folder' }}</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 truncate">{{ $ki->nama_kategori }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">Icon: {{ $ki->icon ?? 'folder' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-500 truncate" title="{{ $ki->deskripsi ?? '-' }}">
                            {{ $ki->deskripsi ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold bg-violet-50 text-violet-700 border border-violet-100">
                                {{ $ki->informasi_publiks_count }} Dokumen
                            </span>
                        </td>
                        <!-- Aksi (3-dot dropdown) -->
                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click="open = !open" @click.away="open = false" type="button" 
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-slate-500 hover:text-slate-800 hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs cursor-pointer">
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
                                    <button type="button" @click="open = false; editKategoriInfoData = { 
                                                id: '{{ $ki->id }}', 
                                                nama_kategori: '{{ addslashes($ki->nama_kategori) }}', 
                                                deskripsi: '{{ addslashes($ki->deskripsi ?? '') }}',
                                                icon: '{{ addslashes($ki->icon ?? 'folder') }}',
                                                bg_color: '{{ addslashes($ki->bg_color ?? 'bg-blue-50') }}',
                                                text_color: '{{ addslashes($ki->text_color ?? 'text-blue-600') }}'
                                            }; editKategoriInfoModal = true;" 
                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-amber-600 transition-colors font-medium cursor-pointer text-left">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Edit Kategori
                                    </button>
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <button type="button" @click="open = false; openDelete('{{ route('admin.kategori-informasi-publik.destroy', $ki->id) }}', '{{ addslashes($ki->nama_kategori) }}', 'Kategori Informasi Publik')" 
                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors font-medium cursor-pointer text-left">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                        Hapus Kategori
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                    <tr id="emptyRow-kategori-informasi" style="display: none;">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl text-slate-300">find_in_page</span>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada kategori informasi yang cocok.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Info -->
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/40 text-xs text-slate-500 font-medium">
            Pengelolaan Master Data Sistem Informasi Pelayanan Publik E-PPID
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL CONFIRMATION: DELETE (LIKE DIP / SWEETALERT STYLE)                  -->
    <!-- ========================================================================= -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="closeDelete()" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="p-6">
                        <div class="flex items-center gap-3.5 mb-4">
                            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[24px]">delete_forever</span>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 leading-tight">Konfirmasi Penghapusan</h3>
                                <p class="text-xs text-slate-400 mt-0.5" x-text="deleteType"></p>
                            </div>
                        </div>

                        <div class="bg-rose-50/60 border border-rose-100 rounded-xl p-3.5 mb-2">
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Apakah Anda yakin ingin menghapus data <strong class="text-slate-900" x-text="'“' + deleteTitle + '”'"></strong>?
                            </p>
                            <p class="text-[11px] text-rose-600 font-medium mt-1">
                                Tindakan ini tidak dapat dibatalkan jika data dihapus dari sistem.
                            </p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" @click="closeDelete()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                            <span>Hapus Data</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODALS: CREATE & EDIT BIDANG                                              -->
    <!-- ========================================================================= -->

    <!-- Modal Create Bidang -->
    <div x-show="createBidangModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="createBidangModal = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form action="{{ route('admin.unit-pengolah.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">corporate_fare</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 m-0">Tambah Bidang / Unit Pengolah</h3>
                        </div>
                        <button type="button" @click="createBidangModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nama Bidang <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_bidang" required placeholder="Contoh: Bidang Perencanaan, Pengendalian dan Evaluasi Pembangunan Daerah" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-slate-800">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Deskripsi / Ruang Lingkup</label>
                            <textarea name="deskripsi" rows="3" placeholder="Deskripsi tugas atau peruntukan unit pengolah data ini..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-slate-800"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="createBidangModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition-colors cursor-pointer">Simpan Bidang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Bidang -->
    <div x-show="editBidangModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="editBidangModal = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/unit-pengolah') }}/' + editBidangData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Bidang / Unit Pengolah</h3>
                        </div>
                        <button type="button" @click="editBidangModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nama Bidang <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_bidang" x-model="editBidangData.nama_bidang" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-slate-800">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Deskripsi / Ruang Lingkup</label>
                            <textarea name="deskripsi" x-model="editBidangData.deskripsi" rows="3" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-slate-800"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="editBidangModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition-colors cursor-pointer">Perbarui Bidang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODALS: CREATE & EDIT KATEGORI PEMOHON                                    -->
    <!-- ========================================================================= -->

    <!-- Modal Create Kategori Pemohon -->
    <div x-show="createKategoriPemohonModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="createKategoriPemohonModal = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form action="{{ route('admin.kategori-pemohon.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">groups</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 m-0">Tambah Kategori Pemohon</h3>
                        </div>
                        <button type="button" @click="createKategoriPemohonModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nama Kategori Pemohon <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_kategori" required placeholder="Contoh: Perorangan / Individu, Kelompok Orang, Badan Hukum" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none text-slate-800">
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="createKategoriPemohonModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-colors cursor-pointer">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kategori Pemohon -->
    <div x-show="editKategoriPemohonModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="editKategoriPemohonModal = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/kategori-pemohon') }}/' + editKategoriPemohonData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Kategori Pemohon</h3>
                        </div>
                        <button type="button" @click="editKategoriPemohonModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nama Kategori Pemohon <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_kategori" x-model="editKategoriPemohonData.nama_kategori" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none text-slate-800">
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="editKategoriPemohonModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-colors cursor-pointer">Perbarui Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODALS: CREATE & EDIT CARA MEMPEROLEH INFORMASI                           -->
    <!-- ========================================================================= -->

    <!-- Modal Create Cara -->
    <div x-show="createCaraModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="createCaraModal = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form action="{{ route('admin.cara-memperoleh-informasi.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 m-0">Tambah Cara Perolehan Informasi</h3>
                        </div>
                        <button type="button" @click="createCaraModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nama Cara Perolehan <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_cara" required placeholder="Contoh: Salinan Elektronik (Email / Unduh), Cetak Langsung di Kantor" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-slate-800">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Keterangan / Detail</label>
                            <textarea name="deskripsi" rows="3" placeholder="Penjelasan teknis cara penyerahan informasi kepada pemohon..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-slate-800"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="createCaraModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-colors cursor-pointer">Simpan Cara</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Cara -->
    <div x-show="editCaraModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="editCaraModal = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/cara-memperoleh-informasi') }}/' + editCaraData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Cara Perolehan Informasi</h3>
                        </div>
                        <button type="button" @click="editCaraModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nama Cara Perolehan <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_cara" x-model="editCaraData.nama_cara" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-slate-800">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Keterangan / Detail</label>
                            <textarea name="deskripsi" x-model="editCaraData.deskripsi" rows="3" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-slate-800"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="editCaraModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-colors cursor-pointer">Perbarui Cara</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODALS: CREATE & EDIT KATEGORI INFORMASI PUBLIK                           -->
    <!-- ========================================================================= -->

    <!-- Modal Create Kategori Info -->
    <div x-show="createKategoriInfoModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="createKategoriInfoModal = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form action="{{ route('admin.kategori-informasi-publik.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">category</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 m-0">Tambah Kategori Informasi Publik</h3>
                        </div>
                        <button type="button" @click="createKategoriInfoModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_kategori" required placeholder="Contoh: Informasi Berkala, Informasi Serta Merta, Setiap Saat" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800">
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">Nama Icon</label>
                                <input type="text" name="icon" value="folder" placeholder="folder, bolt, etc" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800 font-mono">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">Warna Background</label>
                                <input type="text" name="bg_color" value="bg-blue-50" placeholder="bg-blue-50" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800 font-mono">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">Warna Teks</label>
                                <input type="text" name="text_color" value="text-blue-600" placeholder="text-blue-600" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800 font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Deskripsi Ketentuan</label>
                            <textarea name="deskripsi" rows="3" placeholder="Ketentuan mengenai jenis informasi publik ini..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="createKategoriInfoModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-violet-600 hover:bg-violet-700 text-white shadow-xs transition-colors cursor-pointer">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kategori Info -->
    <div x-show="editKategoriInfoModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="editKategoriInfoModal = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/kategori-informasi-publik') }}/' + editKategoriInfoData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Kategori Informasi Publik</h3>
                        </div>
                        <button type="button" @click="editKategoriInfoModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_kategori" x-model="editKategoriInfoData.nama_kategori" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800">
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">Nama Icon</label>
                                <input type="text" name="icon" x-model="editKategoriInfoData.icon" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800 font-mono">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">Warna Background</label>
                                <input type="text" name="bg_color" x-model="editKategoriInfoData.bg_color" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800 font-mono">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">Warna Teks</label>
                                <input type="text" name="text_color" x-model="editKategoriInfoData.text_color" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800 font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Deskripsi Ketentuan</label>
                            <textarea name="deskripsi" x-model="editKategoriInfoData.deskripsi" rows="3" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 outline-none text-slate-800"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="editKategoriInfoModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-violet-600 hover:bg-violet-700 text-white shadow-xs transition-colors cursor-pointer">Perbarui Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>
@endsection
