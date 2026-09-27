<?php

namespace MoonWalkerz\Contact\Classes\Captcha\Providers;

use MoonWalkerz\Contact\Classes\Captcha\CaptchaFailedException;
use MoonWalkerz\Contact\Classes\Captcha\Provider;

/**
 * Cloudflare Turnstile.
 *
 * Keys come from the plugin settings or, when those are empty, from the
 * TURNSTILE_SITE_KEY / TURNSTILE_SECRET environment variables.
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

    protected function envAliases(): array
    {
        return [
            'site_key'   => ['TURNSTILE_SITE_KEY', 'TURNSTILE_SITEKEY'],
            'secret_key' => ['TURNSTILE_SECRET'],
        ];
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
                'action'              => $this->action(),
            ],
        ], 'mm-captcha-turnstile');
    }

    public function verify(string $token, array $ctx): void
    {
        $data = $this->siteverify(self::VERIFY_URL, $token, $ctx);

        // The token must have been issued for this form: siteverify echoes
        // the "action" the widget was rendered with. Cloudflare's testing
        // keys (1x0000…AA / 2x0000…AA) do not echo it, so they are exempt.
        $expected = $this->action();
        $testingKey = ! empty($data['metadata']['result_with_testing_key']);

        if ($expected !== '' && ! $testingKey && (($data['action'] ?? '') !== $expected)) {
            throw new CaptchaFailedException(
                trans('moonwalkerz.contact::lang.captcha.error_failed'),
                'action ' . ($data['action'] ?? '(none)') . " != {$expected}"
            );
        }

        $this->commonChecks($data['hostname'] ?? null, $token);
    }
}
