<?php

namespace MoonWalkerz\Contact\Classes\Captcha\Providers;

use MoonWalkerz\Contact\Classes\Captcha\Provider;

/**
 * hCaptcha (checkbox or invisible).
 */
class HCaptcha extends Provider
{
    const VERIFY_URL = 'https://api.hcaptcha.com/siteverify';

    public function id(): string
    {
        return 'hcaptcha';
    }

    public function isInvisible(): bool
    {
        return $this->config('mode', 'normal') === 'invisible';
    }

    public function scriptUrl(): string
    {
        return 'https://js.hcaptcha.com/1/api.js?' . http_build_query([
            'render' => 'explicit',
            'onload' => 'mmCaptchaOnload',
            'hl'     => $this->locale(),
        ]);
    }

    public function responseField(): string
    {
        return 'h-captcha-response';
    }

    public function render(string $id): string
    {
        return $this->widget($id, [
            'api'    => 'hcaptcha',
            'mode'   => $this->isInvisible() ? 'invisible' : 'checkbox',
            'params' => [
                'sitekey' => (string) $this->config('site_key', ''),
                'theme'   => (string) $this->config('theme', 'light'),
                'size'    => $this->isInvisible() ? 'invisible' : (string) $this->config('size', 'normal'),
            ],
        ], 'mm-captcha-hcaptcha');
    }

    public function verify(string $token, array $ctx): void
    {
        $data = $this->siteverify(self::VERIFY_URL, $token, $ctx, [
            'sitekey' => (string) $this->config('site_key', ''),
        ]);

        $this->commonChecks($data['hostname'] ?? null, $token);
    }
}
