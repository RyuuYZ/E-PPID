<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran Permohonan Informasi</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #03224d;
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #94a3b8;
        }
        .content {
            padding: 32px 24px;
        }
        .greeting {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 24px;
            color: #334155;
        }
        .reg-box {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 28px;
        }
        .reg-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            margin-bottom: 6px;
        }
        .reg-number {
            font-size: 24px;
            font-weight: 800;
            color: #03224d;
            font-family: monospace, sans-serif;
            letter-spacing: 0.05em;
            margin: 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
            font-size: 13px;
        }
        .details-table tr td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .td-label {
            width: 35%;
            color: #64748b;
            font-weight: 600;
        }
        .td-val {
            width: 65%;
            color: #0f172a;
            font-weight: 500;
        }
        .btn-group {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        .btn {
            display: inline-block;
            background-color: #03224d;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            margin: 4px 6px;
        }
        .btn-secondary {
            background-color: #f1f5f9;
            color: #03224d !important;
            border: 1px solid #cbd5e1;
        }
        .info-card {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 14px 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            font-size: 12px;
            line-height: 1.5;
            color: #1e40af;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PPID Bappeda Kabupaten Ciamis</h1>
            <p>Pejabat Pengelola Informasi dan Dokumentasi</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">
                Yth. <strong>{{ $permohonan->nama_pemohon }}</strong>,<br><br>
                Terima kasih telah mengajukan permohonan informasi publik melalui portal E-PPID Bappeda Kabupaten Ciamis. Permohonan Anda telah kami terima dan akan segera diproses oleh tim PPID.
            </p>

            <!-- Registration / Invoice Box -->
            <div class="reg-box">
                <div class="reg-label">Kode Invoice / Nomor Registrasi</div>
                <div class="reg-number">{{ $permohonan->nomor_registrasi }}</div>
            </div>

            <!-- Detail Permohonan -->
            <table class="details-table">
                <tr>
                    <td class="td-label">Tanggal Pengajuan</td>
                    <td class="td-val">{{ $permohonan->created_at ? $permohonan->created_at->format('d/m/Y H:i') : date('d/m/Y H:i') }} WIB</td>
                </tr>
                <tr>
                    <td class="td-label">NIK / No. Identitas</td>
                    <td class="td-val">{{ $permohonan->nik_atau_no_badan_hukum }}</td>
                </tr>
                <tr>
                    <td class="td-label">Alamat</td>
                    <td class="td-val">{{ $permohonan->alamat }}</td>
                </tr>
                @if(!empty($permohonan->subjek_informasi))
                <tr>
                    <td class="td-label">Judul Informasi</td>
                    <td class="td-val"><strong>{{ $permohonan->subjek_informasi }}</strong></td>
                </tr>
                @endif
                <tr>
                    <td class="td-label">Rincian Informasi</td>
                    <td class="td-val">{{ $permohonan->rincian_informasi }}</td>
                </tr>
                <tr>
                    <td class="td-label">Tujuan Penggunaan</td>
                    <td class="td-val">{{ $permohonan->tujuan_penggunaan }}</td>
                </tr>
            </table>

            <div class="info-card">
                <strong>Catatan Penting:</strong> Sesuai dengan UU KIP No. 14 Tahun 2008, petugas PPID akan melakukan verifikasi administrasi dan memproses permohonan Anda selambat-lambatnya <strong>10 (sepuluh) hari kerja</strong>. Anda dapat memantau progres secara berkala menggunakan kode invoice di atas.
            </div>

            <!-- Action Buttons -->
            <div class="btn-group">
                <a href="{{ route('permohonan.lacak', ['tracking_id' => $permohonan->nomor_registrasi]) }}" class="btn">
                    Lacak Status Permohonan
                </a>
                <a href="{{ route('permohonan.tanda_terima', $permohonan->nomor_registrasi) }}" class="btn btn-secondary">
                    Unduh Bukti Tanda Terima (PDF)
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 6px 0;"><strong>PPID Bappeda Kabupaten Ciamis</strong></p>
            <p style="margin: 0;">Jl. Jenderal Sudirman No. 16, Ciamis, Jawa Barat | Email: bappeda@ciamiskab.go.id</p>
            <p style="margin: 8px 0 0 0; font-size: 11px; color: #cbd5e1;">Email ini dibuat secara otomatis oleh sistem, mohon untuk tidak membalas email ini secara langsung.</p>
        </div>
    </div>
</body>
</html>
