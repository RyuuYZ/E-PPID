@extends('admin.layouts.app')

@section('title', 'Daftar Permohonan Informasi - Admin E-PPID')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen"
      x-data="{
          selectedItem: null,
          modalOpen: false,
          copied: false,
          openDetail(data) {
              this.selectedItem = data;
              this.modalOpen = true;
          },
          closeDetail() {
              this.modalOpen = false;
              setTimeout(() => { this.selectedItem = null; }, 200);
          },
          copyNomorRegistrasi(no) {
              navigator.clipboard.writeText(no);
              this.copied = true;
              setTimeout(() => { this.copied = false; }, 2000);
          }
      }"
      @keydown.escape.window="closeDetail()">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-2xl text-blue-600">assignment</span>
                <h1 class="text-xl md:text-2xl font-bold text-slate-800 tracking-tight">
                    @if($currentStatus)
                        Permohonan: {{ ucwords(str_replace('_', ' ', $currentStatus)) }}
                    @else
                        Daftar Semua Permohonan
                    @endif
                </h1>
            </div>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Kelola, verifikasi, dan pantau seluruh permohonan informasi publik secara terintegrasi.</p>
        </div>
        
        <!-- Actions / Filter -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.permohonan.create') }}" class="inline-flex items-center gap-1.5 bg-[#03224d] text-white px-3.5 py-2 rounded-xl shadow-xs text-xs font-semibold hover:bg-[#0B1B3D] transition-all">
                <span class="material-symbols-outlined text-[16px]">add</span> 
                <span>Tambah Permohonan</span>
            </a>

            <div class="relative">
                <select class="bg-white border border-slate-200 text-slate-700 rounded-xl shadow-xs text-xs py-2 pl-3 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium appearance-none cursor-pointer" 
                        onchange="window.location.href=this.value">
                    <option value="{{ route('admin.permohonan.index') }}">Semua Status</option>
                    <option value="{{ route('admin.permohonan.index', ['status' => 'diajukan']) }}" {{ $currentStatus == 'diajukan' ? 'selected' : '' }}>Permohonan Masuk (Diajukan)</option>
                    <option value="{{ route('admin.permohonan.index', ['status' => 'diverifikasi']) }}" {{ $currentStatus == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="{{ route('admin.permohonan.index', ['status' => 'ditugaskan']) }}" {{ $currentStatus == 'ditugaskan' ? 'selected' : '' }}>Ditugaskan</option>
                    <option value="{{ route('admin.permohonan.index', ['status' => 'menunggu_data']) }}" {{ $currentStatus == 'menunggu_data' ? 'selected' : '' }}>Menunggu Data</option>
                    <option value="{{ route('admin.permohonan.index', ['status' => 'data_diuji']) }}" {{ $currentStatus == 'data_diuji' ? 'selected' : '' }}>Data Diuji</option>
                    <option value="{{ route('admin.permohonan.index', ['status' => 'menunggu_tanda_tangan']) }}" {{ $currentStatus == 'menunggu_tanda_tangan' ? 'selected' : '' }}>Menunggu Tanda Tangan</option>
                    <option value="{{ route('admin.permohonan.index', ['status' => 'selesai']) }}" {{ $currentStatus == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full table-fixed text-left border-collapse text-sm text-slate-600">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200">
                        <th class="w-[22%] px-4 py-3.5">No. Registrasi</th>
                        <th class="w-[26%] px-4 py-3.5">Pemohon</th>
                        <th class="w-[18%] px-4 py-3.5">Tanggal Masuk</th>
                        <th class="w-[22%] px-4 py-3.5">Status</th>
                        <th class="w-[12%] px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($permohonan as $p)
                    @php
                        $modalPayload = [
                            'id' => $p->id,
                            'nomor_registrasi' => $p->nomor_registrasi,
                            'nama_pemohon' => $p->nama_pemohon,
                            'nik_atau_no_badan_hukum' => $p->nik_atau_no_badan_hukum ?? '-',
                            'email' => $p->email ?? '-',
                            'no_telp' => $p->no_telp ?? '-',
                            'pekerjaan' => $p->pekerjaan ?? '-',
                            'alamat' => $p->alamat ?? '-',
                            'kategori_pemohon' => $p->kategori_pemohon?->nama_kategori_pemohon ?? '-',
                            'rincian_informasi' => $p->rincian_informasi ?? '-',
                            'tujuan_penggunaan' => $p->tujuan_penggunaan ?? '-',
                            'cara_memperoleh' => $p->cara_memperoleh_informasi?->nama_cara_memperoleh_informasi ?? '-',
                            'cara_salinan' => $p->cara_mendapatkan_salinan ?? '-',
                            'status_label' => $p->status->label(),
                            'status_badge_class' => $p->status->badgeClass(),
                            'tanggal_masuk' => $p->created_at->format('d M Y, H:i') . ' WIB',
                            'file_identitas' => $p->file_identitas ? asset('storage/' . $p->file_identitas) : null,
                            'url_show' => route('admin.permohonan.show', $p->id),
                            'url_tanda_terima' => route('permohonan.tanda_terima', $p->nomor_registrasi),
                        ];
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="bg-slate-100 text-slate-700 font-mono px-2.5 py-1 rounded-lg text-xs font-semibold border border-slate-200/80 shadow-2xs">
                                {{ $p->nomor_registrasi }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-slate-800 truncate" title="{{ $p->nama_pemohon }}">{{ $p->nama_pemohon }}</div>
                            <div class="text-xs text-slate-400 truncate">{{ $p->email ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-500 whitespace-nowrap text-xs">
                            <div class="font-medium text-slate-700">{{ $p->created_at->format('d M Y') }}</div>
                            <div class="text-[11px] text-slate-400">{{ $p->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border border-current/20 {{ $p->status->badgeClass() }} max-w-full" title="{{ $p->status->label() }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75 shrink-0"></span>
                                <span class="truncate">{{ $p->status->label() }}</span>
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            <!-- Tombol Aksi Popup (Modal) -->
                            <button type="button" 
                                    @click="openDetail({{ json_encode($modalPayload) }})"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition-all shadow-2xs cursor-pointer"
                                    title="Lihat Rincian & Aksi (Popup)">
                                <span class="material-symbols-outlined text-[18px]">more_vert</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-slate-400 text-sm">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">folder_off</span>
                                <p class="font-medium text-slate-600">Belum ada data permohonan</p>
                                <p class="text-xs text-slate-400 mt-0.5">Permohonan yang masuk akan tercatat secara otomatis di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Bottom Bar -->
        <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50 rounded-b-2xl">
            {{ $permohonan->links('vendor.pagination.custom', ['resourceName' => 'PERMOHONAN']) }}
        </div>
    </div>

    <!-- ========================================== -->
    <!-- POPUP MODAL (Rincian Permohonan & Aksi)     -->
    <!-- ========================================== -->
    <div x-show="modalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop Blur Overlay -->
        <div x-show="modalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
             @click="closeDetail()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <!-- Modal Content Card -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl border border-slate-200 transition-all sm:my-8 sm:w-full sm:max-w-2xl flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-slate-900 to-[#03224d] px-6 py-4 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-blue-300 border border-white/10">
                            <span class="material-symbols-outlined text-[20px]">assignment</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white tracking-tight" id="modal-title">Rincian Permohonan Informasi</h3>
                            <p class="text-xs text-blue-200/80">Informasi ringkas dan pintasan aksi permohonan</p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="closeDetail()" 
                            class="text-white/70 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 overflow-y-auto space-y-5 text-slate-700 text-xs" x-if="selectedItem">
                    <!-- Status & No. Registrasi Banner -->
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">No. Registrasi</div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-mono text-sm font-bold text-slate-800" x-text="selectedItem?.nomor_registrasi"></span>
                                <button type="button" 
                                        @click="copyNomorRegistrasi(selectedItem?.nomor_registrasi)" 
                                        class="text-slate-400 hover:text-blue-600 transition-colors p-1 rounded hover:bg-white"
                                        title="Salin Nomor Registrasi">
                                    <span class="material-symbols-outlined text-[16px]" x-show="!copied">content_copy</span>
                                    <span class="material-symbols-outlined text-[16px] text-emerald-600" x-show="copied" style="display: none;">check</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-col sm:items-end">
                            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Terkini</div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-current/20"
                                  :class="selectedItem?.status_badge_class">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75"></span>
                                <span x-text="selectedItem?.status_label"></span>
                            </span>
                        </div>
                    </div>

                    <!-- 2-Columns Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Identitas Pemohon -->
                        <div class="border border-slate-200/80 rounded-xl p-4 bg-white space-y-2.5">
                            <div class="flex items-center gap-1.5 font-bold text-slate-800 text-xs pb-2 border-b border-slate-100">
                                <span class="material-symbols-outlined text-[16px] text-blue-600">person</span>
                                <span>Identitas Pemohon</span>
                            </div>
                            
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Nama Pemohon</span>
                                <span class="font-bold text-slate-800 text-xs" x-text="selectedItem?.nama_pemohon"></span>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">NIK / No. Badan Hukum</span>
                                <span class="font-medium text-slate-700" x-text="selectedItem?.nik_atau_no_badan_hukum"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Kategori</span>
                                    <span class="font-medium text-slate-700" x-text="selectedItem?.kategori_pemohon"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Pekerjaan</span>
                                    <span class="font-medium text-slate-700" x-text="selectedItem?.pekerjaan"></span>
                                </div>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Email & Kontak</span>
                                <span class="font-medium text-slate-700" x-text="selectedItem?.email + ' / ' + selectedItem?.no_telp"></span>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Alamat</span>
                                <span class="font-medium text-slate-700" x-text="selectedItem?.alamat"></span>
                            </div>
                        </div>

                        <!-- Rincian Permohonan -->
                        <div class="border border-slate-200/80 rounded-xl p-4 bg-white space-y-2.5">
                            <div class="flex items-center gap-1.5 font-bold text-slate-800 text-xs pb-2 border-b border-slate-100">
                                <span class="material-symbols-outlined text-[16px] text-blue-600">description</span>
                                <span>Rincian Permohonan</span>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tanggal Masuk</span>
                                <span class="font-medium text-slate-700" x-text="selectedItem?.tanggal_masuk"></span>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Informasi yang Dimohon</span>
                                <p class="font-medium text-slate-700 bg-slate-50 p-2 rounded-lg border border-slate-100 max-h-24 overflow-y-auto leading-relaxed" 
                                   x-text="selectedItem?.rincian_informasi"></p>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tujuan Penggunaan</span>
                                <p class="font-medium text-slate-700 bg-slate-50 p-2 rounded-lg border border-slate-100 max-h-20 overflow-y-auto leading-relaxed" 
                                   x-text="selectedItem?.tujuan_penggunaan"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Cara Memperoleh</span>
                                    <span class="font-medium text-slate-700" x-text="selectedItem?.cara_memperoleh"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Bentuk Salinan</span>
                                    <span class="font-medium text-slate-700" x-text="selectedItem?.cara_salinan"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Berkas Identitas jika ada -->
                    <template x-if="selectedItem?.file_identitas">
                        <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 text-[20px]">badge</span>
                                <div>
                                    <div class="font-semibold text-slate-800 text-xs">Berkas Identitas Pemohon (KTP / Akta)</div>
                                    <div class="text-[11px] text-slate-500">Tersedia dokumen lampiran identitas</div>
                                </div>
                            </div>
                            <a :href="selectedItem?.file_identitas" target="_blank" 
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-blue-200 text-blue-700 hover:bg-blue-50 font-semibold text-xs transition-colors shadow-2xs">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                <span>Lihat Berkas</span>
                            </a>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer Actions -->
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="w-full sm:w-auto">
                        <template x-if="selectedItem?.url_tanda_terima">
                            <a :href="selectedItem?.url_tanda_terima" target="_blank"
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-semibold text-xs transition-colors shadow-2xs">
                                <span class="material-symbols-outlined text-[16px]">print</span>
                                <span>Cetak Tanda Terima</span>
                            </a>
                        </template>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <button type="button" 
                                @click="closeDetail()"
                                class="flex-1 sm:flex-initial px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs transition-colors shadow-2xs cursor-pointer">
                            Tutup
                        </button>
                        
                        <a :href="selectedItem?.url_show"
                           class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-[#03224d] text-white hover:bg-[#0B1B3D] font-semibold text-xs transition-all shadow-xs">
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            <span>Proses & Detail Lengkap</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</main>
@endsection
