@extends('admin.layouts.app')

@section('title', 'Kelola Banner Carousel - Admin E-PPID')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen" x-data="{
    previewModalOpen: false,
    previewImageUrl: '',
    previewTitle: '',
    deleteModalOpen: false,
    deleteUrl: '',
    deleteTitle: '',
    openPreview(url, title) {
        this.previewImageUrl = url;
        this.previewTitle = title;
        this.previewModalOpen = true;
    },
    confirmDelete(url, title) {
        this.deleteUrl = url;
        this.deleteTitle = title;
        this.deleteModalOpen = true;
    }
}">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">view_carousel</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Banner Carousel Landing Page</h1>
            </div>
            <p class="text-xs text-slate-500 ml-10">Kelola slider visual, pesan sambutan, dan tautan aksi unggulan pada halaman utama portal E-PPID</p>
        </div>

        <div class="flex items-center gap-2.5 self-stretch sm:self-auto justify-end">
            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 bg-white text-slate-700 hover:text-blue-600 hover:bg-slate-50 px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-200/80 transition-colors shadow-2xs">
                <span class="material-symbols-outlined text-[16px]">visibility</span>
                <span>Lihat Landing Page</span>
            </a>
            <a href="{{ route('admin.carousel.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition-colors shadow-xs">
                <span class="material-symbols-outlined text-[16px]">add_photo_alternate</span>
                <span>Tambah Slide Banner</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs font-medium shadow-2xs">
        <div class="flex items-center gap-2.5">
            <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
            <span class="material-symbols-outlined text-[16px]">close</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-xs font-medium shadow-2xs">
        <div class="flex items-center gap-2.5">
            <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
            <span class="material-symbols-outlined text-[16px]">close</span>
        </button>
    </div>
    @endif

    <!-- Bento Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-5 mb-6">
        <!-- Card 1: Total Slide -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Slide Banner</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $totalSlides }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">slideshow</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Koleksi Banner:</span>
                <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">Hero Carousel</span>
            </div>
        </div>

        <!-- Card 2: Slide Aktif -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Slide Aktif Ditampilkan</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-emerald-600 m-0 tracking-tight">{{ $activeSlides }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">visibility</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Status Tayang:</span>
                <span class="inline-flex items-center gap-1 font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live di Beranda
                </span>
            </div>
        </div>

        <!-- Card 3: Slide Nonaktif -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Draft / Nonaktif</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-600 m-0 tracking-tight">{{ $inactiveSlides }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">visibility_off</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Disembunyikan:</span>
                <span class="font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">Arsip / Draft</span>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-500 text-[18px]">format_list_numbered</span>
                <h3 class="text-sm font-bold text-slate-800">Daftar Slide Banner (Urutan Tampil)</h3>
            </div>
            <span class="text-xs text-slate-400">Diurutkan berdasarkan kolom Urutan terkecil (1, 2, 3, ...)</span>
        </div>

        @if($carousels->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">Urutan</th>
                        <th class="py-3 px-4 w-44">Banner</th>
                        <th class="py-3 px-4 min-w-[240px]">Konten / Judul &amp; Deskripsi</th>
                        <th class="py-3 px-4 min-w-[160px]">Tombol Aksi (CTA)</th>
                        <th class="py-3 px-4 w-28 text-center">Status</th>
                        <th class="py-3 px-4 w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($carousels as $carousel)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <!-- Urutan -->
                        <td class="py-3.5 px-4 text-center font-bold">
                            <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs flex items-center justify-center mx-auto border border-slate-200">
                                {{ $carousel->order }}
                            </span>
                        </td>

                        <!-- Thumbnail Preview -->
                        <td class="py-3.5 px-4">
                            <div class="relative w-36 h-20 rounded-xl overflow-hidden border border-slate-200/80 bg-slate-900 group cursor-pointer shadow-2xs"
                                 @click="openPreview('{{ $carousel->image_url }}', '{{ addslashes($carousel->title) }}')">
                                <img src="{{ $carousel->image_url }}" alt="{{ $carousel->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                    <span class="material-symbols-outlined text-[18px]">zoom_in</span>
                                </div>
                            </div>
                        </td>

                        <!-- Konten Judul & Subtitle -->
                        <td class="py-3.5 px-4">
                            <h4 class="font-bold text-slate-900 text-sm mb-1 leading-snug">{{ $carousel->title }}</h4>
                            <p class="text-slate-500 text-[11px] line-clamp-2 leading-relaxed max-w-xl">
                                {{ $carousel->subtitle ?: 'Tidak ada deskripsi tambahan.' }}
                            </p>
                        </td>

                        <!-- Tombol CTA -->
                        <td class="py-3.5 px-4">
                            @if($carousel->button_text && $carousel->button_url)
                            <div class="flex flex-col gap-1">
                                <div class="inline-flex items-center gap-1 font-semibold text-slate-800 text-[11px]">
                                    <span class="material-symbols-outlined text-[14px] text-blue-600">touch_app</span>
                                    <span>{{ $carousel->button_text }}</span>
                                </div>
                                <a href="{{ $carousel->button_url }}" target="_blank" class="text-[10px] text-slate-400 hover:text-blue-600 font-mono truncate max-w-[180px] flex items-center gap-0.5">
                                    <span>{{ $carousel->button_url }}</span>
                                    <span class="material-symbols-outlined text-[12px]">open_in_new</span>
                                </a>
                                <span class="text-[9px] text-slate-400">
                                    Target: <code class="bg-slate-100 px-1 py-0.2 rounded">{{ $carousel->button_target }}</code>
                                </span>
                            </div>
                            @else
                            <span class="text-slate-400 italic text-[11px]">Tanpa tombol aksi</span>
                            @endif
                        </td>

                        <!-- Status Toggle -->
                        <td class="py-3.5 px-4 text-center">
                            <form action="{{ route('admin.carousel.toggle-status', $carousel->id) }}" method="POST" class="inline-block m-0">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border transition-colors cursor-pointer {{ $carousel->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}"
                                        title="Klik untuk mengubah status aktif/nonaktif">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $carousel->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $carousel->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </button>
                            </form>
                        </td>

                        <!-- Aksi (Edit, Hapus) -->
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.carousel.edit', $carousel->id) }}" 
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs"
                                   title="Edit Slide">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </a>
                                <button type="button" 
                                        @click="confirmDelete('{{ route('admin.carousel.destroy', $carousel->id) }}', '{{ addslashes($carousel->title) }}')"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs cursor-pointer"
                                        title="Hapus Slide">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-16 px-4 text-center">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3 border border-blue-100 shadow-2xs">
                <span class="material-symbols-outlined text-[28px]">add_photo_alternate</span>
            </div>
            <h4 class="text-sm font-bold text-slate-800 mb-1">Belum Ada Slide Banner Carousel</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">Tambahkan slide banner visual untuk mempercantik dan menyampaikan informasi utama di landing page.</p>
            <a href="{{ route('admin.carousel.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition-colors shadow-xs">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>Buat Slide Pertama</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Image Preview Modal -->
    <div x-show="previewModalOpen" 
         style="display: none;" 
         class="fixed inset-0 z-[100] overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" @click="previewModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-3xl border border-slate-200">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-800 truncate" x-text="previewTitle"></h3>
                    <button type="button" @click="previewModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <div class="p-4 bg-slate-950 flex items-center justify-center">
                    <img :src="previewImageUrl" :alt="previewTitle" class="max-h-[70vh] w-auto max-w-full rounded-lg object-contain shadow-md">
                </div>
                <div class="px-5 py-3 bg-white flex justify-end">
                    <button type="button" @click="previewModalOpen = false" class="px-4 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteModalOpen" 
         style="display: none;" 
         class="fixed inset-0 z-[100] overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs transition-opacity" @click="deleteModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md border border-slate-200 p-6">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 border border-rose-100 shadow-2xs">
                    <span class="material-symbols-outlined text-[24px]">delete_forever</span>
                </div>
                <h3 class="text-base font-bold text-slate-900 text-center mb-1">Hapus Slide Banner?</h3>
                <p class="text-xs text-slate-500 text-center mb-5 leading-relaxed">
                    Slide <strong class="text-slate-800" x-text="deleteTitle"></strong> akan dihapus permanen dari sistem beserta berkas gambarnya. Tindakan ini tidak dapat dibatalkan.
                </p>
                
                <form :action="deleteUrl" method="POST" class="m-0 flex items-center justify-center gap-2.5">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="w-1/2 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition-colors shadow-xs">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
