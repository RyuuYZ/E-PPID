@extends('admin.layouts.app')

@section('content')
@php
    $currentStatusVal = $permohonan->status->value;
    
    // Alur 6 Tahap Penanganan Permohonan E-PPID
    // 1: Pengajuan, 2: Verifikasi Berkas, 3: Disposisi Penugasan, 4: Validasi & Uji Data, 5: Pengesahan TTE, 6: Selesai
    if ($currentStatusVal === 'diajukan') {
        $activeStep = 2; // Pemohon sudah selesai ajukan, sekarang tahap Verifikasi Desk Layanan
        $stepStatus = [
            1 => 'completed',
            2 => 'current',
            3 => 'pending',
            4 => 'pending',
            5 => 'pending',
            6 => 'pending',
        ];
    } elseif ($currentStatusVal === 'menunggu_kelengkapan') {
        $activeStep = 2;
        $stepStatus = [
            1 => 'completed',
            2 => 'warning', // perlu perbaikan berkas
            3 => 'pending',
            4 => 'pending',
            5 => 'pending',
            6 => 'pending',
        ];
    } elseif ($currentStatusVal === 'ditutup_tidak_lengkap') {
        $activeStep = 2;
        $stepStatus = [
            1 => 'completed',
            2 => 'rejected',
            3 => 'pending',
            4 => 'pending',
            5 => 'pending',
            6 => 'pending',
        ];
    } elseif ($currentStatusVal === 'diverifikasi') {
        $activeStep = 3; // Verifikasi selesai, sekarang tahap Disposisi Penugasan
        $stepStatus = [
            1 => 'completed',
            2 => 'completed',
            3 => 'current',
            4 => 'pending',
            5 => 'pending',
            6 => 'pending',
        ];
    } elseif (in_array($currentStatusVal, ['ditugaskan', 'menunggu_data'])) {
        $activeStep = 4; // Ditugaskan, sekarang tahap Pengumpulan & Validasi Data
        $stepStatus = [
            1 => 'completed',
            2 => 'completed',
            3 => 'completed',
            4 => 'current',
            5 => 'pending',
            6 => 'pending',
        ];
    } elseif ($currentStatusVal === 'data_diuji') {
        $activeStep = 4; // Pengujian kesesuaian data oleh PPID
        $stepStatus = [
            1 => 'completed',
            2 => 'completed',
            3 => 'completed',
            4 => 'current',
            5 => 'pending',
            6 => 'pending',
        ];
    } elseif ($currentStatusVal === 'menunggu_tanda_tangan') {
        $activeStep = 5; // Validasi draf selesai, sekarang tahap Pengesahan TTE
        $stepStatus = [
            1 => 'completed',
            2 => 'completed',
            3 => 'completed',
            4 => 'completed',
            5 => 'current',
            6 => 'pending',
        ];
    } elseif ($currentStatusVal === 'ditandatangani') {
        $activeStep = 6; // TTE selesai, sekarang tahap Pengiriman Jawaban ke Pemohon
        $stepStatus = [
            1 => 'completed',
            2 => 'completed',
            3 => 'completed',
            4 => 'completed',
            5 => 'completed',
            6 => 'current',
        ];
    } elseif (in_array($currentStatusVal, ['selesai', 'keberatan_diajukan', 'keberatan_diputuskan'])) {
        $activeStep = 6;
        $stepStatus = [
            1 => 'completed',
            2 => 'completed',
            3 => 'completed',
            4 => 'completed',
            5 => 'completed',
            6 => 'completed',
        ];
    } else {
        $activeStep = 1;
        $stepStatus = [1 => 'current', 2 => 'pending', 3 => 'pending', 4 => 'pending', 5 => 'pending', 6 => 'pending'];
    }

    $workflowSteps = [
        1 => [
            'title' => 'Pengajuan',
            'desc' => 'Registrasi Permohonan',
            'role' => 'Pemohon',
            'icon' => 'edit_document',
        ],
        2 => [
            'title' => 'Verifikasi',
            'desc' => 'Pemeriksaan Berkas',
            'role' => 'Desk Layanan',
            'icon' => 'fact_check',
        ],
        3 => [
            'title' => 'Penugasan',
            'desc' => 'Disposisi Unit Pengolah',
            'role' => 'PPID Pelaksana',
            'icon' => 'forward_to_inbox',
        ],
        4 => [
            'title' => 'Validasi Data',
            'desc' => 'Pengujian & Draf Jawaban',
            'role' => 'Unit & PPID',
            'icon' => 'rule',
        ],
        5 => [
            'title' => 'Pengesahan TTE',
            'desc' => 'Tanda Tangan Digital',
            'role' => 'Atasan PPID',
            'icon' => 'draw',
        ],
        6 => [
            'title' => 'Selesai',
            'desc' => 'Penyerahan Jawaban',
            'role' => 'Desk Layanan',
            'icon' => 'task_alt',
        ],
    ];

    // Status SLA Calculation
    $slaColor = 'bg-slate-100 text-slate-600 border-slate-200';
    $slaText = 'Belum Dihitung';
    if ($permohonan->batas_waktu_jawaban) {
        $jatuhTempo = \Carbon\Carbon::parse($permohonan->batas_waktu_jawaban);
        $now = \Carbon\Carbon::now();
        if ($permohonan->status->isTerminal()) {
            $slaColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $slaText = 'Selesai';
        } elseif ($now->greaterThan($jatuhTempo)) {
            $slaColor = 'bg-rose-50 text-rose-700 border-rose-200';
            $slaText = 'Melewati Batas (SLA Breach)';
        } else {
            $sisa = $now->diffInWeekdays($jatuhTempo);
            if ($sisa <= 2) {
                $slaColor = 'bg-amber-50 text-amber-700 border-amber-200';
            } else {
                $slaColor = 'bg-blue-50 text-blue-700 border-blue-200';
            }
            $slaText = "Sisa $sisa Hari Kerja";
        }
    }
@endphp

