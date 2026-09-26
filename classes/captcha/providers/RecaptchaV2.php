<?php

namespace MoonWalkerz\Contact\Classes\Captcha\Providers;

use MoonWalkerz\Contact\Classes\Captcha\Provider;
use MoonWalkerz\Contact\Models\Settings;

/**
 * Google reCAPTCHA v2 (checkbox or invisible badge).
 */
class RecaptchaV2 extends Provider
{
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public function id(): string
    {
        return 'recaptcha_v2';
    }

    /**
     * Falls back to the pre-1.3 settings (captcha / google_api_key /
     * google_secret_key) until the settings are saved again.
     */
    public function config(string $key, $default = null)
    {
        $value = parent::config($key);

        if ($value !== null || Settings::get('captcha_provider')) {
            return $value ?? $default;
        }

        $legacy = match ($key) {
            'site_key'   => Settings::get('google_api_key'),
            'secret_key' => Settings::get('google_secret_key'),
            'mode'       => ((int) Settings::get('captcha') === 1) ? 'invisible' : 'checkbox',
            default      => null,
        };

        return ($legacy === null || $legacy === '') ? $default : $legacy;
    }

    public function isInvisible(): bool
    {
        return $this->config('mode', 'checkbox') === 'invisible';
    }

    public function scriptUrl(): string
    {
        return 'https://www.google.com/recaptcha/api.js?' . http_build_query([
            'render' => 'explicit',
            'onload' => 'mmCaptchaOnload',
            'hl'     => $this->locale(),
        ]);
    }

    public function responseField(): string
    {
        return 'g-recaptcha-response';
    }

    public function render(string $id): string
    {
        return $this->widget($id, [
            'api'    => 'grecaptcha',
            'mode'   => $this->isInvisible() ? 'invisible' : 'checkbox',
            'params' => [
                'sitekey' => (string) $this->config('site_key', ''),
                'theme'   => (string) $this->config('theme', 'light'),
                'size'    => $this->isInvisible() ? 'invisible' : (string) $this->config('size', 'normal'),
                'badge'   => (string) $this->config('badge', 'bottomright'),
            ],
        ], 'mm-captcha-recaptcha');
    }

    public function verify(string $token, array $ctx): void
    {
        $data = $this->siteverify(self::VERIFY_URL, $token, $ctx);

        $this->commonChecks($data['hostname'] ?? null, $token);
    }
}
