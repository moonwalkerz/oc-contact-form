<?php

namespace MoonWalkerz\Contact\Classes\Captcha\Providers;

use MoonWalkerz\Contact\Classes\Captcha\Provider;

/**
 * Cloudflare Turnstile.
 */
class Turnstile extends Provider
{
    const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function id(): string
    {
        return 'turnstile';
    }

    public function scriptUrl(): string
    {
        return 'https://challenges.cloudflare.com/turnstile/v0/api.js?' . http_build_query([
            'render' => 'explicit',
            'onload' => 'mmCaptchaOnload',
        ]);
    }

    public function responseField(): string
    {
        return 'cf-turnstile-response';
    }

    public function render(string $id): string
    {
        return $this->widget($id, [
            'api'    => 'turnstile',
            'mode'   => 'checkbox',
            'params' => [
                'sitekey'             => (string) $this->config('site_key', ''),
                'theme'               => (string) $this->config('theme', 'auto'),
                'size'                => (string) $this->config('size', 'normal'),
                'appearance'          => (string) $this->config('appearance', 'always'),
                'response-field-name' => $this->responseField(),
                'language'            => $this->locale(),
            ],
        ], 'mm-captcha-turnstile');
    }

    public function verify(string $token, array $ctx): void
    {
        $data = $this->siteverify(self::VERIFY_URL, $token, $ctx);

        $this->commonChecks($data['hostname'] ?? null, $token);
    }
}
