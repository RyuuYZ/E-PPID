<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Permohonan - {{ $permohonan->nomor_registrasi }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background-color: white !important;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-border {
                border: 1px solid #000 !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-0 md:p-8">

    <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 shadow-sm md:shadow-md print-border">
        
        <!-- Action Buttons -->
        <div class="no-print mb-8 flex justify-end gap-3 border-b border-gray-200 pb-4">
            <button onclick="window.close()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors text-sm font-semibold">Tutup</button>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>

        <!-- Kop Surat Tanda Terima -->
        <div class="text-center mb-8 border-b-2 border-black pb-4">
            <h1 class="text-2xl font-bold uppercase tracking-wider mb-1">Tanda Terima Permohonan Informasi</h1>
            <h2 class="text-lg font-semibold text-gray-700">PPID Bappeda Kabupaten Ciamis</h2>
            <p class="text-sm mt-1">Jl. Jend. Sudirman No.16, Ciamis, Jawa Barat</p>
        </div>

        <!-- Body -->
        <div class="mb-6">
            <p class="text-sm mb-4 leading-relaxed text-justify">
                Kami menyatakan telah menerima permohonan informasi publik secara elektronik melalui portal E-PPID Bappeda Ciamis dengan rincian sebagai berikut:
            </p>

            <table class="w-full text-sm mb-6 border-collapse">
                <tbody>
                    <tr>
                        <td class="py-2 w-1/3 font-bold text-gray-600">Nomor Registrasi</td>
                        <td class="py-2 w-[2%]">:</td>
                        <td class="py-2 font-bold text-lg">{{ $permohonan->nomor_registrasi }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-bold text-gray-600">Tanggal Pengajuan</td>
                        <td class="py-2">:</td>
                        <td class="py-2">{{ $permohonan->created_at->format('d F Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-bold text-gray-600">Nama Pemohon</td>
                        <td class="py-2">:</td>
                        <td class="py-2">{{ $permohonan->nama_pemohon }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-bold text-gray-600">No. Identitas (KTP/Badan)</td>
                        <td class="py-2">:</td>
                        <td class="py-2">{{ $permohonan->nik_atau_no_badan_hukum }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-bold text-gray-600">Email</td>
                        <td class="py-2">:</td>
                        <td class="py-2">{{ $permohonan->email }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-bold text-gray-600">Rincian Informasi</td>
                        <td class="py-2">:</td>
                        <td class="py-2 whitespace-pre-line">{{ $permohonan->rincian_informasi }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="p-4 bg-gray-50 border border-gray-300 rounded text-sm text-justify leading-relaxed mt-8">
                <p class="font-bold mb-2 uppercase">Perhatian:</p>
                <ol class="list-decimal pl-4 space-y-1">
                    <li>Simpan nomor registrasi Anda untuk melakukan pelacakan status permohonan melalui menu <strong>Lacak Permohonan</strong>.</li>
                    <li>Sesuai UU No. 14 Tahun 2008, PPID memiliki kewajiban untuk merespon selambat-lambatnya <strong>10 Hari Kerja</strong> sejak permohonan ini dinyatakan lengkap (lolos verifikasi).</li>
                    <li>Jika ada kekurangan berkas administrasi, kami akan menghubungi Anda melalui kontak (Email/Telepon) yang terdaftar.</li>
                </ol>
            </div>
        </div>

        <!-- Signature/Footer -->
        <div class="mt-12 flex justify-end">
            <div class="text-center">
                <p class="text-sm mb-16">Ciamis, {{ now()->format('d F Y') }}</p>
                <p class="text-sm font-bold">( Sistem E-PPID Otomatis )</p>
                <p class="text-xs mt-1">Sistem Informasi Bappeda</p>
            </div>
        </div>
    </div>

    <script>
        // Otomatis munculkan dialog print ketika halaman dimuat
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
