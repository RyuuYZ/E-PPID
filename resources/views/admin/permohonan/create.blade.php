@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">Tambah Permohonan (Walk-in)</h2>
            <p class="text-xs text-gray-500 mt-0.5">Buat permohonan baru untuk pemohon yang datang langsung ke kantor.</p>
        </div>
        <a href="{{ route('admin.permohonan.index', ['status' => 'diajukan']) }}" class="inline-flex items-center gap-1.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded px-3 py-1.5 hover:bg-gray-50 transition-colors shadow-sm text-xs">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded shadow-sm text-sm">
            <strong class="font-bold">Terjadi Kesalahan!</strong>
            <ul class="mt-2 list-disc list-inside text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded shadow-sm p-6">
        <form action="{{ route('admin.permohonan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Data Diri Pemohon -->
            <div>
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                    <span class="material-symbols-outlined text-[#1a2b42] text-[20px]">person</span>
                    <h3 class="text-base font-bold text-gray-800 m-0">Data Diri Pemohon</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nama Lengkap -->
                    <div class="flex flex-col gap-1.5 md:col-span-2">
                        <label for="namaLengkap" class="text-xs font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="namaLengkap" name="nama_pemohon" value="{{ old('nama_pemohon') }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required placeholder="Masukkan nama lengkap sesuai identitas">
                    </div>
                    
                    <!-- NIK -->
                    <div class="flex flex-col gap-1.5">
                        <label for="nik" class="text-xs font-semibold text-gray-700">NIK / No. Identitas <span class="text-red-500">*</span></label>
                        <input type="text" id="nik" name="nik_atau_no_badan_hukum" value="{{ old('nik_atau_no_badan_hukum') }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required placeholder="16 digit NIK atau Nomor Badan Hukum">
                    </div>
                    
                    <!-- Kategori Pemohon -->
                    <div class="flex flex-col gap-1.5">
                        <label for="kategori_pemohon" class="text-xs font-semibold text-gray-700">Kategori Pemohon <span class="text-red-500">*</span></label>
                        <select id="kategori_pemohon" name="kategori_pemohon_id" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3 bg-white" required>
                            <option value="" disabled selected>Pilih Kategori Pemohon...</option>
                            @foreach($kategoriPemohons as $kp)
                                <option value="{{ $kp->id }}" {{ old('kategori_pemohon_id') == $kp->id ? 'selected' : '' }}>{{ $kp->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- No. Telepon -->
                    <div class="flex flex-col gap-1.5">
                        <label for="telepon" class="text-xs font-semibold text-gray-700">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                        <input type="tel" id="telepon" name="no_telp" value="{{ old('no_telp') }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required placeholder="08xxxxxxxxxx">
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-xs font-semibold text-gray-700">Alamat Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required placeholder="email@contoh.com">
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="flex flex-col gap-1.5 md:col-span-2">
                        <label for="alamat" class="text-xs font-semibold text-gray-700">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea id="alamat" name="alamat" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow p-3 resize-none" required rows="3" placeholder="Masukkan alamat lengkap (Jalan, RT/RW, Desa/Kelurahan, Kecamatan)">{{ old('alamat') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Rincian Informasi yang Dibutuhkan -->
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                    <span class="material-symbols-outlined text-[#1a2b42] text-[20px]">description</span>
                    <h3 class="text-base font-bold text-gray-800 m-0">Rincian Informasi yang Dibutuhkan</h3>
                </div>
                
                <div class="flex flex-col gap-5">
                    <!-- Subjek Informasi -->
                    <div class="flex flex-col gap-1.5">
                        <label for="subjek" class="text-xs font-semibold text-gray-700">Subjek Informasi <span class="text-red-500">*</span></label>
                        <input type="text" id="subjek" name="subjek" value="{{ old('subjek') }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required placeholder="Garis besar informasi yang diminta (Cth: Dokumen APBD 2023)">
                    </div>

                    <!-- Deskripsi Detail -->
                    <div class="flex flex-col gap-1.5">
                        <label for="deskripsi" class="text-xs font-semibold text-gray-700">Deskripsi Detail Informasi <span class="text-red-500">*</span></label>
                        <textarea id="deskripsi" name="rincian_informasi" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow p-3 resize-none" required rows="4" placeholder="Jelaskan secara rinci dokumen atau informasi yang Anda butuhkan...">{{ old('rincian_informasi') }}</textarea>
                    </div>

                    <!-- Tujuan Penggunaan -->
                    <div class="flex flex-col gap-1.5">
                        <label for="tujuan" class="text-xs font-semibold text-gray-700">Tujuan Penggunaan Informasi <span class="text-red-500">*</span></label>
                        <textarea id="tujuan" name="tujuan_penggunaan" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow p-3 resize-none" required rows="3" placeholder="Jelaskan untuk apa informasi ini akan digunakan (Cth: Penelitian Akademis)">{{ old('tujuan_penggunaan') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Cara Memperoleh Informasi -->
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                    <span class="material-symbols-outlined text-[#1a2b42] text-[20px]">inventory_2</span>
                    <h3 class="text-base font-bold text-gray-800 m-0">Cara Memperoleh Informasi</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($caraMemperoleh as $cara)
                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none 
                        {{ old('cara_memperoleh_informasi_id') == $cara->id ? 'border-[#1a2b42] ring-1 ring-[#1a2b42]' : 'border-gray-200 hover:bg-gray-50' }}">
                        <input type="radio" name="cara_memperoleh_informasi_id" value="{{ $cara->id }}" class="sr-only" required {{ old('cara_memperoleh_informasi_id') == $cara->id ? 'checked' : '' }} onchange="updateCaraUI(this)">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">{{ $cara->nama_cara }}</span>
                                <span class="mt-1 flex items-center text-xs text-gray-500">{{ $cara->deskripsi }}</span>
                            </span>
                        </span>
                        <span class="pointer-events-none absolute -inset-px rounded-lg border-2 border-transparent" aria-hidden="true"></span>
                    </label>
                    @endforeach
                </div>
                <script>
                    function updateCaraUI(radio) {
                        document.querySelectorAll('input[name="cara_memperoleh_informasi_id"]').forEach((input) => {
                            const label = input.closest('label');
                            label.classList.remove('border-[#1a2b42]', 'ring-1', 'ring-[#1a2b42]');
                            label.classList.add('border-gray-200', 'hover:bg-gray-50');
                        });
                        if (radio.checked) {
                            const selectedLabel = radio.closest('label');
                            selectedLabel.classList.remove('border-gray-200', 'hover:bg-gray-50');
                            selectedLabel.classList.add('border-[#1a2b42]', 'ring-1', 'ring-[#1a2b42]');
                        }
                    }
                </script>
            </div>

            <!-- Section 4: Unggah Dokumen Pendukung -->
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                    <span class="material-symbols-outlined text-[#1a2b42] text-[20px]">upload_file</span>
                    <h3 class="text-base font-bold text-gray-800 m-0">Unggah Dokumen Pendukung</h3>
                </div>
                
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-gray-700">KTP / Identitas Diri Resmi <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center text-center bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="material-symbols-outlined text-[32px] text-gray-400 mb-2">cloud_upload</span>
                        <p class="text-xs text-gray-500 mb-3">Format didukung: JPG, PNG, PDF (Maks. 5MB).</p>
                        <input accept=".jpg,.jpeg,.png,.pdf" class="block w-full max-w-xs text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#1a2b42] file:text-white hover:file:bg-[#121c2e] transition-colors cursor-pointer" id="dokumenKtp" name="file_identitas" required type="file">
                    </div>
                </div>
            </div>

            <div class="pt-6 flex justify-end gap-3 border-t border-gray-100 mt-6">
                <a href="{{ route('admin.permohonan.index', ['status' => 'diajukan']) }}" class="inline-flex justify-center rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#1a2b42] focus:ring-offset-2 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center rounded bg-[#1a2b42] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#121c2e] focus:outline-none focus:ring-2 focus:ring-[#1a2b42] focus:ring-offset-2 transition-colors">
                    Simpan Permohonan Baru
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
