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
                $statusColors = [
                    'Diterima' => 'bg-blue-50 text-blue-600 border-blue-100',
                    'Diverifikasi' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                    'Ditugaskan' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                    'Diuji' => 'bg-teal-50 text-teal-600 border-teal-100',
                    'Menunggu TTE' => 'bg-purple-50 text-purple-600 border-purple-100',
                    'Selesai' => 'bg-green-50 text-green-600 border-green-100',
                    'Ditutup' => 'bg-red-50 text-red-600 border-red-100',
                ];
                $colorClass = $statusColors[$permohonan->tahapan_proses] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                $statusLabel = $permohonan->tahapan_proses;
                
                $slaColor = 'bg-gray-100 text-gray-600 border-gray-200';
                $slaText = 'Belum Dihitung';
                if ($permohonan->tanggal_jatuh_tempo) {
                    $jatuhTempo = \Carbon\Carbon::parse($permohonan->tanggal_jatuh_tempo);
                    $now = \Carbon\Carbon::now();
                    if ($permohonan->status == 'selesai' || $permohonan->status == 'ditolak' || $permohonan->status == 'ditutup') {
                        $slaColor = 'bg-green-50 text-green-600 border-green-100';
                        $slaText = 'Selesai';
                    } elseif ($now->greaterThan($jatuhTempo)) {
                        $slaColor = 'bg-red-50 text-red-600 border-red-200';
                        $slaText = 'Melewati Batas (SLA Breach)';
                    } else {
                        $sisa = $now->diffInDays($jatuhTempo);
                        if ($sisa <= 2) {
                            $slaColor = 'bg-orange-50 text-orange-600 border-orange-200';
                        } else {
                            $slaColor = 'bg-blue-50 text-blue-600 border-blue-200';
                        }
                        $slaText = "Sisa $sisa Hari";
                    }
                }
            @endphp
            <div class="flex flex-col items-end gap-1">
                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider border {{ $colorClass }}">
                    {{ $statusLabel }}
                </span>
                @if($permohonan->tanggal_jatuh_tempo)
                <span class="inline-flex items-center px-2 py-1 rounded text-[9px] font-bold uppercase tracking-wider border {{ $slaColor }}" title="Jatuh Tempo: {{ \Carbon\Carbon::parse($permohonan->tanggal_jatuh_tempo)->format('d M Y') }}">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-5">
            
            <!-- Profil Pemohon -->
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
                </div>
            </div>

        </div>

        <!-- Right Column: Action Panel & Timeline -->
        <div class="space-y-5">
            
            <!-- Action Panel -->
            <div class="bg-white border border-gray-200 rounded shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wider m-0">Tindak Lanjut (Pelaksana)</h3>
                </div>
                
                <div class="p-5">
                    <form action="{{ route('admin.permohonan.update-status', $permohonan->id) }}" method="POST">
                        @csrf
                        
                        @if($permohonan->tahapan_proses == 'Diterima')
                            <!-- Action: Verifikasi oleh Desk Layanan -->
                            <!-- Action: Verifikasi oleh Desk Layanan -->
                            @if(auth()->user()->hasRole('Desk Layanan'))
                                <div x-data="{ action: 'verifikasi' }">
                                    <div class="flex gap-4 border-b border-gray-200 mb-4 pb-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" x-model="action" value="verifikasi" class="text-blue-600 focus:ring-blue-500">
                                            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Lanjut Proses</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" x-model="action" value="tolak" class="text-red-600 focus:ring-red-500">
                                            <span class="text-xs font-bold text-red-600 uppercase tracking-wider">Tolak Permohonan</span>
                                        </label>
                                    </div>

                                    <!-- Verifikasi Form -->
                                    <div x-show="action === 'verifikasi'">
                                        <div class="mb-4">
                                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Catatan Kelengkapan:</label>
                                            <textarea name="catatan_verifikasi" rows="3" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-gray-50" placeholder="Catatan jika berkas tidak lengkap..."></textarea>
                                        </div>
                                        <button type="submit" name="tahapan_proses" value="Diverifikasi" class="w-full bg-blue-600 text-white font-semibold rounded py-2 px-4 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-[11px] transition-colors uppercase tracking-wider cursor-pointer">
                                            Verifikasi Kelengkapan (Lanjut)
                                        </button>
                                    </div>

                                    <!-- Tolak Form -->
                                    <div x-show="action === 'tolak'" style="display: none;">
                                        <div class="mb-4">
                                            <label class="block text-[11px] font-bold text-red-700 uppercase tracking-wider mb-2">Alasan Penolakan Otomatis:</label>
                                            <select name="alasan_penolakan" class="w-full text-xs px-3 py-2 border border-red-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors bg-red-50 mb-2">
                                                <option value="">-- Pilih Alasan Penolakan --</option>
                                                <option value="Tujuan tidak jelas: Permintaan informasi diajukan untuk kepentingan atau alasan yang tidak jelas.">Tujuan Tidak Jelas</option>
                                                <option value="Informasi dikecualikan: Informasi yang diminta masuk dalam kategori rahasia atau dikecualikan berdasarkan UU No. 14 Tahun 2008.">Informasi Dikecualikan</option>
                                                <option value="Informasi belum dikuasai: Badan publik yang dituju belum menguasai, mendokumentasikan, atau menyimpan informasi yang diminta.">Informasi Belum Dikuasai</option>
                                                <option value="Tidak sesuai prosedur: Permintaan tidak mengikuti ketentuan atau prosedur operasional yang diatur dalam perundang-undangan.">Tidak Sesuai Prosedur</option>
                                                <option value="Lainnya">Alasan Lainnya (Tulis Manual)</option>
                                            </select>
                                            <textarea name="alasan_manual" rows="2" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors bg-gray-50 mt-2" placeholder="Catatan tambahan / alasan lainnya..."></textarea>
                                        </div>
                                        <button type="submit" name="tahapan_proses" value="Ditolak" class="w-full bg-red-600 text-white font-semibold rounded py-2 px-4 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-[11px] transition-colors uppercase tracking-wider cursor-pointer">
                                            Tolak Permohonan
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 p-4 rounded text-xs text-gray-600 mb-4 text-center">
                                    Menunggu verifikasi kelengkapan berkas oleh <strong>Petugas Desk Layanan</strong>.
                                </div>
                            @endif

                        @elseif($permohonan->tahapan_proses == 'Diverifikasi')
                            <!-- Action: Kirim ke Petugas Penghubung -->
                            @if(auth()->user()->hasRole('PPID Pelaksana'))
                                <div class="mb-4">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Tugaskan ke Unit Pengolah (Bidang):</label>
                                    <select name="unit_pengolah_id" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-gray-50" required>
                                        <option value="">-- Pilih Bidang Terkait --</option>
                                        @foreach($unitPengolahs as $up)
                                            <option value="{{ $up->id }}">{{ $up->nama_bidang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="tahapan_proses" value="Ditugaskan">
                                <button type="submit" class="w-full bg-blue-600 text-white font-semibold rounded py-2 px-4 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-[11px] transition-colors uppercase tracking-wider">
                                    Minta Data ke Penghubung
                                </button>
                            @else
                                <div class="bg-gray-50 border border-gray-200 p-4 rounded text-xs text-gray-600 mb-4 text-center">
                                    Menunggu <strong>PPID Pelaksana</strong> menugaskan Unit Pengolah Data.
                                </div>
                            @endif
                            
                        @elseif($permohonan->tahapan_proses == 'Ditugaskan')
                            <!-- Action: Waiting Data -->
                            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded text-xs text-yellow-800 mb-4">
                                Menunggu petugas penghubung menyiapkan data yang diminta.
                            </div>
                            @if(auth()->user()->hasRole('Petugas Penghubung') || auth()->user()->hasRole('PPID Pelaksana'))
                                <input type="hidden" name="tahapan_proses" value="Diuji">
                                <button type="submit" class="w-full bg-white text-gray-700 font-semibold rounded py-2 px-4 border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-[11px] transition-colors uppercase tracking-wider">
                                    Simulasi: Data Telah Diterima (Uji)
                                </button>
                            @endif

                        @elseif($permohonan->tahapan_proses == 'Diuji')
                            <!-- Action: Validasi & Teruskan ke Atasan -->
                            @if(auth()->user()->hasRole('PPID Pelaksana'))
                                <div class="mb-4">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Catatan Validasi Konsep Jawaban:</label>
                                    <textarea name="catatan" rows="3" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-gray-50" placeholder="Tulis catatan (Opsional)..."></textarea>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" name="tahapan_proses" value="Menunggu TTE" class="flex-1 bg-purple-600 text-white font-semibold rounded py-2 px-2 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 text-[10px] sm:text-[11px] transition-colors uppercase tracking-wider text-center">
                                        Validasi & Teruskan ke Atasan
                                    </button>
                                    <button type="button" onclick="document.getElementById('revisiModal').classList.remove('hidden')" class="bg-red-50 text-red-600 border border-red-200 font-semibold rounded py-2 px-2 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-[10px] sm:text-[11px] transition-colors uppercase tracking-wider text-center">
                                        Kembalikan ke Penghubung
                                    </button>
                                </div>
                                
                                <!-- Modal Revisi -->
                                <div id="revisiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                        <div class="mt-3 text-center">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900">Kembalikan ke Petugas Penghubung</h3>
                                            <div class="mt-2 px-2 py-3 text-left">
                                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Catatan Revisi:</label>
                                                <textarea name="catatan_revisi" rows="3" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors bg-gray-50" placeholder="Tulis alasan kenapa data dikembalikan..."></textarea>
                                            </div>
                                            <div class="items-center px-4 py-3 flex gap-2">
                                                <button type="button" onclick="document.getElementById('revisiModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded hover:bg-gray-200 w-full">Batal</button>
                                                <button type="submit" name="tahapan_proses" value="Ditugaskan" class="px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 w-full uppercase tracking-wider">Kembalikan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 p-4 rounded text-xs text-gray-600 mb-4 text-center">
                                    Menunggu <strong>PPID Pelaksana</strong> menguji data dan menyusun konsep jawaban.
                                </div>
                            @endif

                        @elseif($permohonan->tahapan_proses == 'Menunggu TTE')
                            <!-- Action: TTE Atasan -->
                            @if(auth()->user()->hasRole('Atasan PPID Pelaksana'))
                                <div class="mb-4">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Persetujuan Jawaban Final:</label>
                                    <p class="text-[11px] text-gray-500 mb-2">Bubuhkan tanda tangan elektronik untuk mengirimkan jawaban.</p>
                                </div>
                                <input type="hidden" name="tahapan_proses" value="Selesai">
                                <button type="submit" class="w-full bg-green-600 text-white font-semibold rounded py-2 px-4 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 text-[11px] transition-colors uppercase tracking-wider">
                                    Setujui & Tanda Tangani
                                </button>
                            @else
                                <div class="bg-gray-50 border border-gray-200 p-4 rounded text-xs text-gray-600 mb-4 text-center">
                                    Menunggu Tanda Tangan Elektronik (TTE) dari <strong>Atasan PPID Pelaksana</strong>.
                                </div>
                            @endif
                            
                        @else
                            <!-- Selesai / Ditutup -->
                            <div class="text-center py-4">
                                <span class="material-symbols-outlined {{ $permohonan->status == 'ditolak' ? 'text-red-500' : 'text-green-500' }} text-4xl mb-2">
                                    {{ $permohonan->status == 'ditolak' ? 'cancel' : 'check_circle' }}
                                </span>
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Permohonan {{ $permohonan->status }}</p>
                                
                                @if(in_array($permohonan->status, ['selesai', 'ditolak', 'ditutup']))
                                    @if(!$permohonan->keberatan)
                                        <button type="button" onclick="document.getElementById('keberatanModal').classList.remove('hidden')" class="mt-4 px-4 py-2 bg-red-600 text-white text-[11px] font-semibold rounded hover:bg-red-700 transition-colors uppercase tracking-wider inline-flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[16px]">gavel</span> Ajukan Keberatan
                                        </button>
                                    @else
                                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700">
                                            Sengketa / Keberatan telah diajukan.<br>
                                            <a href="{{ route('admin.keberatan.show', $permohonan->keberatan->id) }}" class="font-bold hover:underline mt-1 inline-block">Lihat Detail Keberatan</a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Modal Ajukan Keberatan -->
            @if(in_array($permohonan->status, ['selesai', 'ditolak', 'ditutup']) && !$permohonan->keberatan)
            <div id="keberatanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <form action="{{ route('admin.keberatan.store', $permohonan->id) }}" method="POST">
                        @csrf
                        <div class="mt-3 text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <span class="material-symbols-outlined text-red-600">gavel</span>
                            </div>
                            <h3 class="text-lg leading-6 font-bold text-gray-900">Form Pengajuan Keberatan</h3>
                            
                            <div class="mt-4 px-2 text-left space-y-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Alasan Keberatan:</label>
                                    <select name="alasan_keberatan" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors bg-gray-50" required>
                                        <option value="">-- Pilih Alasan Utama --</option>
                                        <option value="Permohonan Informasi ditolak">Permohonan Informasi ditolak</option>
                                        <option value="Informasi yang diberikan tidak lengkap">Informasi yang diberikan tidak lengkap</option>
                                        <option value="Informasi tidak sesuai dengan yang diminta">Informasi tidak sesuai dengan yang diminta</option>
                                        <option value="Permintaan informasi tidak ditanggapi (Lewat SLA)">Permintaan informasi tidak ditanggapi (Lewat SLA)</option>
                                        <option value="Biaya yang dikenakan tidak wajar">Biaya yang dikenakan tidak wajar</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Keterangan Tambahan:</label>
                                    <textarea name="keterangan_tambahan" rows="3" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors bg-gray-50" placeholder="Tulis rincian keluhan pemohon..."></textarea>
                                </div>
                            </div>
                            
                            <div class="mt-5 sm:mt-6 flex gap-3">
                                <button type="button" onclick="document.getElementById('keberatanModal').classList.add('hidden')" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Batal
                                </button>
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-xs font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Ajukan Keberatan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Timeline Tracker -->
            <div class="bg-white border border-gray-200 rounded shadow-sm p-5 mb-5">
                <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wider mb-5">Jejak Proses</h3>
                
                <ol class="relative border-l border-gray-200 ml-3 space-y-6">
                    <!-- Step 1 -->
                    <li class="mb-6 ml-6">
                        <span class="absolute flex items-center justify-center w-3 h-3 bg-green-500 rounded-full -left-[6.5px] ring-4 ring-white"></span>
                        <h4 class="text-[11px] font-bold text-gray-900 uppercase tracking-wider">Permohonan Masuk</h4>
                        <time class="block text-[10px] font-medium text-gray-400 mt-1">{{ $permohonan->created_at->format('d M Y H:i') }}</time>
                    </li>

                    <!-- Step 2 -->
                    <li class="mb-6 ml-6">
                        @php
                            $step2_done = in_array($permohonan->tahapan_proses, ['Diverifikasi', 'Ditugaskan', 'Diuji', 'Menunggu TTE', 'Selesai', 'Ditutup']);
                            $step2_active = ($permohonan->tahapan_proses == 'Diterima');
                            $step2_rejected = ($permohonan->tahapan_proses == 'Ditolak');
                        @endphp
                        <span class="absolute flex items-center justify-center w-3 h-3 {{ $step2_done ? 'bg-green-500' : ($step2_rejected ? 'bg-red-500' : ($step2_active ? 'bg-blue-500' : 'bg-gray-200')) }} rounded-full -left-[6.5px] ring-4 ring-white"></span>
                        <h4 class="text-[11px] font-bold uppercase tracking-wider {{ $step2_done || $step2_active || $step2_rejected ? 'text-gray-900' : 'text-gray-400' }}">Verifikasi Desk Layanan</h4>
                        <p class="text-[10px] font-medium text-gray-400 mt-1">Oleh Desk Layanan</p>
                        @if($step2_rejected)
                            <p class="text-xs text-red-600 mt-2 font-medium">Permohonan Ditolak</p>
                        @endif
                    </li>

                    <!-- Step 3 -->
                    <li class="mb-6 ml-6">
                        @php
                            $step3_done = in_array($permohonan->tahapan_proses, ['Diuji', 'Menunggu TTE', 'Selesai']);
                            $step3_active = in_array($permohonan->tahapan_proses, ['Diverifikasi', 'Ditugaskan']);
                        @endphp
                        <span class="absolute flex items-center justify-center w-3 h-3 {{ $step3_done ? 'bg-green-500' : ($step3_active ? 'bg-blue-500' : 'bg-gray-200') }} rounded-full -left-[6.5px] ring-4 ring-white"></span>
                        <h4 class="text-[11px] font-bold uppercase tracking-wider {{ $step3_done || $step3_active ? 'text-gray-900' : 'text-gray-400' }}">Penyediaan Data</h4>
                        <p class="text-[10px] font-medium text-gray-400 mt-1">Oleh Penghubung Bidang</p>
                    </li>

                    <!-- Step 4 -->
                    <li class="mb-6 ml-6">
                        @php
                            $step4_done = in_array($permohonan->tahapan_proses, ['Menunggu TTE', 'Selesai']);
                            $step4_active = ($permohonan->tahapan_proses == 'Diuji');
                        @endphp
                        <span class="absolute flex items-center justify-center w-3 h-3 {{ $step4_done ? 'bg-green-500' : ($step4_active ? 'bg-blue-500' : 'bg-gray-200') }} rounded-full -left-[6.5px] ring-4 ring-white"></span>
                        <h4 class="text-[11px] font-bold uppercase tracking-wider {{ $step4_done || $step4_active ? 'text-gray-900' : 'text-gray-400' }}">Pengujian Data</h4>
                        <p class="text-[10px] font-medium text-gray-400 mt-1">Oleh PPID Pelaksana</p>
                    </li>

                    <!-- Step 5 -->
                    <li class="ml-6">
                        @php
                            $step5_done = ($permohonan->tahapan_proses == 'Selesai');
                            $step5_active = ($permohonan->tahapan_proses == 'Menunggu TTE');
                        @endphp
                        <span class="absolute flex items-center justify-center w-3 h-3 {{ $step5_done ? 'bg-green-500' : ($step5_active ? 'bg-blue-500' : 'bg-gray-200') }} rounded-full -left-[6.5px] ring-4 ring-white"></span>
                        <h4 class="text-[11px] font-bold uppercase tracking-wider {{ $step5_done || $step5_active ? 'text-gray-900' : 'text-gray-400' }}">Persetujuan (TTE)</h4>
                        <p class="text-[10px] font-medium text-gray-400 mt-1">Oleh Atasan PPID</p>
                    </li>
                </ol>
            </div>
            
            <!-- Activity Logs -->
            <div class="bg-white border border-gray-200 rounded shadow-sm p-5">
                <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wider mb-5">Log Aktivitas</h3>
                
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
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
