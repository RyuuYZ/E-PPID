@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 md:p-8 bg-slate-50 overflow-y-auto min-h-screen">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Tambah Permohonan (Walk-in)</h1>
                <p class="text-xs text-slate-500 mt-1">Registrasi permohonan informasi publik bagi pemohon yang datang langsung ke meja layanan PPID.</p>
            </div>
            <a href="{{ route('admin.permohonan.index', ['status' => 'diajukan']) }}" 
               class="inline-flex items-center gap-1.5 bg-white border border-slate-200 text-slate-700 font-semibold rounded-xl px-3.5 py-2 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-2xs text-xs self-start sm:self-auto">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-xs text-xs animate-fade-in">
                <div class="flex items-center gap-2 mb-1.5 font-bold text-rose-900 text-sm">
                    <span class="material-symbols-outlined text-rose-600 text-[18px]">error</span>
                    <span>Terdapat Kesalahan Pengisian Formulir</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <form action="{{ route('admin.permohonan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Data Identitas Pemohon -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
                <div class="mb-5 pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Identitas Pemohon</h2>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div class="flex flex-col gap-1.5 sm:col-span-2">
                        <label for="namaLengkap" class="text-xs font-semibold text-slate-700">
                            Nama Lengkap Pemohon <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="namaLengkap" name="nama_pemohon" value="{{ old('nama_pemohon') }}" 
                               class="w-full text-xs border border-slate-200 rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all h-10 px-3.5 text-slate-900 placeholder:text-slate-400" 
                               required placeholder="Contoh: Ahmad Hidayat">
                        @error('nama_pemohon')
                            <p class="text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- NIK / No. Identitas -->
                    <div class="flex flex-col gap-1.5">
                        <div class="flex justify-between items-center">
                            <label for="nik" class="text-xs font-semibold text-slate-700">
                                NIK / No. Identitas <span class="text-rose-500">*</span>
                            </label>
                            <span id="nik-counter" class="text-[10px] text-slate-400 font-mono">{{ strlen(old('nik_atau_no_badan_hukum', '')) }}/16 digit</span>
                        </div>
                        <input type="text" id="nik" name="nik_atau_no_badan_hukum" value="{{ old('nik_atau_no_badan_hukum') }}" 
                               class="w-full text-xs font-mono border border-slate-200 rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all h-10 px-3.5 text-slate-900 placeholder:text-slate-400" 
                               required placeholder="16 digit angka NIK" inputmode="numeric" maxlength="16" pattern="[0-9]{1,16}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16); document.getElementById('nik-counter').innerText = this.value.length + '/16 digit';">
                        @error('nik_atau_no_badan_hukum')
                            <p class="text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Kategori Pemohon -->
                    <div class="flex flex-col gap-1.5">
                        <label for="kategori_pemohon" class="text-xs font-semibold text-slate-700">
                            Kategori Pemohon <span class="text-rose-500">*</span>
                        </label>
                        <select id="kategori_pemohon" name="kategori_pemohon_id" data-placeholder="-- Pilih Kategori --" class="custom-select w-full text-xs font-medium" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriPemohons as $kp)
                                <option value="{{ $kp->id }}" {{ old('kategori_pemohon_id') == $kp->id ? 'selected' : '' }}>{{ $kp->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('kategori_pemohon_id')
                            <p class="text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pekerjaan -->
                    <div class="flex flex-col gap-1.5">
                        <label for="pekerjaan" class="text-xs font-semibold text-slate-700">
                            Pekerjaan / Profesi
                        </label>
                        <input type="text" id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" 
                               class="w-full text-xs border border-slate-200 rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all h-10 px-3.5 text-slate-900 placeholder:text-slate-400" 
                               placeholder="Contoh: Pegawai Swasta, Peneliti, Wiraswasta">
                    </div>

                    <!-- No. Telepon / WhatsApp -->
                    <div class="flex flex-col gap-1.5">
                        <label for="telepon_display" class="text-xs font-semibold text-slate-700">
                            No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-xl border @error('no_telp') border-rose-300 bg-rose-50/20 @else border-slate-200 bg-white @enderror overflow-hidden focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100 transition-all h-10">
                            <span class="inline-flex items-center px-3 bg-slate-100 border-r border-slate-200 text-slate-600 font-semibold text-xs select-none font-mono">
                                +62
                            </span>
                            <input class="h-full px-3 flex-1 bg-transparent outline-none text-slate-900 placeholder:text-slate-400 text-xs font-mono" 
                                   id="telepon_display" 
                                   value="{{ old('no_telp') ? preg_replace('/^\+62/', '', old('no_telp')) : '' }}"
                                   placeholder="81234567890" 
                                   required 
                                   type="text" 
                                   inputmode="numeric" 
                                   maxlength="13" 
                                   oninput="handlePhoneInput(this)">
                        </div>
                        <input type="hidden" name="no_telp" id="telepon" value="{{ old('no_telp') }}" required>
                        @error('no_telp')
                            <p class="text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col gap-1.5 sm:col-span-2">
                        <label for="email" class="text-xs font-semibold text-slate-700">
                            Alamat Email Pemohon <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                class="w-full text-xs border border-slate-200 rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all h-10 px-3.5 text-slate-900 placeholder:text-slate-400" 
                                required placeholder="email@contoh.com">
                        @error('email')
                            <p class="text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Divider Wilayah -->
                    <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Wilayah Domisili</span>
                    </div>

                    <!-- Kabupaten -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-700">Kabupaten / Kota</label>
                        <input type="text" value="Kabupaten Ciamis" readonly 
                               class="w-full text-xs border border-slate-200 rounded-xl bg-slate-50 text-slate-700 font-semibold cursor-not-allowed select-none outline-none h-10 px-3.5">
                        <input type="hidden" name="kabupaten" value="Kabupaten Ciamis">
                    </div>

                    <!-- Kecamatan -->
                    <div class="flex flex-col gap-1.5">
                        <label for="select_kecamatan" class="text-xs font-semibold text-slate-700">
                            Kecamatan <span class="text-rose-500">*</span>
                        </label>
                        <select id="select_kecamatan" name="kecamatan" required onchange="handleKecamatanChange()"
                                class="w-full text-xs border border-slate-200 rounded-xl bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all h-10 px-3.5 text-slate-900">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    <!-- Desa / Kelurahan -->
                    <div class="flex flex-col gap-1.5">
                        <label for="select_desa" class="text-xs font-semibold text-slate-700">
                            Desa / Kelurahan <span class="text-rose-500">*</span>
                        </label>
                        <select id="select_desa" name="desa" required onchange="updateFullAlamat()" disabled
                                class="w-full text-xs border border-slate-200 rounded-xl bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all h-10 px-3.5 text-slate-900 disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed">
                            <option value="">-- Pilih Kecamatan Dahulu --</option>
                        </select>
                    </div>

                    <!-- Detail Alamat -->
                    <div class="flex flex-col gap-1.5">
                        <label for="detail_alamat" class="text-xs font-semibold text-slate-700">
                            Jalan / Dusun / RT / RW
                        </label>
                        <input type="text" id="detail_alamat" name="detail_alamat" value="{{ old('detail_alamat') }}" oninput="updateFullAlamat()"
                               placeholder="Contoh: Jl. Jend. Sudirman No. 16, RT 01/RW 02" 
                               class="w-full text-xs border border-slate-200 bg-white rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all h-10 px-3.5 text-slate-900 placeholder:text-slate-400">
                    </div>

                    <!-- Pratinjau Alamat Lengkap -->
                    <div class="flex flex-col gap-1.5 sm:col-span-2">
                        <label for="alamat" class="text-xs font-semibold text-slate-700">
                            Pratinjau Alamat Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alamat" name="alamat" rows="2" readonly required
                                  placeholder="Pilih Kecamatan dan Desa di atas untuk menghasilkan alamat lengkap..."
                                  class="w-full text-xs border border-slate-200 bg-slate-50 rounded-xl outline-none p-3 resize-none font-medium leading-relaxed text-slate-800">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Rincian Informasi yang Dibutuhkan -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
                <div class="mb-5 pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Informasi yang Diminta</h2>
                </div>
                
                <div class="flex flex-col gap-4">
                    <!-- Subjek / Pokok Informasi -->
                    <div class="flex flex-col gap-1.5">
                        <label for="subjek" class="text-xs font-semibold text-slate-700">
                            Judul / Pokok Informasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="subjek" name="subjek_informasi" value="{{ old('subjek_informasi', old('subjek')) }}" 
                               class="w-full text-xs border border-slate-200 rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all h-10 px-3.5 text-slate-900 placeholder:text-slate-400" 
                               required placeholder="Contoh: Dokumen RTRW Kabupaten Ciamis Tahun 2024-2044">
                        @error('subjek_informasi')
                            <p class="text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rincian Informasi -->
                    <div class="flex flex-col gap-1.5">
                        <label for="deskripsi" class="text-xs font-semibold text-slate-700">
                            Uraian Rincian Informasi <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="deskripsi" name="rincian_informasi" rows="4" required
                                  placeholder="Jelaskan secara rinci bab, tabel, data statistik, atau dokumen yang dibutuhkan..."
                                  class="w-full text-xs border border-slate-200 rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all p-3.5 resize-none leading-relaxed text-slate-900 placeholder:text-slate-400">{{ old('rincian_informasi') }}</textarea>
                        @error('rincian_informasi')
                            <p class="text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tujuan Penggunaan -->
                    <div class="flex flex-col gap-1.5">
                        <label for="tujuan" class="text-xs font-semibold text-slate-700">
                            Tujuan Penggunaan Informasi <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="tujuan" name="tujuan_penggunaan" rows="3" required
                                  placeholder="Jelaskan tujuan pemanfaatan informasi (Contoh: Riset Akademis, Tesis Magister, Kajian Publik)"
                                  class="w-full text-xs border border-slate-200 rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all p-3.5 resize-none leading-relaxed text-slate-900 placeholder:text-slate-400">{{ old('tujuan_penggunaan') }}</textarea>
                        @error('tujuan_penggunaan')
                            <p class="text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Cara Memperoleh Informasi -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
                <div class="mb-5 pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Cara Memperoleh Informasi</h2>
                </div>
                
                <div class="flex flex-col gap-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($caraMemperoleh as $cara)
                        <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 hover:border-slate-300 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/40">
                            <input type="radio" name="cara_memperoleh_informasi_id" value="{{ $cara->id }}" 
                                   class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500" 
                                   required {{ (old('cara_memperoleh_informasi_id') == $cara->id || $loop->first) ? 'checked' : '' }}>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-900">{{ $cara->nama_cara }}</span>
                                <span class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $cara->deskripsi }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('cara_memperoleh_informasi_id')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 4: Unggah Dokumen Pendukung -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
                <div class="mb-5 pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Dokumen Identitas</h2>
                </div>
                
                <div class="flex flex-col gap-3">
                    <div x-data="fileUpload()" 
                         @dragover.prevent="dragover = true" 
                         @dragleave.prevent="dragover = false" 
                         @drop.prevent="handleDrop"
                         :class="dragover ? 'border-blue-500 bg-blue-50/30' : (error ? 'border-rose-300 bg-rose-50/20' : (file ? 'border-emerald-400 bg-emerald-50/20' : 'border-slate-300 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-400'))"
                         class="border-2 border-dashed rounded-xl p-6 sm:p-8 flex flex-col items-center justify-center text-center transition-all relative">
                        
                        <span class="material-symbols-outlined text-[36px] mb-2 transition-colors" 
                              :class="error ? 'text-rose-500' : (file ? 'text-emerald-600' : 'text-slate-400')" 
                              x-text="error ? 'error' : (file ? 'check_circle' : 'cloud_upload')">cloud_upload</span>
                        
                        <input type="file" id="dokumenKtp" name="file_identitas" :required="!file" accept=".jpg,.jpeg,.png,.pdf" 
                               @change="handleFileChange" class="hidden" x-ref="fileInput">
                        
                        <!-- Default state -->
                        <div x-show="!file && !error">
                            <p class="text-xs text-slate-600 font-medium">
                                <button type="button" @click="$refs.fileInput.click()" class="text-blue-700 font-semibold hover:underline">
                                    Pilih Berkas
                                </button>
                                atau seret berkas ke sini
                            </p>
                            <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, PDF (Maksimal 5MB)</p>
                        </div>

                        <!-- Selected file state -->
                        <div x-show="file && !error" class="flex flex-col items-center" style="display: none;">
                            <p class="text-xs font-bold text-slate-800" x-text="file?.name"></p>
                            <p class="text-[11px] text-slate-500 mt-0.5" x-text="fileSize"></p>
                            <button type="button" @click="resetFile()" class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:underline">
                                Ganti Berkas Lain
                            </button>
                        </div>

                        <!-- Error state -->
                        <div x-show="error" class="flex flex-col items-center" style="display: none;">
                            <p class="text-xs font-semibold text-rose-600" x-text="errorMessage"></p>
                            <button type="button" @click="resetFile()" class="mt-2 text-xs font-semibold text-blue-700 hover:underline">
                                Pilih Berkas Lain
                            </button>
                        </div>
                    </div>

                    @error('file_identitas')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.permohonan.index', ['status' => 'diajukan']) }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-navy-900 hover:bg-navy-800 bg-[#0f1d36] transition-all shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Simpan Permohonan Baru</span>
                </button>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('fileUpload', () => ({
        dragover: false,
        file: null,
        error: false,
        errorMessage: '',
        
        get fileSize() {
            if (!this.file) return '';
            const size = (this.file.size / 1024 / 1024).toFixed(2);
            return size + ' MB';
        },

        handleDrop(e) {
            this.dragover = false;
            if (e.dataTransfer.files.length > 0) {
                this.validateAndSetFile(e.dataTransfer.files[0]);
            }
        },

        handleFileChange(e) {
            if (e.target.files.length > 0) {
                this.validateAndSetFile(e.target.files[0]);
            }
        },

        validateAndSetFile(selectedFile) {
            this.error = false;
            this.errorMessage = '';

            const validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const fileName = selectedFile.name.toLowerCase();
            const fileExt = fileName.split('.').pop();

            if (!validExtensions.includes(fileExt)) {
                this.error = true;
                this.errorMessage = 'Format file (.' + fileExt + ') tidak diizinkan. Harap unggah berkas JPG, PNG, atau PDF.';
                this.$refs.fileInput.value = '';
                this.file = null;
                return;
            }

            if (selectedFile.size > maxSize) {
                this.error = true;
                this.errorMessage = 'Ukuran file melebihi batas maksimal 5MB.';
                this.$refs.fileInput.value = '';
                this.file = null;
                return;
            }

            if (selectedFile.size === 0) {
                this.error = true;
                this.errorMessage = 'Berkas kosong (0 byte).';
                this.$refs.fileInput.value = '';
                this.file = null;
                return;
            }

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(selectedFile);
            this.$refs.fileInput.files = dataTransfer.files;

            this.file = selectedFile;
        },

        resetFile() {
            this.file = null;
            this.error = false;
            this.errorMessage = '';
            this.$refs.fileInput.value = '';
        }
    }));
});

