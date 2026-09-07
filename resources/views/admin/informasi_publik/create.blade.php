@extends('admin.layouts.app')

@section('title', 'Tambah Dokumen Informasi Publik - Admin E-PPID')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Tambah Dokumen Informasi Publik</h1>
                </div>
                <p class="text-xs text-slate-500 ml-10">Unggah berkas resmi dan tentukan klasifikasi sesuai regulasi UU KIP dan dokumen Bapperida</p>
            </div>
            <a href="{{ route('admin.informasi-publik.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200/80 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-all shadow-2xs">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 md:p-8">
            <form action="{{ route('admin.informasi-publik.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Judul Dokumen -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Judul Dokumen Publik <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required 
                           placeholder="Contoh: Rencana Kerja Pemerintah Daerah (RKPD) Kabupaten Ciamis Tahun 2025"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-medium outline-none transition-all">
                    @error('judul')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Two-Column Grid: Kategori UU KIP & Jenis Dokumen Perencanaan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kategori UU KIP -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Kategori Regulasi UU KIP <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori_informasi_publik_id" required 
                                data-placeholder="-- Pilih Kategori UU KIP --"
                                data-search-placeholder="Cari kategori UU KIP..."
                                class="custom-select w-full text-xs font-medium">
                            <option value="">-- Pilih Kategori UU KIP --</option>
                            @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_informasi_publik_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }} ({{ $kat->deskripsi }})
                            </option>
                            @endforeach
                        </select>
                        @error('kategori_informasi_publik_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Dokumen Perencanaan Bapperida -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Jenis Dokumen Perencanaan <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_dokumen" required 
                                data-placeholder="-- Pilih Jenis Dokumen --"
                                data-search-placeholder="Cari jenis dokumen perencanaan..."
                                class="custom-select w-full text-xs font-medium">
                            <option value="">-- Pilih Jenis Dokumen --</option>
                            @foreach($jenisOptions as $key => $label)
                            <option value="{{ $key }}" {{ old('jenis_dokumen') == $key ? 'selected' : '' }}>
                                {{ $key }} - {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('jenis_dokumen')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Grid: Tahun, Unit Pengolah, Penanggung Jawab -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Tahun Dokumen <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" required min="2000" max="{{ date('Y') + 1 }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-medium outline-none transition-all">
                        @error('tahun')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Unit Pengolah (Bidang)
                        </label>
                        <select name="unit_pengolah_id" 
                                data-placeholder="-- Tanpa Unit Khusus --"
                                data-search-placeholder="Cari unit pengolah..."
                                class="custom-select w-full text-xs font-medium">
                            <option value="">-- Tanpa Unit Khusus --</option>
                            @foreach($unitPengolahList as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_pengolah_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->nama_bidang }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nama Penanggung Jawab
                        </label>
                        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', 'Bapperida Kabupaten Ciamis') }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-medium outline-none transition-all">
                    </div>
                </div>

                <!-- Ringkasan / Deskripsi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Ringkasan Dokumen (Abstrak)
                    </label>
                    <textarea name="ringkasan" rows="3" placeholder="Tuliskan gambaran ringkas mengenai ruang lingkup dan substansi isi dokumen ini..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-medium outline-none transition-all">{{ old('ringkasan') }}</textarea>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Unggah Berkas PDF Dokumen
                    </label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-6 text-center transition-all bg-slate-50/50">
                        <span class="material-symbols-outlined text-3xl text-slate-400 mb-2">upload_file</span>
                        <p class="text-xs text-slate-600 font-semibold mb-1">Pilih berkas PDF dari komputer Anda</p>
                        <p class="text-[11px] text-slate-400 mb-3">Format yang didukung: .PDF (Maksimal 30 MB)</p>
                        <input type="file" name="file_dokumen" accept="application/pdf" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    </div>
                    @error('file_dokumen')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Publikasi Switch -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                    <label for="is_active" class="text-xs font-bold text-slate-700 cursor-pointer select-none">
                        Publikasikan langsung ke portal publik (Daftar Informasi Publik)
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.informasi-publik.index') }}" 
                       class="px-5 py-2.5 rounded-xl border border-slate-200/80 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-all shadow-2xs">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                        Simpan Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
