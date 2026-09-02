@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.permohonan.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                </a>
                <h2 class="text-lg font-bold text-gray-800 m-0">Detail Permohonan Informasi</h2>
            </div>
            <p class="text-[11px] text-gray-500 ml-7">Nomor Registrasi: <span class="font-bold text-gray-700">{{ $permohonan->nomor_registrasi }}</span></p>
        </div>
        
        <div class="flex items-center">
            @php
                $slaColor = 'bg-gray-100 text-gray-600 border-gray-200';
                $slaText = 'Belum Dihitung';
                if ($permohonan->batas_waktu_jawaban) {
                    $jatuhTempo = \Carbon\Carbon::parse($permohonan->batas_waktu_jawaban);
                    $now = \Carbon\Carbon::now();
                    if ($permohonan->status->isTerminal()) {
                        $slaColor = 'bg-green-50 text-green-600 border-green-100';
                        $slaText = 'Selesai';
                    } elseif ($now->greaterThan($jatuhTempo)) {
                        $slaColor = 'bg-red-50 text-red-600 border-red-200';
                        $slaText = 'Melewati Batas (SLA Breach)';
                    } else {
                        $sisa = $now->diffInWeekdays($jatuhTempo); // Simplification for view
                        if ($sisa <= 2) {
                            $slaColor = 'bg-orange-50 text-orange-600 border-orange-200';
                        } else {
                            $slaColor = 'bg-blue-50 text-blue-600 border-blue-200';
                        }
                        $slaText = "Sisa $sisa Hari Kerja";
                    }
                }
            @endphp
            <div class="flex flex-col items-end gap-1">
                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider border {{ $permohonan->status->badgeClass() }} border-opacity-30">
                    {{ $permohonan->status->label() }}
                </span>
                @if($permohonan->batas_waktu_jawaban)
                <span class="inline-flex items-center px-2 py-1 rounded text-[9px] font-bold uppercase tracking-wider border {{ $slaColor }}" title="Batas Waktu: {{ \Carbon\Carbon::parse($permohonan->batas_waktu_jawaban)->format('d M Y') }}">
                    SLA: {{ $slaText }}
                </span>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-md shadow-sm" role="alert">
            <span class="font-medium">Berhasil!</span> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md shadow-sm" role="alert">
            <span class="font-medium">Gagal!</span> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-5">
            
            <!-- Profil Pemohon (Sembunyikan NIK/Alamat untuk Penghubung) -->
            <div class="bg-white border border-gray-200 rounded shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-[13px] font-bold text-gray-800 m-0 uppercase tracking-wider">Data Pemohon</h3>
                </div>
                <div class="p-0">
                    <table class="w-full text-[13px] text-left text-gray-600">
                        <tbody>
                            <tr class="border-b border-gray-100">
                                <th class="px-5 py-2.5 font-bold text-gray-500 uppercase tracking-wider bg-gray-50 w-1/3 text-[11px]">Nama / Instansi</th>
                                <td class="px-5 py-2.5 font-semibold text-gray-800">{{ $permohonan->nama_pemohon }}</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <th class="px-5 py-2.5 font-bold text-gray-500 uppercase tracking-wider bg-gray-50 text-[11px]">Kategori</th>
                                <td class="px-5 py-2.5 text-gray-700">{{ $permohonan->kategori_pemohon->nama_kategori ?? '-' }}</td>
                            </tr>
                            
                            @if(!auth()->user()->hasRole('Petugas Penghubung'))
                            <tr class="border-b border-gray-100">
                                <th class="px-5 py-2.5 font-bold text-gray-500 uppercase tracking-wider bg-gray-50 text-[11px]">NIK / No. Badan Hukum</th>
                                <td class="px-5 py-2.5 text-gray-700">{{ $permohonan->nik_atau_no_badan_hukum ?? '-' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <th class="px-5 py-2.5 font-bold text-gray-500 uppercase tracking-wider bg-gray-50 text-[11px]">Pekerjaan</th>
                                <td class="px-5 py-2.5 text-gray-700">{{ $permohonan->pekerjaan ?? '-' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <th class="px-5 py-2.5 font-bold text-gray-500 uppercase tracking-wider bg-gray-50 text-[11px]">Alamat</th>
                                <td class="px-5 py-2.5 text-gray-700">{{ $permohonan->alamat ?? '-' }}</td>
                            </tr>
                            @else
                            <tr class="border-b border-gray-100">
                                <td colspan="2" class="px-5 py-2.5 text-[11px] text-gray-400 italic text-center bg-gray-50">
                                    <span class="material-symbols-outlined text-[14px] align-middle mr-1">lock</span>
                                    Data identitas disembunyikan untuk Petugas Penghubung
                                </td>
                            </tr>
                            @endif
                            
                            <tr class="border-b border-gray-100">
                                <th class="px-5 py-2.5 font-bold text-gray-500 uppercase tracking-wider bg-gray-50 text-[11px]">Email</th>
                                <td class="px-5 py-2.5 text-gray-700">{{ $permohonan->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="px-5 py-2.5 font-bold text-gray-500 uppercase tracking-wider bg-gray-50 text-[11px]">No. Telepon</th>
                                <td class="px-5 py-2.5 text-gray-700">{{ $permohonan->no_telp ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rincian Permohonan -->
            <div class="bg-white border border-gray-200 rounded shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-[13px] font-bold text-gray-800 m-0 uppercase tracking-wider">Rincian Permohonan</h3>
                </div>
                <div class="p-5 space-y-5">
                    <div>
                        <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Informasi yang Diminta</h4>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded text-[13px] text-gray-700 whitespace-pre-line leading-relaxed">
                            {{ $permohonan->rincian_informasi }}
                        </div>
                    </div>
                    <div>
                        <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Tujuan Penggunaan Informasi</h4>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded text-[13px] text-gray-700 whitespace-pre-line leading-relaxed">
                            {{ $permohonan->tujuan_penggunaan }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-100">
                        <div>
                            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cara Memperoleh Informasi</span>
                            <span class="text-[13px] font-semibold text-gray-800">{{ $permohonan->cara_memperoleh_informasi->nama_cara ?? 'Melihat/Membaca' }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cara Mendapatkan Salinan</span>
                            <span class="text-[13px] font-semibold text-gray-800">{{ $permohonan->cara_mendapatkan_salinan ?? 'Softcopy (Email)' }}</span>
                        </div>
                    </div>
                    
                    @if(!auth()->user()->hasRole('Petugas Penghubung'))
                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Berkas Lampiran Identitas</h4>
                        @if($permohonan->file_identitas)
                        <a href="{{ Storage::url($permohonan->file_identitas) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">description</span>
                            Lihat Dokumen
                        </a>
                        @else
                        <div class="text-[12px] text-gray-400 italic bg-gray-50 p-3 rounded border border-gray-100">
                            Tidak ada berkas lampiran yang disertakan oleh pemohon.
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Penugasan Unit Pengolah -->
            @if(in_array($permohonan->status->value, ['ditugaskan', 'menunggu_data', 'data_diuji', 'menunggu_tanda_tangan', 'ditandatangani', 'selesai']))
            <div class="bg-white border border-gray-200 rounded shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-[13px] font-bold text-gray-800 m-0 uppercase tracking-wider">Penugasan Unit Pengolah</h3>
                </div>
                <div class="p-0">
                    <table class="w-full text-left border-collapse text-sm text-gray-600">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-[10px] uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-3 font-bold">Unit Pengolah</th>
                                <th class="px-5 py-3 font-bold">Petugas</th>
                                <th class="px-5 py-3 font-bold">Status</th>
                                <th class="px-5 py-3 font-bold">Data/Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($permohonan->penugasan as $tugas)
                            <tr>
                                <td class="px-5 py-4 font-semibold text-gray-800">{{ $tugas->unitPengolah->nama_bidang }}</td>
                                <td class="px-5 py-4">{{ $tugas->petugasPenghubung->name }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase border {{ $tugas->status->badgeClass() }} border-opacity-30">
                                        {{ $tugas->status->label() }}
                                    </span>
                                    @if($tugas->hasil_uji !== \App\Enums\HasilUji::Pending)
                                    <br>
                                    <span class="inline-flex items-center px-2 py-1 mt-1 rounded-md text-[10px] font-bold uppercase border {{ $tugas->hasil_uji->badgeClass() }} border-opacity-30">
                                        {{ $tugas->hasil_uji->label() }}
                                    </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <!-- Aksi untuk Petugas Penghubung submit data -->
                                    @if(auth()->user()->id === $tugas->petugas_penghubung_id && $tugas->status === \App\Enums\PenugasanStatus::Ditugaskan)
                                    <form action="{{ route('admin.penugasan.submit-data', $tugas->id) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                        @csrf
                                        <input type="file" name="data_file" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                                        <textarea name="catatan" rows="1" class="w-full text-xs p-1 border rounded" placeholder="Catatan..."></textarea>
                                        <button type="submit" class="bg-blue-600 text-white text-[10px] px-2 py-1 rounded uppercase font-bold tracking-wider">Kirim Data</button>
                                    </form>
                                    @elseif($tugas->data_path)
                                    <!-- Link download data untuk PPID/Atasan -->
                                    <a href="{{ Storage::url($tugas->data_path) }}" target="_blank" class="text-blue-600 text-xs font-semibold hover:underline flex items-center gap-1 mb-2">
                                        <span class="material-symbols-outlined text-[14px]">download</span> Unduh Data
                                    </a>
                                        <!-- Aksi PPID review data -->
                                        @if(auth()->user()->hasRole('PPID Pelaksana') && $permohonan->status === \App\Enums\PermohonanStatus::DataDiuji && $tugas->hasil_uji === \App\Enums\HasilUji::Pending)
                                        <form action="{{ route('admin.penugasan.review', $tugas->id) }}" method="POST" class="mt-2 pt-2 border-t border-gray-100 flex gap-2">
                                            @csrf
                                            <button type="submit" name="hasil_uji" value="sesuai" class="bg-green-100 text-green-700 text-[10px] px-2 py-1 rounded uppercase font-bold border border-green-200">Sesuai</button>
                                            <button type="submit" name="hasil_uji" value="perlu_revisi" class="bg-red-100 text-red-700 text-[10px] px-2 py-1 rounded uppercase font-bold border border-red-200">Revisi</button>
                                        </form>
                                        @endif
                                    @else
                                    <span class="text-xs text-gray-400 italic">Belum ada data</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-4 text-center text-sm text-gray-500">Belum ada penugasan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Action Panel & Timeline -->
        <div class="space-y-5">
            
            <!-- Action Panel / State Machine Controls -->
            <div class="bg-white border border-gray-200 rounded shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wider m-0">Tindak Lanjut</h3>
                    @if($permohonan->status === \App\Enums\PermohonanStatus::DataDiuji && auth()->user()->hasRole('PPID Pelaksana') && !$permohonan->diperpanjang)
                    <form action="{{ route('admin.permohonan.extend-deadline', $permohonan->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Perpanjang waktu jawaban 7 hari kerja?')" class="text-[10px] bg-white border border-gray-300 text-gray-600 px-2 py-1 rounded hover:bg-gray-50 font-bold uppercase">
                            +7 Hari
                        </button>
                    </form>
                    @endif
                </div>
                
                <div class="p-5">
                    <!-- Desk Layanan: Verifikasi Masuk -->
                    @if($permohonan->status === \App\Enums\PermohonanStatus::Diajukan || $permohonan->status === \App\Enums\PermohonanStatus::MenungguKelengkapan)
                        @if(auth()->user()->hasRole('Desk Layanan'))
                            <form action="{{ route('admin.permohonan.update-status', $permohonan->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Aksi Verifikasi:</label>
                                    <select name="target_status" id="verifikasi_action" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" onchange="document.getElementById('catatan_tak_lengkap').style.display = this.value === 'menunggu_kelengkapan' ? 'block' : 'none'">
                                        <option value="diverifikasi">Berkas Lengkap (Diverifikasi)</option>
                                        <option value="menunggu_kelengkapan">Berkas Tidak Lengkap</option>
                                    </select>
                                </div>
                                <div id="catatan_tak_lengkap" class="mb-4" style="display: none;">
                                    <label class="block text-[11px] font-bold text-red-700 uppercase tracking-wider mb-2">Kekurangan Berkas:</label>
                                    <textarea name="alasan_tidak_lengkap" rows="3" class="w-full text-xs px-3 py-2 border border-red-300 rounded bg-red-50" placeholder="Jelaskan berkas apa yang kurang..."></textarea>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white font-semibold rounded py-2 px-4 hover:bg-blue-700 text-[11px] uppercase tracking-wider">
                                    Proses Verifikasi
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-50 p-4 rounded text-xs text-center text-gray-600 border border-gray-200">
                                Menunggu verifikasi berkas oleh <strong>Desk Layanan</strong>.
                            </div>
                        @endif

                    <!-- PPID Pelaksana: Penugasan Multi-Unit -->
                    @elseif($permohonan->status === \App\Enums\PermohonanStatus::Diverifikasi)
                        @if(auth()->user()->hasRole('PPID Pelaksana'))
                            <form action="{{ route('admin.permohonan.assign', $permohonan->id) }}" method="POST" id="assignForm">
                                @csrf
                                <div class="mb-3 flex justify-between items-center">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider m-0">Tugaskan ke Unit:</label>
                                    <button type="button" onclick="addAssignRow()" class="text-[10px] bg-green-100 text-green-700 border border-green-200 px-2 py-0.5 rounded uppercase font-bold">+ Tambah</button>
                                </div>
                                
                                <div id="assignContainer" class="space-y-3 mb-4">
                                    <div class="p-3 bg-gray-50 border border-gray-200 rounded text-xs assign-row">
                                        <select name="assignments[0][unit_pengolah_id]" class="w-full mb-2 p-1.5 border rounded" required>
                                            <option value="">Pilih Unit/Bidang...</option>
                                            @foreach($unitPengolahs as $up)
                                            <option value="{{ $up->id }}">{{ $up->nama_bidang }}</option>
                                            @endforeach
                                        </select>
                                        <select name="assignments[0][petugas_penghubung_id]" class="w-full mb-2 p-1.5 border rounded" required>
                                            <option value="">Pilih Petugas Penghubung...</option>
                                            @foreach($petugasPenghubungs as $petugas)
                                            <option value="{{ $petugas->id }}">{{ $petugas->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="assignments[0][instruksi]" placeholder="Instruksi spesifik (opsional)" class="w-full p-1.5 border rounded">
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white font-semibold rounded py-2 px-4 hover:bg-blue-700 text-[11px] uppercase tracking-wider">
                                    Minta Data
                                </button>
                            </form>
                            
                            <script>
                                let assignIdx = 1;
                                function addAssignRow() {
                                    const template = document.querySelector('.assign-row').cloneNode(true);
                                    template.innerHTML = template.innerHTML.replace(/assignments\[0\]/g, `assignments[${assignIdx}]`);
                                    template.querySelectorAll('input, select').forEach(el => el.value = '');
                                    document.getElementById('assignContainer').appendChild(template);
                                    assignIdx++;
                                }
                            </script>
                        @else
                            <div class="bg-gray-50 p-4 rounded text-xs text-center text-gray-600 border border-gray-200">
                                Menunggu <strong>PPID Pelaksana</strong> mendisposisikan tugas pencarian data.
                            </div>
                        @endif

                    <!-- PPID Pelaksana: Susun Jawaban (Setelah Semua Data Sesuai) -->
                    @elseif($permohonan->status === \App\Enums\PermohonanStatus::DataDiuji)
                        @if(auth()->user()->hasRole('PPID Pelaksana'))
                            @if($permohonan->allPenugasanSesuai())
                            <form action="{{ route('admin.permohonan.update-status', $permohonan->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="target_status" value="menunggu_tanda_tangan">
                                <div class="mb-4">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Draf Jawaban (Opsional Path):</label>
                                    <input type="text" name="surat_jawaban_path" class="w-full text-xs px-3 py-2 border rounded" placeholder="/storage/draf/jawaban.pdf">
                                    <p class="text-[10px] text-gray-500 mt-1">Semua data telah valid. Lanjutkan ke Atasan untuk TTE.</p>
                                </div>
                                <button type="submit" class="w-full bg-purple-600 text-white font-semibold rounded py-2 px-4 hover:bg-purple-700 text-[11px] uppercase tracking-wider">
                                    Ajukan ke Atasan
                                </button>
                            </form>
                            @else
                            <div class="bg-yellow-50 p-4 rounded text-xs text-center text-yellow-700 border border-yellow-200">
                                Anda harus menguji dan menandai <strong>Semua Penugasan = Sesuai</strong> di tabel sebelah kiri sebelum dapat menyusun draf jawaban.
                            </div>
                            @endif
                        @else
                            <div class="bg-gray-50 p-4 rounded text-xs text-center text-gray-600 border border-gray-200">
                                PPID Pelaksana sedang memvalidasi data dan menyusun konsep jawaban.
                            </div>
                        @endif

                    <!-- Atasan PPID: TTE -->
                    @elseif($permohonan->status === \App\Enums\PermohonanStatus::MenungguTandaTangan)
                        @if(auth()->user()->hasRole('Atasan PPID Pelaksana'))
                            <form action="{{ route('admin.permohonan.update-status', $permohonan->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="target_status" value="ditandatangani">
                                <div class="mb-4 text-center">
                                    <span class="material-symbols-outlined text-4xl text-purple-600 mb-2">draw</span>
                                    <p class="text-xs text-gray-600">Draf jawaban telah disiapkan. Bubuhkan TTE (Tanda Tangan Elektronik) untuk menyetujui surat jawaban final.</p>
                                </div>
                                <button type="submit" class="w-full bg-green-600 text-white font-semibold rounded py-2 px-4 hover:bg-green-700 text-[11px] uppercase tracking-wider">
                                    Tanda Tangani Jawaban
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-50 p-4 rounded text-xs text-center text-gray-600 border border-gray-200">
                                Menunggu persetujuan dan TTE dari <strong>Atasan PPID</strong>.
                            </div>
                        @endif

                    <!-- Desk Layanan: Kirim Jawaban Final -->
                    @elseif($permohonan->status === \App\Enums\PermohonanStatus::Ditandatangani)
                        @if(auth()->user()->hasRole('Desk Layanan'))
                            <form action="{{ route('admin.permohonan.update-status', $permohonan->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="target_status" value="selesai">
                                <div class="mb-4 text-center">
                                    <span class="material-symbols-outlined text-4xl text-green-600 mb-2">mark_email_read</span>
                                    <p class="text-xs text-gray-600">Surat jawaban telah ditandatangani oleh Atasan. Kirimkan surat jawaban ke portal pemohon sekarang.</p>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white font-semibold rounded py-2 px-4 hover:bg-blue-700 text-[11px] uppercase tracking-wider">
                                    Kirim ke Pemohon & Selesai
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-50 p-4 rounded text-xs text-center text-gray-600 border border-gray-200">
                                Surat ditandatangani. Menunggu Desk Layanan mengirimkan ke pemohon.
                            </div>
                        @endif
                        
                    <!-- Terminal State -->
                    @else
                        <div class="text-center py-4">
                            <span class="material-symbols-outlined {{ $permohonan->status === \App\Enums\PermohonanStatus::Selesai ? 'text-green-500' : 'text-red-500' }} text-4xl mb-2">
                                {{ $permohonan->status === \App\Enums\PermohonanStatus::Selesai ? 'task_alt' : 'cancel' }}
                            </span>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Permohonan {{ $permohonan->status->label() }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="bg-white border border-gray-200 rounded shadow-sm p-5">
                <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wider mb-5">Log Aktivitas</h3>
                
                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                    @forelse($permohonan->logs as $log)
                        <div class="flex gap-3 text-sm">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <span class="material-symbols-outlined text-[16px]">history</span>
                                </div>
                            </div>
                            <div class="flex-1 bg-gray-50 border border-gray-100 p-3 rounded">
                                <div class="flex justify-between mb-1">
                                    <span class="font-bold text-gray-800 text-[11px] uppercase tracking-wider">{{ $log->aksi }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="text-[12px] text-gray-600">
                                    <span class="font-semibold text-gray-700">{{ $log->user->name ?? 'Sistem' }}</span> 
                                    @if($log->catatan)
                                        <p class="mt-1 text-gray-500 italic bg-white p-2 border border-gray-200 rounded">"{{ $log->catatan }}"</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-4 bg-gray-50 text-gray-500 text-xs rounded border border-gray-100">
                            Belum ada aktivitas tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</main>
@endsection
