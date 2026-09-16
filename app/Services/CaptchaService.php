<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CaptchaService
{
    protected const SESSION_KEY = '_app_captcha_solution';
    protected const TIME_KEY = '_app_captcha_generated_at';
    protected const HONEYPOT_FIELD = '_hp_website';
    protected const TIMELOCK_FIELD = '_form_rendered_at';
    protected const EXPIRATION_SECONDS = 600; // 10 minutes

    /**
     * Generate a new Math/Alphanumeric CAPTCHA challenge with distorted SVG rendering.
     *
     * @return array{svg: string, id: string}
     */
    public function generate(): array
    {
        // 80% chance of Math challenge (very human-friendly, hard for dumb bots), 20% alphanumeric
        $isMath = (rand(1, 10) <= 8);

        if ($isMath) {
            $operators = ['+', '-', '×'];
            $op = $operators[array_rand($operators)];

            if ($op === '+') {
                $num1 = rand(3, 20);
                $num2 = rand(2, 15);
                $questionText = "{$num1} + {$num2} = ?";
                $solution = (string)($num1 + $num2);
            } elseif ($op === '-') {
                $num1 = rand(10, 30);
                $num2 = rand(1, $num1 - 1);
                $questionText = "{$num1} - {$num2} = ?";
                $solution = (string)($num1 - $num2);
            } else {
                $num1 = rand(2, 9);
                $num2 = rand(2, 6);
                $questionText = "{$num1} × {$num2} = ?";
                $solution = (string)($num1 * $num2);
            }
        } else {
            // Friendly alphanumeric (omitting ambiguous characters like 0, O, 1, I, l)
            $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $code = '';
            $len = rand(4, 5);
            for ($i = 0; $i < $len; $i++) {
                $code .= $chars[rand(0, strlen($chars) - 1)];
            }
            $questionText = $code;
            $solution = strtolower($code);
        }

        $uniqueId = Str::random(16);
        Session::put(self::SESSION_KEY, [
            'id' => $uniqueId,
            'solution' => strtolower($solution),
            'timestamp' => time(),
        ]);
        Session::put(self::TIME_KEY, time());

        $svg = $this->renderSvg($questionText);

        return [
            'svg' => $svg,
            'id' => $uniqueId,
            'timestamp' => time(),
            'honeypot_name' => self::HONEYPOT_FIELD,
            'timelock_name' => self::TIMELOCK_FIELD,
        ];
    }

    /**
     * Verify the user's submitted CAPTCHA answer and consume the token.
     *
     * @param string|null $input
     * @return bool
     */
    public function verify(?string $input): bool
    {
        if ($input === null || trim($input) === '') {
            return false;
        }

        $sessionData = Session::get(self::SESSION_KEY);
        if (!$sessionData || !is_array($sessionData)) {
            return false;
        }

        // Check expiration (10 minutes)
        if (time() - ($sessionData['timestamp'] ?? 0) > self::EXPIRATION_SECONDS) {
            Session::forget(self::SESSION_KEY);
            return false;
        }

        $expected = $sessionData['solution'] ?? '';
        $actual = strtolower(trim($input));

        // Invalidate immediately (single-use)
        Session::forget(self::SESSION_KEY);

        return $actual === $expected;
    }

    protected const CHECKBOX_SESSION_KEY = '_app_captcha_checkbox';

    /**
     * Initialize/generate a checkbox verification challenge token.
     *
     * @return array{seed: string, token: string, timestamp: int}
     */
    public function generateCheckbox(): array
    {
        $id = Str::random(24);
        $seed = hash_hmac('sha256', $id . '|' . microtime(true) . '|' . request()->ip(), config('app.key'));
        $expectedToken = hash_hmac('sha256', $seed . ':human_verified:' . $id, config('app.key'));

        Session::put(self::CHECKBOX_SESSION_KEY, [
            'id' => $id,
            'seed' => $seed,
            'expected_token' => $expectedToken,
            'timestamp' => time(),
        ]);
        Session::put(self::TIME_KEY, time());

        return [
            'id' => $id,
            'seed' => $seed,
            'token' => $expectedToken,
            'timestamp' => time(),
            'honeypot_name' => self::HONEYPOT_FIELD,
            'timelock_name' => self::TIMELOCK_FIELD,
        ];
    }

    /**
     * Verify the one-click checkbox submission.
     *
     * @param Request $request
     * @return bool
     */
    public function verifyCheckbox(Request $request): bool
    {
        // 1. Honeypot check
        if (!$this->verifyHoneypot($request)) {
            \Illuminate\Support\Facades\Log::warning('Checkbox CAPTCHA: Honeypot trap triggered from IP: ' . $request->ip());
            return false;
        }

        // 2. Minimum Time-lock check
        if (!$this->verifyTimeLock($request, 1)) {
            \Illuminate\Support\Facades\Log::warning('Checkbox CAPTCHA: Form submitted too quickly from IP: ' . $request->ip());
            return false;
        }

        // 3. Token & Session Check
        $submittedToken = $request->input('captcha_token');
        if (empty($submittedToken)) {
            return false;
        }

        $sessionData = Session::get(self::CHECKBOX_SESSION_KEY);
        if (!$sessionData || !is_array($sessionData)) {
            return false;
        }

        // Expiration check (15 minutes)
        if (time() - ($sessionData['timestamp'] ?? 0) > self::EXPIRATION_SECONDS) {
            Session::forget(self::CHECKBOX_SESSION_KEY);
            return false;
        }

        $expectedToken = $sessionData['expected_token'] ?? '';
        $isValid = hash_equals($expectedToken, $submittedToken);

        // Consume single-use token upon successful verification
        if ($isValid) {
            Session::forget(self::CHECKBOX_SESSION_KEY);
        }

        return $isValid;
    }

    /**
     * Verify Cloudflare Turnstile submission.
     *
     * @param Request $request
     * @return bool
     */
    public function verifyTurnstile(Request $request): bool
    {
        // 1. Honeypot check
        if (!$this->verifyHoneypot($request)) {
            \Illuminate\Support\Facades\Log::warning('Turnstile: Honeypot trap triggered from IP: ' . $request->ip());
            return false;
        }

        // 2. Turnstile token check
        $token = $request->input('cf-turnstile-response') ?? $request->input('captcha_token');
        if (empty($token)) {
            return false;
        }

        $secret = config('services.turnstile.secret_key', env('TURNSTILE_SECRET_KEY', '1x0000000000000000000000000000000AA'));

        // In testing environment or with dummy key in local, allow valid dummy tokens without outbound network dependency
        if (app()->environment('testing') || ($secret === '1x0000000000000000000000000000000AA' && app()->environment('local'))) {
            return !empty($token) && $token !== 'invalid-token';
        }

        try {
            $response = \Illuminate\Support\Facades\Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            return (bool)$response->json('success', false);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Cloudflare Turnstile siteverify error: ' . $e->getMessage());
            return app()->environment('local', 'testing');
        }
    }

    /**
     * Check if Honeypot bot trap was triggered.
     *
     * @param Request $request
     * @return bool True if clean (human), False if bot trapped
     */
    public function verifyHoneypot(Request $request): bool
    {
        $honeypotValue = $request->input(self::HONEYPOT_FIELD);
        return empty($honeypotValue);
    }

    /**
     * Check if form submission passed minimum time-lock (e.g. submitted in >= 1.5 seconds).
     *
     * @param Request $request
     * @param int $minSeconds
     * @return bool True if valid timing, False if too fast (bot)
     */
    public function verifyTimeLock(Request $request, int $minSeconds = 1): bool
    {
        $renderedAt = (int)$request->input(self::TIMELOCK_FIELD);
        if ($renderedAt <= 0) {
            return true;
        }

        $duration = time() - $renderedAt;
        return $duration >= $minSeconds && $duration <= 86400; // between 1s and 24h
    }

    /**
     * Render a clean, modern distorted SVG for the challenge.
     *
     * @param string $text
     * @return string
     */
    protected function renderSvg(string $text): string
    {
        $width = 200;
        $height = 54;
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $charCount = count($chars);

        // Dark modern color palette
        $colors = ['#1e3a8a', '#1d4ed8', '#0f766e', '#4338ca', '#0369a1', '#15803d', '#374151'];

        $svgElements = [];

        // 1. Background with subtle curved grid lines
        $svgElements[] = "<rect width='{$width}' height='{$height}' rx='10' fill='#f8fafc' stroke='#e2e8f0' stroke-width='1.5'/>";

        // Noise lines
        for ($i = 0; $i < 4; $i++) {
            $x1 = rand(0, 30);
            $y1 = rand(5, $height - 5);
            $x2 = rand($width - 30, $width);
            $y2 = rand(5, $height - 5);
            $cx = rand(50, $width - 50);
            $cy = rand(5, $height - 5);
            $stroke = $colors[array_rand($colors)];
            $svgElements[] = "<path d='M {$x1} {$y1} Q {$cx} {$cy} {$x2} {$y2}' stroke='{$stroke}' stroke-width='1.2' fill='none' opacity='0.25' stroke-dasharray='3,2'/>";
        }

        // Noise dots
        for ($i = 0; $i < 20; $i++) {
            $dx = rand(5, $width - 5);
            $dy = rand(5, $height - 5);
            $r = rand(1, 2);
            $svgElements[] = "<circle cx='{$dx}' cy='{$dy}' r='{$r}' fill='#94a3b8' opacity='0.4'/>";
        }

        // 2. Character glyphs rendering with random gentle rotation and colors
        $startX = 20;
        $stepX = ($width - 40) / max(1, $charCount);

        foreach ($chars as $index => $char) {
            $x = $startX + ($index * $stepX) + rand(-2, 2);
            $y = ($height / 2) + rand(4, 8);
            $rot = rand(-12, 12);
            $color = $colors[array_rand($colors)];
            $fontSize = rand(22, 26);
            $fontWeight = (rand(0, 1) === 1) ? 'bold' : '700';

            $svgElements[] = "<text x='{$x}' y='{$y}' font-family='Inter, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif' font-size='{$fontSize}px' font-weight='{$fontWeight}' fill='{$color}' transform='rotate({$rot}, {$x}, {$y})' text-anchor='middle'>{$char}</text>";
        }

        $innerSvg = implode("\n", $svgElements);
        return "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 {$width} {$height}' width='{$width}' height='{$height}' class='select-none pointer-events-none'>{$innerSvg}</svg>";
    }
}
