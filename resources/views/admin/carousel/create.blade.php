@extends('admin.layouts.app')

@section('title', 'Tambah Slide Banner - Admin E-PPID')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen" x-data="{
    title: '{{ old('title', 'Keterbukaan Informasi Publik Terpadu') }}',
    subtitle: '{{ old('subtitle', 'Layanan informasi publik yang transparan, akuntabel, dan mudah diakses untuk seluruh masyarakat.') }}',
    badgeText: '{{ old('badge_text', 'Lembaga PPID Bapperida') }}',
    buttonText: '{{ old('button_text', 'Ajukan Permohonan') }}',
    buttonUrl: '{{ old('button_url', '/permohonan/baru') }}',
    buttonTarget: '{{ old('button_target', '_self') }}',
    order: {{ old('order', $nextOrder) }},
    isActive: {{ old('is_active', 1) ? 'true' : 'false' }},
    imagePreview: '',
    handleImageChange(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                this.imagePreview = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
}">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <a href="{{ route('admin.carousel.index') }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                </a>
                <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Tambah Slide Banner Baru</h1>
            </div>
            <p class="text-xs text-slate-500 ml-10.5">Lengkapi informasi slide untuk dipublikasikan pada banner carousel landing page</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.carousel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors">
                Batal
            </a>
        </div>
    </div>

    <!-- Error Validation Alert -->
    @if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-2xs">
        <div class="flex items-center gap-2 font-bold mb-1">
            <span class="material-symbols-outlined text-[18px] text-rose-600">error</span>
            <span>Terdapat kesalahan pengisian data:</span>
        </div>
        <ul class="list-disc list-inside ml-6 space-y-0.5 text-[11px]">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.carousel.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: Form Fields -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Card 1: Informasi Banner -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="material-symbols-outlined text-blue-600 text-[18px]">edit_note</span>
                        <h3 class="text-sm font-bold text-slate-900">Konten &amp; Teks Slide</h3>
                    </div>

                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-700 mb-1">
                            Judul Utama (Headline) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" x-model="title" required
                               class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all"
                               placeholder="Contoh: Keterbukaan Informasi Publik Bapperida Ciamis">
                    </div>

                    <div>
                        <label for="subtitle" class="block text-xs font-bold text-slate-700 mb-1">
                            Subjudul / Deskripsi Pesan <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="subtitle" name="subtitle" rows="3" x-model="subtitle"
                                  class="w-full text-xs rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all leading-relaxed"
                                  placeholder="Tuliskan ringkasan penjelasan singkat mengenai pesan banner..."></textarea>
                    </div>
                </div>

                <!-- Card 2: Gambar Banner -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="material-symbols-outlined text-blue-600 text-[18px]">image</span>
                        <h3 class="text-sm font-bold text-slate-900">Berkas Gambar Banner</h3>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Unggah Gambar Banner / Background <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-blue-500 transition-colors bg-slate-50/50">
                            <div class="space-y-2 text-center">
                                <span class="material-symbols-outlined text-[36px] text-slate-400">cloud_upload</span>
                                <div class="flex text-xs text-slate-600 justify-center">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-lg px-3 py-1 font-bold text-blue-600 hover:text-blue-700 border border-slate-200 shadow-2xs hover:border-blue-200 focus-within:outline-none">
                                        <span>Pilih Berkas Gambar</span>
                                        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="sr-only" required @change="handleImageChange($event)">
                                    </label>
                                </div>
                                <p class="text-[10px] text-slate-400">Format: JPG, PNG, WEBP, SVG (Rekomendasi rasio 16:9 / 2:1, Maks. 5MB)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tombol Aksi (CTA) & Pengaturan -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="material-symbols-outlined text-blue-600 text-[18px]">touch_app</span>
                        <h3 class="text-sm font-bold text-slate-900">Tombol Aksi (Call-To-Action) &amp; Publikasi</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="button_text" class="block text-xs font-bold text-slate-700 mb-1">
                                Teks Tombol <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" id="button_text" name="button_text" x-model="buttonText"
                                   class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all"
                                   placeholder="Contoh: Ajukan Permohonan">
                        </div>

                        <div>
                            <label for="button_url" class="block text-xs font-bold text-slate-700 mb-1">
                                Tautan URL Tujuan <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" id="button_url" name="button_url" x-model="buttonUrl"
                                   class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all font-mono"
                                   placeholder="Contoh: /permohonan/baru atau https://...">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label for="button_target" class="block text-xs font-bold text-slate-700 mb-1">
                                Target Pembukaan Tautan
                            </label>
                            <select id="button_target" name="button_target" x-model="buttonTarget"
                                    class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                                <option value="_self">Buka di Tab yang Sama (_self)</option>
                                <option value="_blank">Buka di Tab Baru (_blank)</option>
                            </select>
                        </div>

                        <div>
                            <label for="order" class="block text-xs font-bold text-slate-700 mb-1">
                                Urutan Kemunculan Slide <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="order" name="order" x-model="order" min="0" required
                                   class="w-full text-xs font-mono rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Status Publikasi Slide</span>
                            <span class="text-[11px] text-slate-400">Jika aktif, slide langsung muncul pada carousel landing page</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" :checked="isActive" @change="isActive = $el.checked">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.carousel.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Simpan Slide Banner</span>
                    </button>
                </div>
            </div>

            <!-- Right Column: Live Interactive Preview -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs sticky top-20">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600 text-[18px]">visibility</span>
                            <h3 class="text-sm font-bold text-slate-900">Live Preview Landing Page</h3>
                        </div>
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">Simulasi Tampilan</span>
                    </div>

                    <!-- Slide Mockup Card -->
                    <div class="relative w-full aspect-[16/10] rounded-2xl overflow-hidden shadow-lg border border-slate-800 bg-[#031b3d] flex flex-col justify-end p-5 text-left group">
                        <!-- Background Image or Gradient -->
                        <template x-if="imagePreview">
                            <img :src="imagePreview" alt="Preview Banner" class="absolute inset-0 w-full h-full object-cover">
                        </template>
                        <template x-if="!imagePreview">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#031b3d] via-[#0B2B5C] to-[#0f3c7e] flex items-center justify-center">
                                <div class="text-center text-blue-200/40 p-4">
                                    <span class="material-symbols-outlined text-[40px] block mb-1">image</span>
                                    <span class="text-[10px] font-medium">Unggah gambar untuk melihat visual penuh</span>
                                </div>
                            </div>
                        </template>

                        <!-- Dark Gradient Overlay for Maximum Readability -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/50 to-transparent z-10"></div>

                        <!-- Content Layer -->
                        <div class="relative z-20 space-y-2">
                            <!-- Title -->
                            <h4 class="text-base sm:text-lg font-bold text-white tracking-tight leading-snug line-clamp-2" x-text="title || 'Judul Utama Slide'"></h4>

                            <!-- Subtitle -->
                            <p class="text-xs text-slate-300 line-clamp-2 leading-relaxed font-normal" x-text="subtitle || 'Deskripsi pesan singkat pada banner slider...'"></p>

                            <!-- CTA Button -->
                            <div x-show="buttonText" class="pt-1">
                                <span class="inline-flex items-center gap-1.5 bg-blue-600 text-white text-[11px] font-bold px-3.5 py-1.5 rounded-lg shadow-md">
                                    <span x-text="buttonText"></span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-500 space-y-1">
                        <div class="flex items-center justify-between">
                            <span>Status Slide:</span>
                            <span class="font-bold" :class="isActive ? 'text-emerald-600' : 'text-slate-400'" x-text="isActive ? '● Aktif (Ditampilkan)' : '○ Nonaktif (Disimpan sebagai draft)'"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Nomor Urutan:</span>
                            <span class="font-bold text-slate-800" x-text="'Slide ke-' + order"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection
