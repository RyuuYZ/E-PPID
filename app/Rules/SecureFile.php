<?php

namespace App\Rules;

use App\Services\FileSecurityService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SecureFile implements ValidationRule
{
    protected array $allowedExtensions;
    protected int $maxKilobytes;

    /**
     * @param array $allowedExtensions Whitelist of allowed extensions (e.g. ['jpg', 'jpeg', 'png', 'pdf'])
     * @param int $maxKilobytes Maximum file size in kilobytes (e.g. 5120 for 5MB)
     */
    public function __construct(array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'], int $maxKilobytes = 5120)
    {
        $this->allowedExtensions = $allowedExtensions;
        $this->maxKilobytes = $maxKilobytes;
    }

    /**
     * Factory for Identitas uploads (KTP, SIM, Akta) - Max 5MB (JPG, JPEG, PNG, PDF)
     */
    public static function identitas(): self
    {
        return new self(['jpg', 'jpeg', 'png', 'pdf'], 5120);
    }

    /**
     * Factory for DIP official document uploads - Max 30MB (PDF)
     */
    public static function dokumen(): self
    {
        return new self(['pdf'], 30720);
    }

    /**
     * Factory for Keberatan supporting documents - Max 10MB (PDF, JPG, JPEG, PNG)
     */
    public static function keberatan(): self
    {
        return new self(['pdf', 'jpg', 'jpeg', 'png'], 10240);
    }

    /**
     * Factory for Unit Pengolah assignment submit data - Max 15MB (PDF, DOC, DOCX, XLS, XLSX, CSV, ZIP, JPG, JPEG, PNG)
     */
    public static function penugasan(): self
    {
        return new self(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'zip', 'jpg', 'jpeg', 'png'], 15360);
    }

    /**
     * Factory for profile photo uploads - Max 2MB (JPG, JPEG, PNG)
     */
    public static function profilePhoto(): self
    {
        return new self(['jpg', 'jpeg', 'png'], 2048);
    }

    /**
     * Factory for Carousel / Banner uploads - Max 5MB (JPG, JPEG, PNG, WEBP)
     */
    public static function carousel(): self
    {
        return new self(['jpg', 'jpeg', 'png', 'webp'], 5120);
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('Berkas yang diunggah tidak valid.');
            return;
        }

        $securityService = app(FileSecurityService::class);
        $errorMessage = $securityService->validateSecureFile(
            $value,
            $this->allowedExtensions,
            $this->maxKilobytes
        );

        if ($errorMessage !== null) {
            $fail($errorMessage);
        }
    }
}
