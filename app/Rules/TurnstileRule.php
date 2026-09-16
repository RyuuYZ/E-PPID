<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Closure;

class TurnstileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('Verifikasi CAPTCHA wajib diisi.');
            return;
        }

        $secretKey = config('services.turnstile.secret_key');

        // In testing environment or with dummy key in local, allow valid dummy tokens without outbound network dependency
        if (app()->environment('testing') || ($secretKey === '1x0000000000000000000000000000000AA' && app()->environment('local'))) {
            if ($value === 'invalid-token') {
                $fail('Verifikasi CAPTCHA gagal. Silakan coba lagi.');
            }
            return;
        }

        $response = Http::asForm()
            ->withoutVerifying() // Disable SSL check for local development
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
            ]);

        if (! $response->json('success')) {
            $fail('Verifikasi CAPTCHA gagal. Silakan coba lagi.');
        }
    }
}