<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen"
      x-data="{
          confirmOpen: false,
          confirmTitle: '',
          confirmMessage: '',
          confirmBadge: '',
          confirmBtnText: 'Ya, Lanjutkan',
          confirmBtnColor: 'bg-blue-600 hover:bg-blue-700 text-white',
          confirmIcon: 'help',
          confirmIconBg: 'bg-blue-50 text-blue-600 border-blue-100',
          targetFormId: null,
          customCallback: null,

          openConfirm({ title, message, badge, btnText, btnColor, icon, iconBg, formId, onConfirm }) {
              this.confirmTitle = title || 'Konfirmasi Tindakan';
              this.confirmMessage = message || 'Apakah Anda yakin ingin melanjutkan proses ini?';
              this.confirmBadge = badge || 'Alur Proses Permohonan';
              this.confirmBtnText = btnText || 'Ya, Lanjutkan';
              this.confirmBtnColor = btnColor || 'bg-blue-600 hover:bg-blue-700 text-white';
              this.confirmIcon = icon || 'help';
              this.confirmIconBg = iconBg || 'bg-blue-50 text-blue-600 border-blue-100';
              this.targetFormId = formId || null;
              this.customCallback = onConfirm || null;
              this.confirmOpen = true;
          },

          closeConfirm() {
              this.confirmOpen = false;
              this.targetFormId = null;
              this.customCallback = null;
          },

          proceed() {
              this.confirmOpen = false;
              if (typeof this.customCallback === 'function') {
                  this.customCallback();
              } else if (this.targetFormId) {
                  const form = document.getElementById(this.targetFormId);
                  if (form) form.submit();
              }
          }
      }">
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1.5">
                <a href="{{ route('admin.permohonan.index', ['status' => $permohonan->status->value]) }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Detail Permohonan Informasi</h1>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 ml-10">
                <span>Nomor Registrasi:</span>
                <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200/60">{{ $permohonan->nomor_registrasi }}</span>
                <span class="text-slate-300">•</span>
                <span>Diajukan: <strong class="text-slate-700 font-medium">{{ $permohonan->created_at->format('d M Y, H:i') }} WIB</strong></span>
            </div>
        </div>
        
        <div class="flex items-center gap-2 self-stretch md:self-auto justify-end">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border {{ $permohonan->status->badgeClass() }} shadow-xs">
                <span class="w-2 h-2 rounded-full bg-current opacity-75 animate-pulse"></span>
                {{ $permohonan->status->label() }}
            </span>
            @if($permohonan->batas_waktu_jawaban)
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border {{ $slaColor }} shadow-xs" title="Batas Waktu: {{ \Carbon\Carbon::parse($permohonan->batas_waktu_jawaban)->format('d M Y') }}">
                <span class="material-symbols-outlined text-[14px]">schedule</span>
                SLA: {{ $slaText }}
            </span>
            @endif
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-emerald-800 bg-emerald-50/90 border border-emerald-200 rounded-xl shadow-xs flex items-center gap-2.5" role="alert">
            <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
            <div><strong class="font-bold">Berhasil!</strong> {{ session('success') }}</div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-rose-800 bg-rose-50/90 border border-rose-200 rounded-xl shadow-xs flex items-center gap-2.5" role="alert">
            <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
            <div><strong class="font-bold">Gagal!</strong> {{ session('error') }}</div>
        </div>
    @endif

    <!-- Visual Stepper: Alur Workflow Penanganan Permohonan (Clean & Modern) -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs p-5 md:p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 mb-6 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                    <span class="material-symbols-outlined text-[18px]">account_tree</span>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 m-0">Alur Penanganan Permohonan</h2>
                    <p class="text-[11px] text-slate-500 m-0">Tahapan penanganan dari registrasi pengajuan hingga pengesahan dan penyerahan jawaban</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-full border border-slate-200/80">
                    Tahap {{ $activeStep }} dari 6: <strong class="text-blue-700 font-bold">{{ $workflowSteps[$activeStep]['title'] }}</strong>
                </span>
                @if($permohonan->status->responsibleRole())
                <span class="text-[11px] font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-100" title="Penanggung Jawab Saat Ini">
                    PIC: <strong>{{ $permohonan->status->responsibleRole() }}</strong>
                </span>
                @endif
            </div>
        </div>

        <!-- Stepper Nodes Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 relative">
            <!-- Background connecting line (Desktop) -->
            <div class="hidden lg:block absolute top-5 left-12 right-12 h-0.5 bg-slate-200 z-0"></div>
            @php
                $lineProgressPercent = min(100, max(0, (($activeStep - 1) / 5) * 100));
            @endphp
            <div class="hidden lg:block absolute top-5 left-12 h-0.5 bg-emerald-500 z-0 transition-all duration-500" style="width: calc(({{ $lineProgressPercent }} / 100) * (100% - 96px));"></div>

            @foreach($workflowSteps as $stepNum => $step)
                @php
                    $state = $stepStatus[$stepNum];
                @endphp
                <div class="flex flex-col items-center text-center group relative z-10">
                    <!-- Step Indicator Circle -->
                    <div class="mb-3 relative">
                        @if($state === 'completed')
                            <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-sm shadow-emerald-200 ring-4 ring-emerald-50 transition-all duration-300">
                                <span class="material-symbols-outlined text-[20px] font-bold">check</span>
                            </div>
                        @elseif($state === 'current')
                            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-300 ring-4 ring-blue-100 transition-all duration-300 relative">
                                <span class="material-symbols-outlined text-[20px]">{{ $step['icon'] }}</span>
                                <span class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-amber-400 border-2 border-white rounded-full animate-ping"></span>
                                <span class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-amber-400 border-2 border-white rounded-full"></span>
                            </div>
                        @elseif($state === 'warning')
                            <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center shadow-sm shadow-amber-200 ring-4 ring-amber-50 transition-all duration-300">
                                <span class="material-symbols-outlined text-[20px] font-bold">priority_high</span>
                            </div>
                        @elseif($state === 'rejected')
                            <div class="w-10 h-10 rounded-full bg-rose-500 text-white flex items-center justify-center shadow-sm shadow-rose-200 ring-4 ring-rose-50 transition-all duration-300">
                                <span class="material-symbols-outlined text-[20px] font-bold">close</span>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center border border-slate-200/80 transition-all duration-300">
                                <span class="material-symbols-outlined text-[18px]">{{ $step['icon'] }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Step Text Info -->
                    <div class="space-y-0.5">
                        <div class="flex items-center justify-center gap-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $state === 'current' ? 'text-blue-600' : ($state === 'completed' ? 'text-emerald-600' : 'text-slate-400') }}">
                                Tahap {{ $stepNum }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold {{ $state === 'current' ? 'text-blue-950' : ($state === 'completed' ? 'text-slate-800' : 'text-slate-400') }} leading-tight">
                            {{ $step['title'] }}
                        </h4>
                        <p class="text-[10px] {{ $state === 'current' ? 'text-slate-600 font-medium' : ($state === 'completed' ? 'text-slate-500' : 'text-slate-400') }} leading-tight">
                            {{ $step['desc'] }}
                        </p>
                        <span class="inline-block mt-1 text-[9px] font-semibold px-2 py-0.5 rounded-full {{ $state === 'current' ? 'bg-blue-100 text-blue-800 font-bold' : ($state === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-400') }}">
                            {{ $step['role'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Content Layout (2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri (2 Kolom): Profil Pemohon & Rincian Permohonan -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Profil Pemohon -->
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-blue-600">person</span>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Data Pemohon</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 bg-white border border-slate-200 px-2.5 py-0.5 rounded-full">
                        {{ $permohonan->kategori_pemohon->nama_kategori ?? 'Pemohon' }}
                    </span>
                </div>
                <div class="p-0">
                    <table class="w-full text-xs text-left text-slate-600">
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wider bg-slate-50/40 w-1/3 text-[11px]">Nama / Instansi</th>
                                <td class="px-6 py-3 font-bold text-slate-800 text-[13px]">{{ $permohonan->nama_pemohon }}</td>
                            </tr>
                            <tr>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wider bg-slate-50/40 text-[11px]">Kategori</th>
                                <td class="px-6 py-3 text-slate-700 font-medium">{{ $permohonan->kategori_pemohon->nama_kategori ?? '-' }}</td>
                            </tr>
                            
                            @if(!auth()->user()->hasRole('Petugas Penghubung') || auth()->user()->hasRole('Super Admin'))
                            <tr>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wider bg-slate-50/40 text-[11px]">NIK / No. Badan Hukum</th>
                                <td class="px-6 py-3 font-mono text-slate-800">{{ $permohonan->nik_atau_no_badan_hukum ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wider bg-slate-50/40 text-[11px]">Pekerjaan</th>
                                <td class="px-6 py-3 text-slate-700">{{ $permohonan->pekerjaan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wider bg-slate-50/40 text-[11px]">Alamat</th>
                                <td class="px-6 py-3 text-slate-700 leading-relaxed">{{ $permohonan->alamat ?? '-' }}</td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="2" class="px-6 py-3 text-[11px] text-slate-400 italic text-center bg-slate-50/60">
                                    <span class="material-symbols-outlined text-[14px] align-middle mr-1 text-slate-400">lock</span>
                                    Data identitas disembunyikan untuk Petugas Penghubung sesuai kebijakan privasi PPID.
                                </td>
                            </tr>
                            @endif
                            
                            <tr>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wider bg-slate-50/40 text-[11px]">Email</th>
                                <td class="px-6 py-3 text-slate-700 font-mono">{{ $permohonan->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="px-6 py-3 font-semibold text-slate-500 uppercase tracking-wider bg-slate-50/40 text-[11px]">No. Telepon / WhatsApp</th>
                                <td class="px-6 py-3 text-slate-700 font-mono">{{ $permohonan->no_telp ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rincian Permohonan Informasi -->
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-blue-600">description</span>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Rincian Permohonan</h3>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <!-- Judul / Pokok Informasi -->
                    @if(!empty($permohonan->subjek_informasi))
                    <div>
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-blue-600">bookmark</span>
                            Judul / Pokok Informasi
                        </h4>
                        <div class="p-3.5 bg-blue-50/60 border border-blue-200/70 rounded-xl text-sm font-bold text-slate-900 leading-relaxed">
                            {{ $permohonan->subjek_informasi }}
                        </div>
                    </div>
                    @endif

                    <!-- Isi / Uraian Rincian Informasi -->
                    <div>
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">notes</span>
                            Isi / Uraian Rincian Informasi
                        </h4>
                        <div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-xl text-[13px] text-slate-800 whitespace-pre-line leading-relaxed">
                            {{ $permohonan->rincian_informasi }}
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">target</span>
                            Tujuan Penggunaan Informasi
                        </h4>
                        <div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-xl text-[13px] text-slate-800 whitespace-pre-line leading-relaxed">
                            {{ $permohonan->tujuan_penggunaan }}
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                        <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cara Memperoleh Informasi</span>
                            <span class="text-xs font-bold text-slate-800">{{ $permohonan->cara_memperoleh_informasi->nama_cara ?? 'Melihat/Membaca' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cara Mendapatkan Salinan</span>
                            <span class="text-xs font-bold text-slate-800">{{ $permohonan->cara_mendapatkan_salinan ?? 'Softcopy (Email)' }}</span>
                        </div>
                    </div>

                    @if(!auth()->user()->hasRole('Petugas Penghubung') || auth()->user()->hasRole('Super Admin'))
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">badge</span>
                            Berkas Lampiran Identitas (KTP/Akta)
                        </h4>
                        @if($permohonan->file_identitas)
                        <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200/80 rounded-xl">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">id_card</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">Dokumen Identitas Pemohon</p>
                                <p class="text-[11px] text-slate-500">Tersimpan aman pada storage privat server</p>
                            </div>
                            <a href="{{ route('admin.permohonan.file-identitas', $permohonan->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-blue-700 transition-colors shadow-xs">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                                Lihat Berkas
                            </a>
                        </div>
                        @else
                        <div class="text-xs text-slate-400 italic bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                            Tidak ada berkas lampiran yang disertakan oleh pemohon.
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: PENUGASAN DI BAGIAN ATAS, Kemudian TINDAK LANJUT, Kemudian LOG AKTIVITAS -->
        <div class="space-y-6">
            
            <!-- [REPOSISI] 1. PENUGASAN UNIT PENGOLAH (Ditempatkan di Atas Tindak Lanjut) -->
            @if($permohonan->penugasan->isNotEmpty())
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-indigo-600">assignment_ind</span>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Penugasan Unit Pengolah</h3>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $permohonan->penugasan->count() }} Unit
                    </span>
                </div>
                
                <div class="p-4 space-y-3">
                    @foreach($permohonan->penugasan as $tugas)
                    <div class="p-3.5 bg-slate-50/80 border border-slate-200/90 rounded-xl space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ $tugas->unitPengolah->nama_bidang }}</h4>
                                <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">person</span>
                                    <span>Petugas: <strong class="text-slate-700">{{ $tugas->petugasPenghubung->name }}</strong></span>
                                </p>
                            </div>
                            <div class="text-right flex flex-col items-end gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider border {{ $tugas->status->badgeClass() }} border-opacity-40 shadow-2xs">
                                    {{ $tugas->status->label() }}
                                </span>
                                @if($tugas->hasil_uji !== \App\Enums\HasilUji::Pending)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider border {{ $tugas->hasil_uji->badgeClass() }} border-opacity-40 shadow-2xs">
                                    {{ $tugas->hasil_uji->label() }}
                                </span>
                                @endif
                            </div>
                        </div>

                        @if($tugas->instruksi)
                        <div class="text-[11px] text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200/70 italic">
                            <span class="font-semibold text-slate-700 not-italic">Instruksi:</span> "{{ $tugas->instruksi }}"
                        </div>
                        @endif

                        <!-- Aksi / Berkas Data -->
                        <div class="pt-2 border-t border-slate-200/60">
                            <!-- Petugas Penghubung mengunggah data -->
                            @if((auth()->user()->id === $tugas->petugas_penghubung_id || auth()->user()->hasRole('Super Admin')) && $tugas->status === \App\Enums\PenugasanStatus::Ditugaskan)
                            <form id="submitDataForm_{{ $tugas->id }}" action="{{ route('admin.penugasan.submit-data', $tugas->id) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                @csrf
                                <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider">Unggah Berkas Informasi:</label>
                                <input type="file" name="data_file" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" required>
                                <textarea name="catatan" rows="1" class="w-full text-xs p-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan berkas (opsional)..."></textarea>
                                <button type="button" 
                                        @click="
                                            const f = document.getElementById('submitDataForm_{{ $tugas->id }}');
                                            if (!f.checkValidity()) { f.reportValidity(); return; }
                                            openConfirm({
                                                title: 'Konfirmasi Pengiriman Berkas Data',
                                                message: 'Berkas data akan dikirimkan ke PPID Pelaksana untuk diverifikasi dan diuji kesesuaiannya. Lanjutkan?',
                                                badge: 'Pengumpulan Data Unit',
                                                btnText: 'Ya, Kirim ke PPID',
                                                btnColor: 'bg-blue-600 hover:bg-blue-700 text-white',
                                                icon: 'cloud_upload',
                                                iconBg: 'bg-blue-50 text-blue-600 border-blue-100',
                                                formId: 'submitDataForm_{{ $tugas->id }}'
                                            })"
                                        class="w-full inline-flex items-center justify-center gap-1.5 bg-blue-600 text-white text-[11px] font-bold px-3 py-2 rounded-lg hover:bg-blue-700 transition-colors uppercase tracking-wider shadow-xs cursor-pointer">
                                    <span class="material-symbols-outlined text-[15px]">cloud_upload</span> Kirim Data ke PPID
                                </button>
                            </form>
                            @elseif($tugas->data_path)
                            <div class="space-y-2">
                                <a href="{{ Storage::url($tugas->data_path) }}" target="_blank" class="inline-flex items-center justify-between w-full p-2.5 bg-white border border-blue-200 rounded-lg text-blue-700 text-xs font-semibold hover:bg-blue-50 transition-colors shadow-2xs">
                                    <span class="flex items-center gap-1.5 truncate">
                                        <span class="material-symbols-outlined text-[16px] text-blue-600">attachment</span>
                                        <span class="truncate">Unduh Berkas Data</span>
                                    </span>
                                    <span class="material-symbols-outlined text-[14px]">download</span>
                                </a>
                                
                                @if($tugas->catatan_petugas_penghubung)
                                <p class="text-[11px] text-slate-500 italic bg-white p-2 rounded-lg border border-slate-200/60">
                                    <span class="font-semibold text-slate-600 not-italic">Catatan Petugas:</span> {{ $tugas->catatan_petugas_penghubung }}
                                </p>
                                @endif

                                <!-- Review oleh PPID Pelaksana saat Data Sedang Diuji -->
                                @if((auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin')) && $permohonan->status === \App\Enums\PermohonanStatus::DataDiuji && $tugas->hasil_uji === \App\Enums\HasilUji::Pending)
                                <div class="pt-2 border-t border-slate-200/70">
                                    <span class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-2">Uji Kelayakan Data:</span>
                                    <form id="reviewForm_{{ $tugas->id }}" action="{{ route('admin.penugasan.review', $tugas->id) }}" method="POST" class="grid grid-cols-2 gap-2">
                                        @csrf
                                        <input type="hidden" name="hasil_uji" id="hasil_uji_{{ $tugas->id }}" value="sesuai">
                                        <button type="button" 
                                                @click="
                                                    document.getElementById('hasil_uji_{{ $tugas->id }}').value = 'sesuai';
                                                    openConfirm({
                                                        title: 'Konfirmasi Validasi Data Sesuai',
                                                        message: 'Tandai berkas data dari {{ $tugas->unitPengolah->nama_bidang }} sebagai SESUAI dan siap diproses ke draf jawaban?',
                                                        badge: 'Uji Kelayakan Data',
                                                        btnText: 'Ya, Tandai Sesuai',
                                                        btnColor: 'bg-emerald-600 hover:bg-emerald-700 text-white',
                                                        icon: 'check_circle',
                                                        iconBg: 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                        formId: 'reviewForm_{{ $tugas->id }}'
                                                    })"
                                                class="inline-flex items-center justify-center gap-1 bg-emerald-600 text-white text-[11px] py-1.5 px-3 rounded-lg font-bold uppercase tracking-wider hover:bg-emerald-700 transition-colors shadow-xs cursor-pointer">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span> Sesuai
                                        </button>
                                        <button type="button" 
                                                @click="
                                                    document.getElementById('hasil_uji_{{ $tugas->id }}').value = 'perlu_revisi';
                                                    openConfirm({
                                                        title: 'Konfirmasi Permintaan Revisi Data',
                                                        message: 'Minta petugas penghubung {{ $tugas->unitPengolah->nama_bidang }} untuk merevisi atau mengunggah ulang data?',
                                                        badge: 'Uji Kelayakan Data',
                                                        btnText: 'Ya, Minta Revisi',
                                                        btnColor: 'bg-amber-600 hover:bg-amber-700 text-white',
                                                        icon: 'replay',
                                                        iconBg: 'bg-amber-50 text-amber-600 border-amber-100',
                                                        formId: 'reviewForm_{{ $tugas->id }}'
                                                    })"
                                                class="inline-flex items-center justify-center gap-1 bg-amber-600 text-white text-[11px] py-1.5 px-3 rounded-lg font-bold uppercase tracking-wider hover:bg-amber-700 transition-colors shadow-xs cursor-pointer">
                                            <span class="material-symbols-outlined text-[14px]">replay</span> Revisi
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="text-[11px] text-slate-400 italic flex items-center gap-1.5 py-1">
                                <span class="material-symbols-outlined text-[15px] text-amber-500">pending</span>
                                Menunggu petugas mengunggah data...
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- 2. PANEL TINDAK LANJUT (Aksi Sesuai Status Workflow) -->
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs relative">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center rounded-t-2xl">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-blue-600">play_circle</span>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Tindak Lanjut</h3>
                    </div>
                    @if($permohonan->status === \App\Enums\PermohonanStatus::DataDiuji && (auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin')) && !$permohonan->diperpanjang)
                    <form id="extendDeadlineForm" action="{{ route('admin.permohonan.extend-deadline', $permohonan->id) }}" method="POST">
                        @csrf
                        <button type="button" 
                                @click="openConfirm({
                                    title: 'Konfirmasi Perpanjangan Waktu',
                                    message: 'Batas waktu respon dan jawaban permohonan akan diperpanjang selama 7 hari kerja sesuai ketentuan UU KIP. Lanjutkan?',
                                    badge: 'Perpanjangan Waktu Layanan',
                                    btnText: 'Ya, Perpanjang Waktu',
                                    btnColor: 'bg-blue-600 hover:bg-blue-700 text-white',
                                    icon: 'update',
                                    iconBg: 'bg-blue-50 text-blue-600 border-blue-100',
                                    formId: 'extendDeadlineForm'
                                })"
                                class="text-[10px] bg-white border border-slate-300 text-slate-700 px-2.5 py-1 rounded-lg hover:bg-slate-50 font-bold uppercase tracking-wider shadow-2xs cursor-pointer">
                            +7 Hari Perpanjangan
                        </button>
                    </form>
                    @endif
                </div>
                
                <div class="p-5">
                    <!-- Desk Layanan: Verifikasi Permohonan Masuk -->
                    @if($permohonan->status === \App\Enums\PermohonanStatus::Diajukan || $permohonan->status === \App\Enums\PermohonanStatus::MenungguKelengkapan)
                        @if(auth()->user()->hasRole('Desk Layanan') || auth()->user()->hasRole('Super Admin'))
                            <form id="verifikasiForm" action="{{ route('admin.permohonan.update-status', $permohonan->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Hasil Verifikasi Berkas:</label>
                                    <select name="target_status" id="verifikasi_action" class="w-full h-10 px-3.5 text-xs font-semibold text-slate-800 bg-slate-50/60 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 shadow-2xs transition-all cursor-pointer" onchange="document.getElementById('catatan_tak_lengkap').style.display = this.value === 'menunggu_kelengkapan' ? 'block' : 'none'">
                                        <option value="diverifikasi">Berkas Lengkap & Terverifikasi</option>
                                        <option value="menunggu_kelengkapan">Berkas Belum Lengkap (Perlu Dilengkapi)</option>
                                    </select>
                                </div>
                                <div id="catatan_tak_lengkap" style="display: none;">
                                    <label class="block text-[11px] font-bold text-rose-700 uppercase tracking-wider mb-2">Rincian Kekurangan Berkas:</label>
                                    <textarea name="alasan_tidak_lengkap" rows="3" class="w-full text-xs px-3 py-2.5 border border-rose-300 rounded-xl bg-rose-50/50 text-slate-800 focus:ring-2 focus:ring-rose-200" placeholder="Jelaskan berkas atau persyaratan apa yang kurang..."></textarea>
                                </div>
                                <button type="button" 
                                        @click="
                                            const action = document.getElementById('verifikasi_action').value;
                                            const isLengkap = action === 'diverifikasi';
                                            openConfirm({
                                                title: isLengkap ? 'Konfirmasi Verifikasi Berkas' : 'Konfirmasi Berkas Belum Lengkap',
                                                message: isLengkap ? 'Apakah Anda yakin seluruh berkas permohonan ini SUDAH LENGKAP dan siap diproses ke tahap disposisi PPID Pelaksana?' : 'Apakah Anda yakin ingin meminta pemohon melengkapi kekurangan berkas sesuai catatan?',
                                                badge: 'Verifikasi Berkas Permohonan',
                                                btnText: isLengkap ? 'Ya, Verifikasi Berkas' : 'Ya, Minta Kelengkapan',
                                                btnColor: isLengkap ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-amber-600 hover:bg-amber-700 text-white',
                                                icon: isLengkap ? 'verified' : 'warning',
                                                iconBg: isLengkap ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-amber-50 text-amber-600 border-amber-100',
                                                formId: 'verifikasiForm'
                                            })"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-bold rounded-xl py-2.5 px-4 hover:bg-blue-700 text-xs uppercase tracking-wider shadow-sm transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    Simpan & Proses Verifikasi
                                </button>
                            </form>
                        @else
                            <div class="bg-slate-50 p-5 rounded-xl text-xs text-center text-slate-600 border border-slate-200/80">
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-1 block mx-auto">hourglass_top</span>
                                Menunggu verifikasi berkas oleh <strong>Desk Layanan</strong>.
                            </div>
                        @endif

                    <!-- PPID Pelaksana: Disposisi Penugasan ke Unit Pengolah -->
                    @elseif($permohonan->status === \App\Enums\PermohonanStatus::Diverifikasi)
                        @if(auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                            <form action="{{ route('admin.permohonan.assign', $permohonan->id) }}" method="POST" id="assignForm" class="space-y-4">
                                @csrf
                                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Tugaskan ke Unit Pengolah</h4>
                                        <p class="text-[11px] text-slate-500 m-0">Pilih unit kerja dan petugas untuk pencarian data</p>
                                    </div>
                                    <button type="button" onclick="addAssignRow()" class="inline-flex items-center gap-1 text-[11px] bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg uppercase font-bold tracking-wider transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]">add</span> Tambah Unit
                                    </button>
                                </div>
                                
                                <div id="assignContainer" class="space-y-3">
                                    <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2.5 assign-row">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Unit Pengolah / Bidang:</label>
                                            <select name="assignments[0][unit_pengolah_id]" data-placeholder="Pilih Unit/Bidang..." class="custom-select w-full text-xs font-medium" required>
                                                <option value="">Pilih Unit/Bidang...</option>
                                                @foreach($unitPengolahs as $up)
                                                <option value="{{ $up->id }}">{{ $up->nama_bidang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Petugas Penghubung:</label>
                                            <select name="assignments[0][petugas_penghubung_id]" data-placeholder="Pilih Petugas Penghubung..." class="custom-select w-full text-xs font-medium" required>
                                                <option value="">Pilih Petugas Penghubung...</option>
                                                @foreach($petugasPenghubungs as $petugas)
                                                <option value="{{ $petugas->id }}">{{ $petugas->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Instruksi Khusus (Opsional):</label>
                                            <input type="text" name="assignments[0][instruksi]" placeholder="Contoh: Lampirkan data tahun 2026 format PDF..." class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-1 focus:ring-blue-500 bg-white">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" 
                                        @click="
                                            const form = document.getElementById('assignForm');
                                            if (!form.checkValidity()) { form.reportValidity(); return; }
                                            openConfirm({
                                                title: 'Konfirmasi Disposisi Penugasan',
                                                message: 'Tugas pencarian data akan didelegasikan ke Unit Pengolah yang dipilih. Lanjutkan?',
                                                badge: 'Disposisi Unit Pengolah',
                                                btnText: 'Ya, Disposisikan & Minta Data',
                                                btnColor: 'bg-blue-600 hover:bg-blue-700 text-white',
                                                icon: 'send',
                                                iconBg: 'bg-blue-50 text-blue-600 border-blue-100',
                                                formId: 'assignForm'
                                            })"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-bold rounded-xl py-2.5 px-4 hover:bg-blue-700 text-xs uppercase tracking-wider shadow-sm transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">send</span>
                                    Disposisikan & Minta Data
                                </button>
                            </form>
                            
                            <script>
                                let assignIdx = 1;
                                function addAssignRow() {
                                    const container = document.getElementById('assignContainer');
                                    const newRow = document.createElement('div');
                                    newRow.className = 'p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2.5 assign-row relative pt-8';
                                    newRow.innerHTML = `
                                        <button type="button" onclick="this.closest('.assign-row').remove()" class="absolute top-2 right-2 text-rose-600 hover:text-rose-800 text-[10px] font-bold flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[14px]">delete</span> Hapus
                                        </button>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Unit Pengolah / Bidang:</label>
                                            <select name="assignments[${assignIdx}][unit_pengolah_id]" data-placeholder="Pilih Unit/Bidang..." class="custom-select w-full text-xs font-medium" required>
                                                <option value="">Pilih Unit/Bidang...</option>
                                                @foreach($unitPengolahs as $up)
                                                <option value="{{ $up->id }}">{{ $up->nama_bidang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Petugas Penghubung:</label>
                                            <select name="assignments[${assignIdx}][petugas_penghubung_id]" data-placeholder="Pilih Petugas Penghubung..." class="custom-select w-full text-xs font-medium" required>
                                                <option value="">Pilih Petugas Penghubung...</option>
                                                @foreach($petugasPenghubungs as $petugas)
                                                <option value="{{ $petugas->id }}">{{ $petugas->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Instruksi Khusus (Opsional):</label>
                                            <input type="text" name="assignments[${assignIdx}][instruksi]" placeholder="Contoh: Lampirkan data tahun 2026 format PDF..." class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-1 focus:ring-blue-500 bg-white">
                                        </div>
                                    `;
                                    container.appendChild(newRow);
                                    if (typeof initCustomSelects === 'function') {
                                        initCustomSelects(newRow);
                                    }
                                    assignIdx++;
                                }
                            </script>
                        @else
                            <div class="bg-slate-50 p-5 rounded-xl text-xs text-center text-slate-600 border border-slate-200/80">
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-1 block mx-auto">forward_to_inbox</span>
                                Menunggu <strong>PPID Pelaksana</strong> mendisposisikan tugas pencarian data ke Unit Pengolah.
                            </div>
                        @endif

                    <!-- PPID Pelaksana: Susun Draf Jawaban & Ajukan ke Atasan -->
                    @elseif($permohonan->status === \App\Enums\PermohonanStatus::DataDiuji)
                        @if(auth()->user()->hasRole('PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                            @if($permohonan->allPenugasanSesuai())
                            <form id="ajukanTteForm" action="{{ route('admin.permohonan.update-status', $permohonan->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="target_status" value="menunggu_tanda_tangan">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Tautan Draf Surat Jawaban (Opsional):</label>
                                    <input type="text" name="surat_jawaban_path" class="w-full text-xs px-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-200" placeholder="Contoh: /storage/draf/jawaban_reg003.pdf">
                                    <p class="text-[11px] text-emerald-600 mt-1.5 flex items-center gap-1 font-medium">
                                        <span class="material-symbols-outlined text-[15px]">verified</span>
                                        Semua berkas data telah sesuai. Siap diajukan ke Atasan PPID untuk disahkan.
                                    </p>
                                </div>
                                <button type="button" 
                                        @click="
                                            openConfirm({
                                                title: 'Konfirmasi Pengajuan Konsep Jawaban',
                                                message: 'Konsep surat jawaban akan diajukan ke Atasan PPID untuk diverifikasi dan disahkan dengan TTE. Lanjutkan?',
                                                badge: 'Pengajuan TTE ke Atasan',
                                                btnText: 'Ya, Ajukan ke Atasan',
                                                btnColor: 'bg-indigo-600 hover:bg-indigo-700 text-white',
                                                icon: 'draw',
                                                iconBg: 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                formId: 'ajukanTteForm'
                                            })"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 text-white font-bold rounded-xl py-2.5 px-4 hover:bg-indigo-700 text-xs uppercase tracking-wider shadow-sm transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">draw</span>
                                    Ajukan ke Atasan untuk TTE
                                </button>
                            </form>
                            @else
                            <div class="bg-amber-50/80 p-4 rounded-xl text-xs text-amber-800 border border-amber-200/80 flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0 mt-0.5">info</span>
                                <div>
                                    <p class="font-bold mb-1">Menunggu Validasi Kelayakan Data</p>
                                    <p class="text-[11px] leading-relaxed">Silakan periksa berkas data pada kotak <strong>Penugasan Unit Pengolah</strong> di atas dan tandai statusnya <strong>Sesuai</strong> sebelum mengajukan ke Atasan PPID.</p>
                                </div>
                            </div>
                            @endif
                        @else
                            <div class="bg-slate-50 p-5 rounded-xl text-xs text-center text-slate-600 border border-slate-200/80">
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-1 block mx-auto">rule</span>
                                PPID Pelaksana sedang memvalidasi data dan menyusun konsep surat jawaban.
                            </div>
                        @endif

                    <!-- Atasan PPID: Tanda Tangan Elektronik (TTE) -->
                    @elseif($permohonan->status === \App\Enums\PermohonanStatus::MenungguTandaTangan)
                        @if(auth()->user()->hasRole('Atasan PPID Pelaksana') || auth()->user()->hasRole('Super Admin'))
                            <div x-data="{
                                useSaved: {{ auth()->user()->signature_path ? 'true' : 'false' }},
                                hasDrawn: false,
                                isDrawing: false,
                                lastX: 0,
                                lastY: 0,
                                initCanvas() {
                                    const canvas = document.getElementById('signature-pad');
                                    if (!canvas) return;
                                    const ctx = canvas.getContext('2d');
                                    
                                    const resize = () => {
                                        const rect = canvas.getBoundingClientRect();
                                        canvas.width = rect.width || 400;
                                        canvas.height = 150;
                                        ctx.strokeStyle = '#03224d';
                                        ctx.lineWidth = 2.5;
                                        ctx.lineCap = 'round';
                                        ctx.lineJoin = 'round';
                                    };
                                    resize();
                                    window.addEventListener('resize', resize);

                                    const getPos = (e) => {
                                        const r = canvas.getBoundingClientRect();
                                        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                                        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                                        return {
                                            x: clientX - r.left,
                                            y: clientY - r.top
                                        };
                                    };

                                    const start = (e) => {
                                        this.isDrawing = true;
                                        const pos = getPos(e);
                                        this.lastX = pos.x;
                                        this.lastY = pos.y;
                                    };

                                    const draw = (e) => {
                                        if (!this.isDrawing) return;
                                        e.preventDefault();
                                        const pos = getPos(e);
                                        ctx.beginPath();
                                        ctx.moveTo(this.lastX, this.lastY);
                                        ctx.lineTo(pos.x, pos.y);
                                        ctx.stroke();
                                        this.lastX = pos.x;
                                        this.lastY = pos.y;
                                        this.hasDrawn = true;
                                    };

                                    const stop = () => {
                                        this.isDrawing = false;
                                    };

                                    canvas.addEventListener('mousedown', start);
                                    canvas.addEventListener('mousemove', draw);
                                    window.addEventListener('mouseup', stop);

                                    canvas.addEventListener('touchstart', start, { passive: false });
                                    canvas.addEventListener('touchmove', draw, { passive: false });
                                    window.addEventListener('touchend', stop);
                                },
                                clearCanvas() {
                                    const canvas = document.getElementById('signature-pad');
                                    if (canvas) {
                                        const ctx = canvas.getContext('2d');
                                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                                        this.hasDrawn = false;
                                    }
                                },
                                submitTTE() {
                                    const form = document.getElementById('tteForm');
                                    const useSavedInput = document.getElementById('use_saved_signature');
                                    const sigDataInput = document.getElementById('signature_data');

                                    if (this.useSaved) {
                                        useSavedInput.value = '1';
                                        openConfirm({
                                            title: 'Konfirmasi Pengesahan TTE',
                                            message: 'Surat jawaban akan disahkan secara resmi menggunakan tanda tangan tersimpan profil Anda. Lanjutkan?',
                                            badge: 'Pengesahan TTE Atasan PPID',
                                            btnText: 'Ya, Sahkan & TTD',
                                            btnColor: 'bg-emerald-600 hover:bg-emerald-700 text-white',
                                            icon: 'verified_user',
                                            iconBg: 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            formId: 'tteForm',
                                            onConfirm: () => { form.submit(); }
                                        });
                                    } else {
                                        useSavedInput.value = '0';
                                        const canvas = document.getElementById('signature-pad');
                                        if (!this.hasDrawn || !canvas) {
                                            alert('Mohon gambar tanda tangan Anda terlebih dahulu, atau centang tanda tangan tersimpan.');
                                            return;
                                        }
                                        sigDataInput.value = canvas.toDataURL('image/png');
                                        openConfirm({
                                            title: 'Konfirmasi Pengesahan TTE',
                                            message: 'Surat jawaban akan disahkan secara resmi dengan tanda tangan yang Anda gambar. Lanjutkan?',
                                            badge: 'Pengesahan TTE Atasan PPID',
                                            btnText: 'Ya, Sahkan & TTD',
                                            btnColor: 'bg-emerald-600 hover:bg-emerald-700 text-white',
                                            icon: 'verified_user',
                                            iconBg: 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            formId: 'tteForm',
                                            onConfirm: () => { form.submit(); }
                                        });
                                    }
                                }
                            }" x-init="initCanvas()">
                                <form action="{{ route('admin.permohonan.update-status', $permohonan->id) }}" method="POST" id="tteForm" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="target_status" value="ditandatangani">
                                    <input type="hidden" name="signature_data" id="signature_data">
                                    <input type="hidden" name="use_saved_signature" id="use_saved_signature" :value="useSaved ? '1' : '0'">
                                    
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Tanda Tangan Elektronik (TTE)</h4>
                                        <p class="text-[11px] text-slate-500 mb-3">Bubuhkan tanda tangan elektronik untuk mengesahkan draf jawaban permohonan.</p>
                                        
                                        @if(auth()->user()->signature_path)
                                        <div class="mb-3 p-3 border border-blue-200 bg-blue-50/70 rounded-xl">
                                            <label class="flex items-start gap-2.5 cursor-pointer">
                                                <input type="checkbox" x-model="useSaved" id="toggle_saved_signature" class="mt-0.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                <div>
                                                    <span class="text-xs font-bold text-blue-900 block">Gunakan Tanda Tangan Tersimpan</span>
                                                    <p class="text-[10px] text-blue-700 mt-0.5">Centang untuk menggunakan tanda tangan profil Anda secara otomatis.</p>
                                                </div>
                                            </label>
                                            <div x-show="useSaved" class="mt-2.5 bg-white border border-slate-200 p-2.5 rounded-lg text-center">
                                                <img src="{{ Storage::url(auth()->user()->signature_path) }}" alt="Tanda Tangan Tersimpan" class="max-h-24 mx-auto">
                                            </div>
                                        </div>
                                        @endif

                                        <div x-show="!useSaved" class="border border-slate-300 rounded-xl overflow-hidden bg-white shadow-2xs">
                                            <div class="bg-slate-50 border-b border-slate-200 px-3 py-2 flex justify-between items-center">
                                                <span class="text-[10px] text-slate-600 font-bold uppercase tracking-wider">Gambar Tanda Tangan:</span>
                                                <button type="button" @click="clearCanvas()" class="text-[10px] text-rose-600 hover:text-rose-800 font-bold uppercase tracking-wider flex items-center gap-0.5 cursor-pointer">
                                                    <span class="material-symbols-outlined text-[13px]">refresh</span> Bersihkan
                                                </button>
                                            </div>
                                            <canvas id="signature-pad" class="w-full h-36 touch-none cursor-crosshair bg-white" width="400" height="150"></canvas>
                                        </div>
                                        
                                        <div x-show="!useSaved" class="mt-2">
                                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 hover:text-slate-800">
                                                <input type="checkbox" name="save_signature" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                <span>Simpan sebagai tanda tangan default di profil saya</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <button type="button" id="btnSubmitTTE" 
                                            @click="submitTTE()"
                                            class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white font-bold rounded-xl py-2.5 px-4 hover:bg-emerald-700 text-xs uppercase tracking-wider shadow-sm transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">verified_user</span>
                                        Sahkan & Tandatangani Surat Jawaban
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-slate-50 p-5 rounded-xl text-xs text-center text-slate-600 border border-slate-200/80">
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-1 block mx-auto">history_edu</span>
                                Menunggu persetujuan dan Tanda Tangan Elektronik (TTE) dari <strong>Atasan PPID</strong>.
                            </div>
                        @endif

                    <!-- Desk Layanan: Kirim Surat Jawaban Final ke Pemohon -->
                    @elseif($permohonan->status === \App\Enums\PermohonanStatus::Ditandatangani)
                        @if(auth()->user()->hasRole('Desk Layanan') || auth()->user()->hasRole('Super Admin'))
                            <form id="kirimJawabanForm" action="{{ route('admin.permohonan.update-status', $permohonan->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="target_status" value="selesai">
                                <div class="text-center p-3 bg-emerald-50 border border-emerald-200/80 rounded-xl">
                                    <span class="material-symbols-outlined text-3xl text-emerald-600 mb-1">mark_email_read</span>
                                    <p class="text-xs font-bold text-emerald-900">Surat Jawaban Telah Ditandatangani</p>
                                    <p class="text-[11px] text-emerald-700 mt-1">Kirimkan notifikasi dan surat jawaban resmi ke pemohon untuk menyelesaikan permohonan.</p>
                                </div>
                                <button type="button" 
                                        @click="
                                            openConfirm({
                                                title: 'Konfirmasi Penyelesaian Permohonan',
                                                message: 'Surat jawaban resmi akan dikirimkan kepada pemohon dan status permohonan akan ditutup (SELESAI). Lanjutkan?',
                                                badge: 'Penyelesaian Layanan Informasi',
                                                btnText: 'Ya, Kirim & Selesaikan',
                                                btnColor: 'bg-emerald-600 hover:bg-emerald-700 text-white',
                                                icon: 'send_and_archive',
                                                iconBg: 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                formId: 'kirimJawabanForm'
                                            })"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-bold rounded-xl py-2.5 px-4 hover:bg-blue-700 text-xs uppercase tracking-wider shadow-sm transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">send_and_archive</span>
                                    Kirim Jawaban ke Pemohon & Selesaikan
                                </button>
                            </form>
                        @else
                            <div class="bg-slate-50 p-5 rounded-xl text-xs text-center text-slate-600 border border-slate-200/80">
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-1 block mx-auto">mark_email_unread</span>
                                Surat telah disahkan. Menunggu Desk Layanan mengirimkan berkas jawaban kepada pemohon.
                            </div>
                        @endif

                    <!-- Petugas Penghubung: Menunggu Pengunggahan Data -->
                    @elseif($permohonan->status === \App\Enums\PermohonanStatus::Ditugaskan || $permohonan->status === \App\Enums\PermohonanStatus::MenungguData)
                        <div class="bg-blue-50/70 p-5 rounded-xl text-xs text-center text-blue-800 border border-blue-200/80">
                            <span class="material-symbols-outlined text-3xl text-blue-600 mb-1 block mx-auto">cloud_sync</span>
                            <p class="font-bold uppercase tracking-wider mb-1">Proses Pengumpulan Data</p>
                            <p class="text-[11px] text-blue-600 leading-relaxed">Petugas Penghubung dari unit yang ditugaskan dapat mengunggah berkas data melalui kotak penugasan di atas.</p>
                        </div>
                        
                    <!-- Status Selesai / Ditutup -->
                    @else
                        <div class="text-center py-4">
                            <span class="material-symbols-outlined {{ $permohonan->status === \App\Enums\PermohonanStatus::Selesai ? 'text-emerald-500' : 'text-rose-500' }} text-4xl mb-1">
                                {{ $permohonan->status === \App\Enums\PermohonanStatus::Selesai ? 'task_alt' : 'cancel' }}
                            </span>
                            <p class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Permohonan {{ $permohonan->status->label() }}</p>
                            <p class="text-[11px] text-slate-500">Seluruh rangkaian proses penanganan permohonan informasi telah selesai.</p>
                            
                            @if($permohonan->ttd_path)
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold block mb-2">Tanda Tangan Pengesahan:</span>
                                <img src="{{ Storage::url($permohonan->ttd_path) }}" alt="Tanda Tangan Atasan PPID" class="max-h-20 mx-auto border border-slate-200 rounded-lg p-1 bg-white shadow-2xs">
                                <p class="text-[10px] text-slate-500 mt-1 font-medium">Disahkan oleh: {{ $permohonan->ditandatanganiOleh->name ?? 'Atasan PPID' }}</p>
                            </div>
                            @endif
                        </div>
                        
                        @if($permohonan->status->isTerminal() && (auth()->user()->hasRole('Desk Layanan') || auth()->user()->hasRole('Super Admin')))
                            @if(!\App\Models\PengajuanKeberatan::where('permohonan_informasi_id', $permohonan->id)->exists())
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <h4 class="text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Registrasi Sengketa / Keberatan</h4>
                                <form id="keberatanForm" action="{{ route('admin.keberatan.store', $permohonan->id) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <textarea name="alasan_keberatan" rows="2" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:ring-1 focus:ring-rose-500" placeholder="Alasan utama keberatan pemohon..." required></textarea>
                                    <textarea name="keterangan_tambahan" rows="2" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:ring-1 focus:ring-rose-500" placeholder="Keterangan tambahan (opsional)..."></textarea>
                                    <button type="button" 
                                            @click="
                                                const f = document.getElementById('keberatanForm');
                                                if (!f.checkValidity()) { f.reportValidity(); return; }
                                                openConfirm({
                                                    title: 'Konfirmasi Pengajuan Sengketa / Keberatan',
                                                    message: 'Apakah Anda yakin ingin mendaftarkan permohonan keberatan untuk pemohon ini?',
                                                    badge: 'Sengketa & Keberatan',
                                                    btnText: 'Ya, Ajukan Keberatan',
                                                    btnColor: 'bg-rose-600 hover:bg-rose-700 text-white',
                                                    icon: 'gavel',
                                                    iconBg: 'bg-rose-50 text-rose-600 border-rose-100',
                                                    formId: 'keberatanForm'
                                                })"
                                            class="w-full inline-flex items-center justify-center gap-1.5 bg-rose-600 text-white font-bold rounded-xl py-2 px-4 hover:bg-rose-700 text-[11px] uppercase tracking-wider transition-colors shadow-xs cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]">gavel</span>
                                        Ajukan Sengketa Keberatan
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                                <a href="{{ route('admin.keberatan.index') }}" class="text-xs text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                    <span>Lihat Berkas Sengketa/Keberatan</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>

            <!-- 3. LOG AKTIVITAS (Riwayat Alur Penanganan) -->
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs p-5">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-blue-600">history</span>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Log Aktivitas</h3>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium">{{ $permohonan->logs->count() }} Aktivitas</span>
                </div>
                
                <div class="space-y-3.5 max-h-[380px] overflow-y-auto pr-1">
                    @forelse($permohonan->logs as $log)
                        <div class="flex gap-3 text-xs">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="w-7 h-7 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                                    <span class="material-symbols-outlined text-[14px]">commit</span>
                                </div>
                            </div>
                            <div class="flex-1 bg-slate-50/80 border border-slate-200/70 p-3 rounded-xl">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">{{ $log->aksi }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('d M, H:i') }}</span>
                                </div>
                                <div class="text-[11px] text-slate-600">
                                    <span class="font-medium text-slate-700">Oleh: {{ $log->user->name ?? 'Sistem' }}</span> 
                                    @if($log->catatan)
                                        <p class="mt-1.5 text-slate-500 italic bg-white p-2 border border-slate-200/60 rounded-lg">"{{ $log->catatan }}"</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 bg-slate-50 text-slate-400 text-xs rounded-xl border border-slate-200/60">
                            Belum ada riwayat aktivitas tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- CONFIRMATION POPUP MODAL (FOR ALL WORKFLOW ACTIONS)                       -->
    <!-- ========================================================================= -->
    <div x-show="confirmOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="closeConfirm()" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <div class="p-6">
                    <div class="flex items-center gap-3.5 mb-4">
                        <div class="w-11 h-11 rounded-2xl border flex items-center justify-center shrink-0" :class="confirmIconBg">
                            <span class="material-symbols-outlined text-[24px]" x-text="confirmIcon">help</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 leading-tight" x-text="confirmTitle">Konfirmasi Tindakan</h3>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="confirmBadge">Alur Proses Permohonan</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 mb-2">
                        <p class="text-xs text-slate-700 leading-relaxed" x-text="confirmMessage"></p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="closeConfirm()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="proceed()" :class="confirmBtnColor" class="px-4 py-2 rounded-xl text-xs font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        <span x-text="confirmBtnText">Ya, Lanjutkan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</main>
@endsection
