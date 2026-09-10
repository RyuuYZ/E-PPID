<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tanda Terima Permohonan - {{ $permohonan->nomor_registrasi }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            text-transform: uppercase;
            margin: 0 0 5px 0;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 14px;
            margin: 0 0 5px 0;
            color: #444;
        }
        .header p {
            font-size: 11px;
            margin: 0;
        }
        .content {
            margin-bottom: 20px;
        }
        .content p {
            text-align: justify;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .td-label {
            width: 30%;
            font-weight: bold;
            color: #555;
        }
        .td-colon {
            width: 5%;
        }
        .td-value {
            width: 65%;
        }
        .notice {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px 15px;
            text-align: justify;
            border-radius: 4px;
        }
        .notice h4 {
            margin-top: 0;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .notice ol {
            margin: 0;
            padding-left: 15px;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        .footer-content {
            display: inline-block;
            text-align: center;
        }
        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Tanda Terima Permohonan Informasi</h1>
        <h2>PPID Bappeda Kabupaten Ciamis</h2>
        <p>Jl. Jend. Sudirman No.16, Ciamis, Jawa Barat</p>
    </div>

    <div class="content">
        <p>
            Kami menyatakan telah menerima permohonan informasi publik secara elektronik melalui portal E-PPID Bappeda Ciamis dengan rincian sebagai berikut:
        </p>

        <table>
            <tbody>
                <tr>
                    <td class="td-label">Nomor Registrasi</td>
                    <td class="td-colon">:</td>
                    <td class="td-value"><strong>{{ $permohonan->nomor_registrasi }}</strong></td>
                </tr>
                <tr>
                    <td class="td-label">Tanggal Pengajuan</td>
                    <td class="td-colon">:</td>
                    <td class="td-value">{{ $permohonan->created_at->format('d F Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td class="td-label">Nama Pemohon</td>
                    <td class="td-colon">:</td>
                    <td class="td-value">{{ $permohonan->nama_pemohon }}</td>
                </tr>
                <tr>
                    <td class="td-label">No. Identitas (KTP/Badan)</td>
                    <td class="td-colon">:</td>
                    <td class="td-value">{{ $permohonan->nik_atau_no_badan_hukum }}</td>
                </tr>
                <tr>
                    <td class="td-label">Email</td>
                    <td class="td-colon">:</td>
                    <td class="td-value">{{ $permohonan->email }}</td>
                </tr>
                @if(!empty($permohonan->subjek_informasi))
                <tr>
                    <td class="td-label">Judul Informasi</td>
                    <td class="td-colon">:</td>
                    <td class="td-value" style="font-weight: bold;">{{ $permohonan->subjek_informasi }}</td>
                </tr>
                @endif
                <tr>
                    <td class="td-label">Rincian Informasi</td>
                    <td class="td-colon">:</td>
                    <td class="td-value">{{ $permohonan->rincian_informasi }}</td>
                </tr>
            </tbody>
        </table>

        <div class="notice">
            <h4>Perhatian:</h4>
            <ol>
                <li>Simpan nomor registrasi Anda untuk melakukan pelacakan status permohonan melalui menu <strong>Lacak Permohonan</strong>.</li>
                <li>Sesuai UU No. 14 Tahun 2008, PPID memiliki kewajiban untuk merespon selambat-lambatnya <strong>10 Hari Kerja</strong> sejak permohonan ini dinyatakan lengkap (lolos verifikasi).</li>
                <li>Jika ada kekurangan berkas administrasi, kami akan menghubungi Anda melalui kontak (Email/Telepon) yang terdaftar.</li>
            </ol>
        </div>
    </div>

    <div class="footer">
        <div class="footer-content">
            <p style="margin-bottom: 60px;">Ciamis, {{ now()->format('d F Y') }}</p>
            <p><strong>( Sistem E-PPID Otomatis )</strong></p>
            <p style="font-size: 10px;">Sistem Informasi Bappeda</p>
        </div>
    </div>

</body>
</html>
