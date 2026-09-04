@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.keberatan.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                </a>
                <h2 class="text-lg font-bold text-gray-800 m-0">Detail Sengketa / Keberatan</h2>
            </div>
            <p class="text-[11px] text-gray-500 ml-7">Nomor Registrasi: <span class="font-bold text-gray-700">{{ $keberatan->permohonan_informasi->nomor_registrasi }}</span></p>
        </div>
        
        <div class="flex items-center">
            @php
                $slaColor = 'bg-gray-100 text-gray-600 border-gray-200';
                $slaText = 'Belum Dihitung';
                if ($keberatan->batas_waktu_respon) {
                    $jatuhTempo = \Carbon\Carbon::parse($keberatan->batas_waktu_respon);
                    $now = \Carbon\Carbon::now();
                    if ($keberatan->status->isTerminal()) {
                        $slaColor = 'bg-green-50 text-green-600 border-green-100';
                        $slaText = 'Selesai';
                    } elseif ($now->greaterThan($jatuhTempo)) {
                        $slaColor = 'bg-red-50 text-red-600 border-red-200';
                        $slaText = 'Melewati Batas (SLA Breach)';
                    } else {
                        $sisa = $now->diffInWeekdays($jatuhTempo); // Simplification for view
                        if ($sisa <= 5) {
                            $slaColor = 'bg-orange-50 text-orange-600 border-orange-200';
                        } else {
                            $slaColor = 'bg-blue-50 text-blue-600 border-blue-200';
                        }
                        $slaText = "Sisa $sisa Hari Kerja";
                    }
                }
            @endphp
            <div class="flex flex-col items-end gap-1">
                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider border {{ $keberatan->status->badgeClass() }}">
                    {{ $keberatan->status->label() }}
                </span>
                @if($keberatan->batas_waktu_respon)
                <span class="inline-flex items-center px-2 py-1 rounded text-[9px] font-bold uppercase tracking-wider border {{ $slaColor }}" title="Batas Waktu Respon: {{ \Carbon\Carbon::parse($keberatan->batas_waktu_respon)->format('d M Y') }}">
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
            
            <div class="bg-white border border-gray-200 rounded shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 bg-red-50 flex items-center justify-between">
                    <h3 class="text-[13px] font-bold text-red-800 m-0 uppercase tracking-wider">Formulir Keberatan</h3>
                    <span class="text-xs text-red-600 font-semibold">{{ $keberatan->created_at->format('d M Y') }}</span>
                </div>
                <div class="p-5 space-y-5">
                    <div>
                        <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Alasan Pengajuan Keberatan</h4>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded text-[13px] text-gray-800 font-bold leading-relaxed">
                            {{ $keberatan->alasan_keberatan }}
                        </div>
                    </div>
                    <div>
                        <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Keterangan Tambahan Pemohon</h4>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded text-[13px] text-gray-700 whitespace-pre-line leading-relaxed italic">
                            {{ $keberatan->keterangan_tambahan ?? 'Tidak ada keterangan tambahan.' }}
                        </div>
                    </div>
                    
                    @if($keberatan->tanggapan_atasan)
                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Tanggapan Resmi (Atasan PPID)</h4>
                        <div class="p-4 bg-green-50 border border-green-200 rounded text-[13px] text-gray-800 whitespace-pre-line leading-relaxed">
                            {{ $keberatan->tanggapan_atasan }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Permohonan Asli -->
            <div class="bg-white border border-gray-200 rounded shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-[13px] font-bold text-gray-800 m-0 uppercase tracking-wider">Permohonan Informasi (Asli)</h3>
                    <a href="{{ route('admin.permohonan.show', $keberatan->permohonan_informasi_id) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat Detail Lengkap</a>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Informasi yang Diminta</h4>
                        <div class="p-3 bg-gray-50 border border-gray-100 rounded text-xs text-gray-700 whitespace-pre-line">
                            {{ $keberatan->permohonan_informasi->rincian_informasi }}
                        </div>
                    </div>
                    <div>
                        <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Status Akhir Permohonan</h4>
                        <span class="inline-block px-2 py-1 bg-gray-200 text-gray-700 text-[10px] font-bold uppercase rounded">{{ $keberatan->permohonan_informasi->status }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Action Panel & Timeline -->
        <div class="space-y-5">
            
            <!-- Action Panel for Atasan PPID -->
            <div class="bg-white border border-gray-200 rounded shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wider m-0">Penyelesaian (Atasan PPID)</h3>
                </div>
                
                <div class="p-5">
                    <form action="{{ route('admin.keberatan.update-status', $keberatan->id) }}" method="POST">
                        @csrf
                        
                        @if(!$keberatan->status->isTerminal())
                            @if(auth()->user()->hasRole('Atasan PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                                <div class="mb-4">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggapan / Putusan Atasan:</label>
                                    <textarea name="tanggapan_atasan" rows="4" class="w-full text-xs px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-gray-50" placeholder="Tuliskan keputusan atau tanggapan resmi Atasan PPID...">{{ $keberatan->tanggapan_atasan }}</textarea>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" name="status" value="Diproses" class="flex-1 bg-yellow-50 text-yellow-600 border border-yellow-200 font-semibold rounded py-2 px-2 hover:bg-yellow-100 focus:outline-none text-[10px] sm:text-[11px] transition-colors uppercase tracking-wider text-center">
                                        Simpan Draf (Diproses)
                                    </button>
                                    <button type="submit" name="status" value="Ditolak" class="flex-1 bg-red-50 text-red-600 border border-red-200 font-semibold rounded py-2 px-2 hover:bg-red-100 focus:outline-none text-[10px] sm:text-[11px] transition-colors uppercase tracking-wider text-center">
                                        Tolak Keberatan
                                    </button>
                                    <button type="submit" name="status" value="Selesai" class="flex-1 bg-green-600 text-white font-semibold rounded py-2 px-2 hover:bg-green-700 focus:outline-none text-[10px] sm:text-[11px] transition-colors uppercase tracking-wider text-center">
                                        Putuskan (Selesai)
                                    </button>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 p-4 rounded text-xs text-gray-600 mb-4 text-center">
                                    Menunggu putusan resmi dari <strong>Atasan PPID Pelaksana</strong>.
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <span class="material-symbols-outlined {{ $keberatan->status == \App\Enums\KeberatanStatus::Selesai ? 'text-green-500' : 'text-red-500' }} text-4xl mb-2">
                                    {{ $keberatan->status == \App\Enums\KeberatanStatus::Selesai ? 'gavel' : 'block' }}
                                </span>
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Sengketa {{ $keberatan->status->label() }}</p>
                                <p class="text-[10px] text-gray-500 mt-1">Pada: {{ $keberatan->tanggal_selesai ? \Carbon\Carbon::parse($keberatan->tanggal_selesai)->format('d M Y H:i') : '-' }}</p>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            
            <div class="bg-white border border-gray-200 rounded shadow-sm p-5">
                <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wider mb-3">Informasi Tambahan</h3>
                <p class="text-[11px] text-gray-600 leading-relaxed text-justify">
                    Sesuai dengan UU KIP, Atasan PPID memiliki waktu paling lambat <strong>30 Hari Kerja</strong> sejak diterimanya keberatan untuk memberikan tanggapan tertulis.
                </p>
            </div>
        </div>
    </div>
</main>
@endsection
