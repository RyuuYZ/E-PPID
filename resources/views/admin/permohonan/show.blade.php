@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
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
            @endphp
            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider border {{ $colorClass }}">
                {{ $statusLabel }}
            </span>
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
                            @if(auth()->user()->hasRole('Desk Layanan'))
                                <div class="mb-4">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Catatan Kelengkapan:</label>
                                    <textarea name="catatan" rows="3" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-gray-50" placeholder="Catatan jika berkas tidak lengkap..."></textarea>
                                </div>
                                <input type="hidden" name="tahapan_proses" value="Diverifikasi">
                                <button type="submit" class="w-full bg-blue-600 text-white font-semibold rounded py-2 px-4 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-[11px] transition-colors uppercase tracking-wider">
                                    Verifikasi Kelengkapan
                                </button>
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
                                    <textarea name="catatan" rows="3" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-gray-50" placeholder="Tulis catatan..."></textarea>
                                </div>
                                <input type="hidden" name="tahapan_proses" value="Menunggu TTE">
                                <button type="submit" class="w-full bg-purple-600 text-white font-semibold rounded py-2 px-4 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 text-[11px] transition-colors uppercase tracking-wider">
                                    Validasi & Teruskan ke Atasan
                                </button>
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
                                <span class="material-symbols-outlined text-green-500 text-4xl mb-2">check_circle</span>
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Permohonan Selesai</p>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Timeline Tracker -->
            <div class="bg-white border border-gray-200 rounded shadow-sm p-5">
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
                            $step2_done = in_array($permohonan->tahapan_proses, ['Ditugaskan', 'Diuji', 'Menunggu TTE', 'Selesai']);
                            $step2_active = ($permohonan->tahapan_proses == 'Diverifikasi');
                        @endphp
                        <span class="absolute flex items-center justify-center w-3 h-3 {{ $step2_done ? 'bg-green-500' : ($step2_active ? 'bg-blue-500' : 'bg-gray-200') }} rounded-full -left-[6.5px] ring-4 ring-white"></span>
                        <h4 class="text-[11px] font-bold uppercase tracking-wider {{ $step2_done || $step2_active ? 'text-gray-900' : 'text-gray-400' }}">Verifikasi Desk Layanan</h4>
                        <p class="text-[10px] font-medium text-gray-400 mt-1">Oleh Desk Layanan</p>
                    </li>

                    <!-- Step 3 -->
                    <li class="mb-6 ml-6">
                        @php
                            $step3_done = in_array($permohonan->tahapan_proses, ['Diuji', 'Menunggu TTE', 'Selesai']);
                            $step3_active = ($permohonan->tahapan_proses == 'Ditugaskan');
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

        </div>
    </div>
</main>
@endsection
