<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Preview Dokumen - {{ $dokumen->judul }}</title>
    <style>
        @page {
            margin: 25mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1e293b;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .kop-text {
            text-align: center;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0f172a;
        }
        .kop-text h1 {
            margin: 3px 0;
            font-size: 14pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #03224d;
        }
        .kop-text h3 {
            margin: 0 0 4px 0;
            font-size: 11pt;
            font-weight: 700;
            color: #2563eb;
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 0;
            font-size: 9pt;
            color: #64748b;
        }
        .doc-title-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 5px solid #2563eb;
            padding: 14px 18px;
            margin-bottom: 22px;
            border-radius: 4px;
        }
        .badge-category {
            display: inline-block;
            font-size: 8.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 3px;
            background-color: #dbeafe;
            color: #1d4ed8;
            margin-bottom: 6px;
        }
        .doc-title {
            margin: 0;
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.35;
        }
        .section-title {
            font-size: 10.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 10px;
        }
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        table.meta-table th, 
        table.meta-table td {
            padding: 7px 10px;
            font-size: 9.5pt;
            vertical-align: top;
            border: 1px solid #e2e8f0;
        }
        table.meta-table th {
            width: 32%;
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
            text-align: left;
        }
        table.meta-table td {
            color: #0f172a;
        }
        .summary-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 12px 14px;
            font-size: 10pt;
            line-height: 1.6;
            color: #334155;
            text-align: justify;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .notice-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 4px solid #16a34a;
            padding: 10px 14px;
            font-size: 8.5pt;
            color: #166534;
            margin-bottom: 25px;
            border-radius: 4px;
            line-height: 1.5;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .sig-right {
            text-align: center;
        }
        .sig-space {
            height: 60px;
        }
        .sig-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }
        .sig-nip {
            font-size: 8.5pt;
            color: #64748b;
        }
        .qr-placeholder {
            display: inline-block;
            border: 1px dashed #94a3b8;
            padding: 6px 10px;
            font-size: 7.5pt;
            color: #64748b;
            background-color: #f8fafc;
            border-radius: 4px;
            margin-top: 6px;
        }
        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Header / Kop Surat -->
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: center;">
                <img src="{{ public_path('ppid_logo.png') }}" style="max-height: 55px; width: auto;" alt="Logo">
            </td>
            <td style="width: 85%;">
                <div class="kop-text">
                    <h2>Pemerintah Daerah Kabupaten Ciamis</h2>
                    <h1>Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah</h1>
                    <h3>Pejabat Pengelola Informasi dan Dokumentasi (PPID)</h3>
                    <p>Jl. Stasiun No. 1 Ciamis 46211 &bull; Telp: (0265) 771031 &bull; Laman: bapperida.ciamiskab.go.id</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen Box -->
    <div class="doc-title-box">
        <span class="badge-category">
            Daftar Informasi Publik (DIP) &bull; {{ $dokumen->kategori?->nama_kategori ?? 'Informasi Publik' }}
        </span>
        <h2 class="doc-title">{{ $dokumen->judul }}</h2>
    </div>

    <!-- Klasifikasi & Metadata -->
    <div class="section-title">Klasifikasi &amp; Identitas Dokumen</div>
    <table class="meta-table">
        <tr>
            <th>Nomor / ID Dokumen</th>
            <td><strong>DIP-BAPPERIDA-{{ str_pad($dokumen->id, 4, '0', STR_PAD_LEFT) }}/{{ $dokumen->tahun }}</strong></td>
        </tr>
        <tr>
            <th>Kategori Regulasi UU KIP</th>
            <td>{{ $dokumen->kategori?->nama_kategori ?? '-' }} <em>({{ $dokumen->kategori?->deskripsi ?? 'Ketentuan UU No. 14/2008' }})</em></td>
        </tr>
        <tr>
            <th>Jenis Produk Perencanaan</th>
            <td>{{ $dokumen->jenis_dokumen }}</td>
        </tr>
        <tr>
            <th>Tahun Anggaran / Berlaku</th>
            <td><strong>Tahun {{ $dokumen->tahun }}</strong></td>
        </tr>
        <tr>
            <th>Pejabat Penanggung Jawab</th>
            <td>{{ $dokumen->penanggung_jawab }}</td>
        </tr>
        <tr>
            <th>Unit Kerja Pengolah</th>
            <td>{{ $dokumen->unitPengolah?->nama_bidang ?? 'Bapperida Kabupaten Ciamis' }}</td>
        </tr>
        <tr>
            <th>Format &amp; Ukuran Berkas</th>
            <td>{{ $dokumen->tipe_media ?? 'PDF' }} ({{ $dokumen->file_size ?? 'Dokumen Resmi' }})</td>
        </tr>
        <tr>
            <th>Status Keterbukaan</th>
            <td><strong style="color: #16a34a;">Terbuka Untuk Umum (Dapat Diunduh Langsung)</strong></td>
        </tr>
    </table>

    <!-- Ringkasan / Abstrak Dokumen -->
    <div class="section-title">Ringkasan &amp; Substansi Informasi</div>
    <div class="summary-box">
        {{ $dokumen->ringkasan ?? 'Dokumen resmi perencanaan dan informasi publik yang disusun serta diterbitkan oleh Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah (Bapperida) Kabupaten Ciamis sesuai amanat Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik.' }}
    </div>

    <!-- Catatan Legalitas -->
    <div class="notice-box">
        <strong>Pernyataan Keterbukaan Informasi:</strong><br>
        Dokumen ini merupakan bagian dari Daftar Informasi Publik (DIP) resmi Pemerintah Kabupaten Ciamis. Masyarakat berhak membaca, mengunduh, dan menggunakan informasi ini untuk tujuan yang sah, akademis, transparansi tata kelola pemerintahan daerah, dan kemaslahatan publik dengan mencantumkan sumber resmi.
    </div>

    <!-- Tanda Tangan & Pengesahan -->
    <table class="signature-table">
        <tr>
            <td>
                <div style="font-size: 8.5pt; color: #64748b;">
                    <strong>Verifikasi Dokumen:</strong><br>
                    Tercatat pada Sistem E-PPID Ciamis<br>
                    Waktu Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB<br>
                    <div class="qr-placeholder">
                        VERIFIED BY E-PPID BAPPERIDA CIAMIS<br>
                        SECURE REGISTRATION ID #{{ $dokumen->id }}
                    </div>
                </div>
            </td>
            <td class="sig-right">
                <p>Ciamis, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p><strong>PPID Pelaksana Bapperida Ciamis</strong></p>
                <div class="sig-space"></div>
                <p class="sig-name">TIM PPID BAPPERIDA CIAMIS</p>
                <p class="sig-nip">Pemerintah Kabupaten Ciamis</p>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen Daftar Informasi Publik (DIP) resmi elektronik &bull; Portal E-PPID Bapperida Kabupaten Ciamis &bull; Halaman 1 dari 1
    </div>

</body>
</html>