// Data Wilayah Kabupaten Ciamis
const ciamisWilayah = {
    "Banjarsari": ["Banjarsari", "Cibadak", "Cicapar", "Ciherang", "Ciulu", "Kawasen", "Ratawangi", "Sindangsari"],
    "Banjaranyar": ["Banjaranyar", "Cigayam", "Cikaso", "Cikupa", "Kalijaya", "Karyamukti", "Langkapsari", "Pasirlawang"],
    "Baregbeg": ["Baregbeg", "Petirhilir", "Pusakanagara", "Saguling", "Sukamaju", "Sukamulya", "Jelat", "Karang Ampel", "Mekarjaya"],
    "Ciamis": ["Ciamis", "Cigembor", "Kertasari", "Benteng", "Maleber", "Sindangrasa", "Linggasari", "Imbanagara", "Imbanagara Raya", "Pawindan", "Panyingkiran"],
    "Cidolog": ["Cidolog", "Ciparay", "Hegarmanah", "Janggala", "Jelegong"],
    "Cihaurbeuti": ["Cihaurbeuti", "Cijulang", "Cikolak", "Padamulya", "Pamokolan", "Pasirtamiang", "Sukahaji", "Sukahurip", "Sukamaju", "Sumberjaya", "Tanjungsari"],
    "Cijeungjing": ["Bojongmengger", "Cijeungjing", "Ciharalang", "Handapherang", "Karanganyar", "Karangkamulyan", "Kertabumi", "Kertaharja", "Pamalayan", "Utama"],
    "Cikoneng": ["Cikoneng", "Cimari", "Gegempalan", "Kujangsari", "Nasol", "Panaragan", "Sindangsari", "Sukasenang"],
    "Cimaragas": ["Beber", "Bojongmalang", "Cimaragas", "Jayaraksa", "Raksabaya"],
    "Cipaku": ["Bangbayang", "Buniseuri", "Cieurih", "Cipaku", "Gereba", "Jalatrang", "Mekarsari", "Muktisari", "Pusakasari", "Selacau", "Selamanik", "Sukawening", "Tanjungmulya"],
    "Cisaga": ["Bangunharja", "Cisaga", "Danasari", "Girimukti", "Kepel", "Mekarmukti", "Sidamulya", "Sukahurip", "Tanjungjaya", "Wangunjaya"],
    "Jatinagara": ["Bayasari", "Cintanagara", "Dayeuhluhur", "Jatinagara", "Mulyasari", "Sukanagara"],
    "Kawali": ["Citegem", "Karangpawitan", "Kawali", "Kawalimukti", "Linggawangi", "Margamulya", "Purwasari", "Selasari", "Sindangsari", "Talagasari", "Winduraja"],
    "Lakbok": ["Baregbeg", "Cintajaya", "Cintaratu", "Kertajaya", "Kalapasawit", "Karyamulya", "Puloerang", "Rawaapu", "Sidaharja", "Sindangangin", "Sukanagara", "Tambakreja"],
    "Lumbung": ["Awiluar", "Cikupa", "Darmaraja", "Lumbung", "Lumbungsari", "Rawa", "Sadewata", "Sukahsari"],
    "Pamarican": ["Bangunsari", "Bantarsari", "Kertahayu", "Margajaya", "Medangkang", "Neglasari", "Pamarican", "Pasirnagara", "Sidaharja", "Sidamulih", "Sukahurip", "Sukajadi", "Sukajaya", "Sukaparana"],
    "Panawangan": ["Bangunjaya", "Cinyasag", "Gardujaya", "Giriluyu", "Indragiri", "Jagabaya", "Kertajaya", "Kertayasa", "Nagarajati", "Nagarajaya", "Nagarapageuh", "Panawangan", "Sadapaingan", "Sagalaherang"],
    "Panjalu": ["Bahara", "Ciomas", "Hujungtiwu", "Kertamandala", "Mandalare", "Maparah", "Panjalu", "Sandingtaman"],
    "Panumbangan": ["Banjarangsana", "Buanamekar", "Golat", "Jayagiri", "Kertarahayu", "Medanglayang", "Panumbangan", "Payungagung", "Payungsari", "Sindangbarang", "Sindangherang", "Sindangmukti", "Sukakerta", "Tanjungmulya"],
    "Purwadadi": ["Bantardawa", "Karangpaningal", "Padaringan", "Pasirlawang", "Purwadadi", "Purwajaya", "Sidarahayu", "Sukamulya", "Kutawaringin"],
    "Rajadesa": ["Andapraja", "Purwaraja", "Rajadesa", "Sirnabaya", "Sirnajaya", "Sukaharja", "Sukajaya", "Tanjungsari", "Tanjungjaya", "Tanjungukur", "Tigaherang"],
    "Rancah": ["Bojonggedang", "Cileungsir", "Cisontrol", "Dadiharja", "Jangalaharja", "Kawunglarang", "Kiarapayung", "Patakaharja", "Rancah", "Situmukti", "Wangunsari", "Karangpari", "Giriharja"],
    "Sadananya": ["Bendasari", "Gunungsari", "Mambang", "Sadananya", "Mekarjadi", "Sukajadi", "Tanjungsari", "Werasari"],
    "Sindangkasih": ["Budiasih", "Budiharja", "Gunungcupu", "Sindangkasih", "Sukamanah", "Sukaraja", "Sukaresik", "Wanasigra"],
    "Sukadana": ["Bunter", "Ciparigi", "Margaharja", "Salakaria", "Sukadana"],
    "Sukamantri": ["Cibeureum", "Sindanglaya", "Sukamantri", "Tenggerraharja"],
    "Tambaksari": ["Kadupandak", "Karangpaningal", "Karyamekar", "Mekarsari", "Sukasari", "Tambaksari"]
};

