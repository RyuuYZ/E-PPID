@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-5">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.surat-masuk.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            </a>
            <h2 class="text-lg font-bold text-gray-800 m-0">Detail Surat Masuk</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Informasi Surat -->
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white border border-gray-200 rounded shadow-sm p-6">
                <h3 class="text-[13px] font-bold text-gray-800 mb-4 border-b pb-2 uppercase tracking-wider">Informasi Utama</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Nomor Surat</span>
                        <span class="block text-[13px] font-semibold text-gray-900 mt-1">{{ $suratMasuk->nomor_surat }}</span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Asal/Pengirim</span>
                        <span class="block text-[13px] font-semibold text-gray-900 mt-1">{{ $suratMasuk->pengirim }}</span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Tanggal Surat</span>
                        <span class="block text-[13px] text-gray-800 mt-1">{{ \Carbon\Carbon::parse($suratMasuk->tanggal_surat)->translatedFormat('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Diterima Bappeda</span>
                        <span class="block text-[13px] text-gray-800 mt-1">{{ \Carbon\Carbon::parse($suratMasuk->tanggal_diterima)->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Perihal</span>
                        <p class="text-[13px] text-gray-800 mt-1.5 bg-gray-50 p-3 rounded border border-gray-200 leading-relaxed">{{ $suratMasuk->perihal }}</p>
                    </div>
                </div>

                @if($suratMasuk->file_lampiran)
                    <div class="mt-5 border-t border-gray-100 pt-4">
                        <a href="{{ Storage::url($suratMasuk->file_lampiran) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 font-semibold bg-blue-50 px-3 py-1.5 rounded transition-colors">
                            <span class="material-symbols-outlined text-[16px]">description</span>
                            Lihat Dokumen Terlampir
                        </a>
                    </div>
                @endif
            </div>

            <!-- Riwayat Disposisi -->
            <div class="bg-white border border-gray-200 rounded shadow-sm p-6">
                <h3 class="text-[13px] font-bold text-gray-800 mb-4 border-b pb-2 uppercase tracking-wider">Riwayat Disposisi</h3>
                @if($disposisis->count() > 0)
                    <div class="space-y-3">
                        @foreach($disposisis as $disposisi)
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Pemberi:</span>
                                        <span class="text-[11px] font-bold text-gray-900 ml-1">{{ $disposisi->pemberi_tugas->name ?? 'Sistem' }}</span>
                                    </div>
                                    <span class="text-[10px] font-medium text-gray-400">{{ $disposisi->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tujuan Bidang:</span>
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 ml-1 uppercase tracking-wider">
                                        {{ $disposisi->unit_pengolah->nama_bidang ?? 'Umum' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">Instruksi:</span>
                                    <p class="text-xs text-gray-800 leading-relaxed">{{ $disposisi->instruksi }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-500 italic">Belum ada disposisi untuk surat ini.</p>
                @endif
            </div>
        </div>

        <!-- Panel Disposisi (Atasan) -->
        <div class="lg:col-span-1">
            @if(auth()->user()->hasRole('Atasan PPID Pelaksana') || auth()->user()->hasRole('PPID Pelaksana'))
                <div class="bg-white border border-gray-200 rounded shadow-sm p-5 sticky top-5">
                    <h3 class="text-[13px] font-bold text-gray-800 mb-4 border-b pb-2 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">assignment_turned_in</span>
                        Beri Disposisi
                    </h3>
                    
                    <form action="{{ route('admin.surat-masuk.disposisi', $suratMasuk->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tujuan (Unit Pengolah) <span class="text-red-500">*</span></label>
                            <select name="unit_pengolah_id" data-placeholder="-- Pilih Bidang --" data-search-placeholder="Cari bidang / unit..." class="custom-select w-full text-xs font-medium" required>
                                <option value="">-- Pilih Bidang --</option>
                                @foreach($unitPengolahs as $up)
                                    <option value="{{ $up->id }}">{{ $up->nama_bidang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Instruksi / Catatan <span class="text-red-500">*</span></label>
                            <textarea name="instruksi" rows="4" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-gray-50" required placeholder="Contoh: Segera tindaklanjuti dan buatkan draf balasan."></textarea>
                        </div>
                        <button type="submit" class="w-full flex justify-center items-center gap-1.5 py-2 px-4 border border-transparent rounded shadow-sm text-xs font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors uppercase tracking-wider">
                            Kirim Disposisi
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
