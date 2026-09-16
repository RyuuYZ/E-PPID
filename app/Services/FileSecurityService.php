<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileSecurityService
{
    /**
     * Dangerous file extensions that should NEVER appear anywhere in the filename.
     */
    protected const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'inc', 'phpt',
        'pl', 'py', 'pyw', 'cgi', 'sh', 'bash', 'zsh', 'exe', 'bat', 'cmd', 'vbs',
        'js', 'jse', 'jsp', 'jspx', 'asp', 'aspx', 'cer', 'asa', 'asax', 'ascx', 'ashx',
        'asmx', 'axd', 'htm', 'html', 'shtml', 'xhtml', 'svg', 'htaccess', 'htpasswd',
        'pht', 'phtm', 'jar', 'war', 'ear', 'dll', 'so', 'dylib', 'bin', 'msi', 'com',
        'scr', 'reg', 'wsf', 'wsh', 'ps1', 'ps2', 'psc1', 'psc2'
    ];

    /**
     * Dangerous script content signatures to inspect within file contents.
     */
    protected const DANGEROUS_PATTERNS = [
        // PHP Tags & Functions
        '/<\?php/i',
        '/<\?=/i',
        '/<\?\s+/i',
        '/<\?[a-zA-Z]/i',
        '/<script[\s\S]*?language=[\'"]?php[\'"]?/i',
        '/__halt_compiler\s*\(/i',
        '/phpinfo\s*\(/i',
        '/eval\s*\(/i',
        '/assert\s*\(/i',
        '/(passthru|shell_exec|system|exec|proc_open|popen|pcntl_exec)\s*\(/i',
        '/base64_decode\s*\(\s*["\'][A-Za-z0-9+\/=]{20,}["\']\s*\)/i',
        '/preg_replace\s*\(\s*["\'].*\/e["\']/i',
        '/create_function\s*\(/i',

        // ASP / JSP Tags
        '/<\%/i',
        '/<\%@/i',
        '/<\%=/i',

        // HTML & JavaScript Tags & Handlers
        '/<script[\s\S]*?>/i',
        '/<\/script>/i',
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/data\s*:\s*text\/html/i',
        '/<iframe[\s\S]*?>/i',
        '/<object[\s\S]*?>/i',
        '/<embed[\s\S]*?>/i',
        '/<applet[\s\S]*?>/i',
        '/<form[\s\S]*?>/i',
        '/onload\s*=\s*["\']/i',
        '/onerror\s*=\s*["\']/i',
        '/onclick\s*=\s*["\']/i',
        '/document\.cookie/i',
        '/window\.location/i',

        // XML Entity / SVG Injection
        '/<!ENTITY/i',
        '/<!DOCTYPE[\s\S]*?ENTITY/i',
        '/xmlns:svg/i',
        '/<svg[\s\S]*?>/i',

        // Shell / Bash / Python / Perl / Shebang
        '/^#!\s*\/(usr\/)?bin\/(bash|sh|zsh|dash|python|perl|ruby|node|php)/im',
        '/#!/i',
        '/\b(curl|wget)\b[\s\S]{1,100}\|\s*(bash|sh)/i',
        '/\b(chmod\s+[0-7]{3,4}|chown\s+|rm\s+-rf\s+|nc\s+-e\s+|bash\s+-i)/i',
        '/\b(import\s+(os|sys|subprocess|shutil|socket|pty|platform|requests|urllib)|from\s+(os|sys|subprocess)\s+import)\b/i',

        // PowerShell & Batch Commands
        '/\b(powershell(\.exe)?|Invoke-Expression|IEX\s*\(|cmd(\.exe)?\s+\/[ck]|@echo\s+off|wscript\.|cscript\.)\b/i',

        // PDF Specific JavaScript Actions
        '/\/JavaScript\b/i',
        '/\/JS\b\s*[\(\<]/i',
        '/\/Launch\b/i',
        '/\/EmbeddedFiles\b/i',
        '/\/RichMedia\b/i',
    ];

    /**
     * MIME type mappings for allowed extensions.
     */
    protected const MIME_MAP = [
        'jpg'  => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png'  => ['image/png', 'image/x-png'],
        'pdf'  => ['application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/pdf', 'text/pdf', 'text/x-pdf'],
        'doc'  => ['application/msword', 'application/vnd.ms-office'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/x-zip'],
        'xls'  => ['application/vnd.ms-excel', 'application/msexcel', 'application/vnd.ms-office'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/x-zip'],
        'csv'  => ['text/csv', 'text/plain', 'application/csv', 'text/comma-separated-values'],
        'zip'  => ['application/zip', 'application/x-zip', 'application/x-zip-compressed'],
        'webp' => ['image/webp'],
    ];

    /**
     * Validate an uploaded file for security, true type, and safe content.
     *
     * @param UploadedFile $file
     * @param array $allowedExtensions
     * @param int $maxKb
     * @return string|null Error message on failure, or null on success
     */
    public function validateSecureFile(UploadedFile $file, array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'], int $maxKb = 5120): ?string
    {
        // 1. Basic Upload Integrity
        if (!$file->isValid()) {
            return 'Proses unggah berkas gagal atau berkas terputus saat dikirim.';
        }

        $realPath = $file->getRealPath();
        if (!$realPath || !file_exists($realPath) || !is_readable($realPath)) {
            return 'Berkas fisik tidak dapat dibaca oleh server.';
        }

        $fileSize = $file->getSize();
        if ($fileSize === 0) {
            return 'Berkas yang diunggah kosong (0 byte).';
        }

        if ($fileSize > ($maxKb * 1024)) {
            $maxMb = round($maxKb / 1024, 1);
            return "Ukuran berkas melebihi batas maksimal ({$maxMb} MB).";
        }

        $originalName = $file->getClientOriginalName();
        $allowedExtensions = array_map('strtolower', $allowedExtensions);

        // 2. Filename & Double Extension Sanitization
        $filenameError = $this->validateFilename($originalName, $allowedExtensions);
        if ($filenameError !== null) {
            return $filenameError;
        }

        $clientExtension = strtolower($file->getClientOriginalExtension());
        if (!in_array($clientExtension, $allowedExtensions, true)) {
            $allowedStr = strtoupper(implode(', ', $allowedExtensions));
            return "Format ekstensi berkas (.{$clientExtension}) tidak diizinkan. Ekstensi yang diizinkan: {$allowedStr}.";
        }

        // 3. True MIME Type Inspection (via finfo / server-side detection)
        $detectedMime = $this->detectMimeType($realPath);
        $mimeError = $this->validateMimeType($clientExtension, $detectedMime);
        if ($mimeError !== null) {
            return $mimeError;
        }

        // 4. Magic Bytes (Binary Header) Check
        $magicError = $this->validateMagicBytes($clientExtension, $realPath);
        if ($magicError !== null) {
            return $magicError;
        }

        // 5. Deep Malicious Script & Polyglot Payload Inspection
        $scriptError = $this->scanForMaliciousContent($realPath);
        if ($scriptError !== null) {
            return $scriptError;
        }

        // 6. Format-Specific Deep Structure Verification
        if (in_array($clientExtension, ['jpg', 'jpeg', 'png'], true)) {
            $imageError = $this->validateImageStructure($realPath, $clientExtension);
            if ($imageError !== null) {
                return $imageError;
            }
        } elseif ($clientExtension === 'pdf') {
            $pdfError = $this->validatePdfStructure($realPath);
            if ($pdfError !== null) {
                return $pdfError;
            }
        } elseif (in_array($clientExtension, ['docx', 'xlsx', 'zip'], true)) {
            $zipError = $this->validateZipArchiveStructure($realPath);
            if ($zipError !== null) {
                return $zipError;
            }
        }

        return null;
    }

    /**
     * Inspect original client filename for suspicious patterns, null bytes, or double extensions.
     */
    protected function validateFilename(string $filename, array $allowedExtensions): ?string
    {
        // Check for null bytes
        if (str_contains($filename, "\0") || str_contains($filename, '%00')) {
            return 'Nama berkas mengandung karakter tidak valid (null byte injection).';
        }

        // Check for directory traversal attempts
        if (str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            return 'Nama berkas mengandung karakter berbahaya (path traversal).';
        }

        // Check all segments for dangerous extensions (e.g. shell.php.jpg, test.phtml.png)
        $parts = explode('.', strtolower($filename));
        if (count($parts) > 2) {
            // Remove the final extension (which should be the allowed one)
            array_pop($parts);
            foreach ($parts as $subPart) {
                if (in_array($subPart, self::DANGEROUS_EXTENSIONS, true)) {
                    Log::warning("Blocked file upload with double extension / dangerous subpart: {$filename}");
                    return 'Berkas ditolak: nama berkas terindikasi memiliki ekstensi ganda yang mencurigakan.';
                }
            }
        }

        return null;
    }

    /**
     * Detect real MIME type using PHP fileinfo.
     */
    protected function detectMimeType(string $path): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);

        return $mime ? strtolower($mime) : 'application/octet-stream';
    }

    /**
     * Validate that detected MIME type matches expected allowed extensions.
     */
    protected function validateMimeType(string $extension, string $detectedMime): ?string
    {
        $allowedMimes = self::MIME_MAP[$extension] ?? [];

        if (empty($allowedMimes)) {
            return null; // No strict map
        }

        if (!in_array($detectedMime, $allowedMimes, true)) {
            Log::warning("MIME type mismatch on upload. Extension: .{$extension}, Detected MIME: {$detectedMime}");
            return "Tipe konten berkas ({$detectedMime}) tidak sesuai dengan ekstensi (.{$extension}).";
        }

        return null;
    }

    /**
     * Validate magic bytes (binary signatures) at the beginning of the file.
     */
    protected function validateMagicBytes(string $extension, string $path): ?string
    {
        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return 'Gagal membaca header berkas.';
        }

        $header = fread($handle, 16);
        fclose($handle);

        if ($header === false || strlen($header) < 4) {
            return 'Header berkas rusak atau terlalu pendek.';
        }

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                // JPEG starts with FF D8 FF
                if (!str_starts_with($header, "\xFF\xD8\xFF")) {
                    return 'Header berkas JPG/JPEG tidak valid (magic bytes mismatch).';
                }
                break;

            case 'png':
                // PNG starts with 89 50 4E 47 0D 0A 1A 0A
                if (!str_starts_with($header, "\x89PNG\r\n\x1a\n")) {
                    return 'Header berkas PNG tidak valid (magic bytes mismatch).';
                }
                break;

            case 'webp':
                // WebP starts with RIFF and has WEBP at offset 8
                if (!str_starts_with($header, "RIFF") || substr($header, 8, 4) !== "WEBP") {
                    return 'Header berkas WEBP tidak valid (magic bytes mismatch).';
                }
                break;

            case 'pdf':
                // PDF must start with %PDF- (within first 1024 bytes)
                $firstKb = @file_get_contents($path, false, null, 0, 1024);
                if ($firstKb === false || !str_contains($firstKb, '%PDF-')) {
                    return 'Header berkas PDF tidak valid (tidak diawali signature %PDF-).';
                }
                break;

            case 'docx':
            case 'xlsx':
            case 'zip':
                // ZIP/OpenXML starts with PK\x03\x04
                if (!str_starts_with($header, "PK\x03\x04")) {
                    return 'Header berkas arsip/dokumen terkompresi tidak valid.';
                }
                break;

            case 'doc':
            case 'xls':
                // OLE Compound File Header D0 CF 11 E0 A1 B1 1A E1
                if (!str_starts_with($header, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1")) {
                    return 'Header dokumen Microsoft Office lama tidak valid.';
                }
                break;
        }

        // Also check if the file starts with executable binary headers (MZ for EXE/DLL, \x7fELF for ELF)
        if (str_starts_with($header, "MZ") || str_starts_with($header, "\x7fELF")) {
            Log::alert("Blocked executable binary upload masked as {$extension}");
            return 'Berkas ditolak karena terdeteksi sebagai berkas biner/executable program.';
        }

        return null;
    }

    /**
     * Deep content scanning for PHP, ASP, Shebang, and malicious script signatures.
     */
    protected function scanForMaliciousContent(string $path): ?string
    {
        // Read up to 8MB of the file for content inspection
        $content = @file_get_contents($path, false, null, 0, 8 * 1024 * 1024);
        if ($content === false) {
            return null;
        }

        foreach (self::DANGEROUS_PATTERNS as $pattern) {
            if (preg_match($pattern, $content)) {
                Log::alert("Blocked malicious payload upload matching pattern {$pattern}");
                return 'Berkas ditolak: isi berkas terdeteksi mengandung kode skrip atau payload yang tidak diizinkan.';
            }
        }

        return null;
    }

    /**
     * Validate image structure using PHP GD library and getimagesize.
     */
    protected function validateImageStructure(string $path, string $extension): ?string
    {
        // 1. getimagesize check
        $imageInfo = @getimagesize($path);
        if ($imageInfo === false || empty($imageInfo[0]) || empty($imageInfo[1])) {
            return 'Berkas gambar rusak atau bukan berkas gambar asli yang valid.';
        }

        $expectedType = match ($extension) {
            'jpg', 'jpeg' => IMAGETYPE_JPEG,
            'png' => IMAGETYPE_PNG,
            default => null,
        };

        if ($expectedType !== null && $imageInfo[2] !== $expectedType) {
            return 'Tipe gambar tidak cocok dengan struktur internal berkas.';
        }

        // 2. GD image rendering verification (verifies image raster stream without corruption)
        if (function_exists('imagecreatefromstring')) {
            $rawContent = @file_get_contents($path);
            if ($rawContent !== false) {
                $gdImg = @imagecreatefromstring($rawContent);
                if ($gdImg === false) {
                    return 'Struktur data gambar rusak atau tidak dapat didekode oleh parser grafis.';
                }
                $w = @imagesx($gdImg);
                $h = @imagesy($gdImg);
                imagedestroy($gdImg);

                if (!$w || !$h || $w <= 0 || $h <= 0) {
                    return 'Dimensi gambar tidak valid atau gambar rusak.';
                }
            }
        }

        return null;
    }

    /**
     * Validate PDF structure.
     */
    protected function validatePdfStructure(string $path): ?string
    {
        $fileSize = filesize($path);
        if ($fileSize < 30) {
            return 'Berkas PDF tidak lengkap atau ukurannya terlalu kecil.';
        }

        // Read last 1024 bytes to check for PDF EOF marker
        $handle = @fopen($path, 'rb');
        if ($handle) {
            $offset = max(0, $fileSize - 1024);
            fseek($handle, $offset);
            $tail = fread($handle, 1024);
            fclose($handle);

            if ($tail !== false && !str_contains($tail, '%%EOF') && !str_contains($tail, 'obj') && !str_contains($tail, 'trailer')) {
                return 'Struktur akhir berkas PDF rusak atau tidak lengkap.';
            }
        }

        return null;
    }

    /**
     * Validate ZIP / OpenXML archive structure.
     */
    protected function validateZipArchiveStructure(string $path): ?string
    {
        if (class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();
            $res = $zip->open($path, \ZipArchive::CHECKCONS);
            if ($res !== true) {
                return 'Arsip dokumen atau file ZIP rusak atau tidak valid.';
            }

            // Check files inside zip archive for malicious script extensions
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = strtolower($zip->getNameIndex($i));
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (in_array($ext, self::DANGEROUS_EXTENSIONS, true)) {
                    $zip->close();
                    Log::alert("Blocked ZIP containing dangerous file: {$filename}");
                    return "Arsip mengandung berkas berbahaya ({$filename}) di dalamnya.";
                }
            }

            $zip->close();
        }

        return null;
    }
}