document.addEventListener('DOMContentLoaded', function() {
    const kecSelect = document.getElementById('select_kecamatan');
    if (kecSelect) {
        Object.keys(ciamisWilayah).sort().forEach(function(kec) {
            const opt = document.createElement('option');
            opt.value = kec;
            opt.textContent = 'Kecamatan ' + kec;
            kecSelect.appendChild(opt);
        });
    }
});

function handleKecamatanChange() {
    const kecSelect = document.getElementById('select_kecamatan');
    const desaSelect = document.getElementById('select_desa');
    const selectedKec = kecSelect.value;

    desaSelect.innerHTML = '<option value="">-- Pilih Desa / Kelurahan --</option>';

    if (selectedKec && ciamisWilayah[selectedKec]) {
        desaSelect.disabled = false;
        ciamisWilayah[selectedKec].sort().forEach(function(desa) {
            const opt = document.createElement('option');
            opt.value = desa;
            opt.textContent = desa;
            desaSelect.appendChild(opt);
        });
    } else {
        desaSelect.disabled = true;
        desaSelect.innerHTML = '<option value="">-- Pilih Kecamatan Dahulu --</option>';
    }

    updateFullAlamat();
}

function updateFullAlamat() {
    const kec = document.getElementById('select_kecamatan').value;
    const desa = document.getElementById('select_desa').value;
    const detail = document.getElementById('detail_alamat').value.trim();
    const alamatTextarea = document.getElementById('alamat');

    if (!kec || !desa) {
        alamatTextarea.value = '';
        alamatTextarea.placeholder = 'Pilih Kecamatan dan Desa di atas untuk menghasilkan alamat lengkap...';
        return;
    }

    let result = '';
    if (detail) {
        result += detail + ', ';
    }
    result += 'Desa/Kel. ' + desa + ', Kec. ' + kec + ', Kab. Ciamis, Jawa Barat';
    alamatTextarea.value = result;
}

function handlePhoneInput(input) {
    let val = input.value.replace(/[^0-9]/g, '');
    if (val.startsWith('62')) {
        val = val.substring(2);
    }
    if (val.startsWith('0')) {
        val = val.replace(/^0+/, '');
    }
    input.value = val.slice(0, 13);
    const hidden = document.getElementById('telepon');
    if (hidden) {
        hidden.value = val ? ('+62' + val) : '';
    }
}
</script>
@endsection
