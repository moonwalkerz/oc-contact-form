<?php

namespace MoonWalkerz\Contact\Classes\Captcha;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use MoonWalkerz\Contact\Models\Settings;

/**
 * Base class shared by every captcha provider.
 *
 * Settings are stored flat in the plugin Settings model as "{id}_{key}"
 * (e.g. "hcaptcha_site_key").
 */
abstract class Provider
{
    /**
     * Internal identifier, also used as the settings prefix.
     */
    abstract public function id(): string;

    /**
     * POST field that carries the provider token.
     */
    abstract public function responseField(): string;

    /**
     * Widget HTML (without the shared wrapper and extras).
     */
    abstract public function render(string $id): string;

    /**
     * Verifies the submitted token.
     *
     * @throws CaptchaFailedException
     */
    abstract public function verify(string $token, array $ctx): void;

    /**
     * Settings keys that must be filled in for the provider to work.
     */
    public function requiredKeys(): array
    {
        return ['site_key', 'secret_key'];
    }

    /**
     * External script to load, or empty string.
     */
    public function scriptUrl(): string
    {
        return '';
    }

    /**
     * Reads a provider setting, falling back to the default when empty.
     */
    public function config(string $key, $default = null)
    {
        $value = Settings::get($this->id() . '_' . $key);

        return ($value === null || $value === '') ? $default : $value;
    }

    public function isConfigured(): bool
    {
        foreach ($this->requiredKeys() as $key) {
            if (trim((string) $this->config($key, '')) === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Standard widget container read by assets/js/captcha.js.
     */
    protected function widget(string $id, array $data, string $class, string $inner = ''): string
    {
        return sprintf(
            '<div class="mm-captcha-widget %s" id="%s" data-mm-captcha="%s">%s</div>',
            e($class),
            e($id),
            e(json_encode($data)),
            $inner
        );
    }

    /**
     * Attribution note required by Google when the reCAPTCHA badge is hidden.
     */
    protected function googleLegalNote(): string
    {
        return '<style>.grecaptcha-badge{visibility:hidden!important}</style>'
            . '<p class="mm-captcha-legal">' . trans('moonwalkerz.contact::lang.captcha.google_legal', [
                'privacy' => '<a href="https://policies.google.com/privacy" target="_blank" rel="noopener">' . e(trans('moonwalkerz.contact::lang.captcha.google_privacy')) . '</a>',
                'terms'   => '<a href="https://policies.google.com/terms" target="_blank" rel="noopener">' . e(trans('moonwalkerz.contact::lang.captcha.google_terms')) . '</a>',
            ]) . '</p>';
    }

    /**
     * Posts to a verification endpoint and returns the decoded JSON body.
     *
     * @throws CaptchaFailedException
     */
    protected function remoteCall(string $url, array $payload, bool $asJson = false): array
    {
        try {
            $request = Http::timeout(10);
            $response = $asJson
                ? $request->asJson()->post($url, $payload)
                : $request->asForm()->post($url, $payload);
        } catch (ConnectionException $e) {
            throw new CaptchaFailedException(
                trans('moonwalkerz.contact::lang.captcha.error_transport'),
                $e->getMessage(),
                true
            );
        }

        $data = $response->json();

        // Providers also answer 4xx with a JSON verdict (e.g. Turnstile on a bad
        // secret): that is a real rejection, never a transport failure.
        if (! is_array($data)) {
            throw new CaptchaFailedException(
                trans('moonwalkerz.contact::lang.captcha.error_transport'),
                'HTTP ' . $response->status() . ' ' . substr($response->body(), 0, 200),
                true
            );
        }

        return $data;
    }

    /**
     * Shared handling for the classic "siteverify" endpoints
     * (reCAPTCHA, hCaptcha, Turnstile).
     *
     * @throws CaptchaFailedException
     */
    protected function siteverify(string $url, string $token, array $ctx, array $extra = []): array
    {
        if ($token === '') {
            throw new CaptchaFailedException(trans('moonwalkerz.contact::lang.captcha.error_missing'), 'missing token');
        }

        $data = $this->remoteCall($url, array_filter([
            'secret'   => (string) $this->config('secret_key', ''),
            'response' => $token,
            'remoteip' => $ctx['remoteip'] ?? '',
        ] + $extra));

        if (empty($data['success'])) {
            throw new CaptchaFailedException(
                trans('moonwalkerz.contact::lang.captcha.error_failed'),
                'error-codes: ' . implode(',', (array) ($data['error-codes'] ?? []))
            );
        }

        return $data;
    }

    /**
     * Hostname check (optional) and single-use enforcement.
     *
     * @throws CaptchaFailedException
     */
    protected function commonChecks(?string $hostname, string $token): void
    {
        if (Settings::get('captcha_verify_hostname') && $hostname) {
            $expected = request()->getHost();
            if (strcasecmp($hostname, $expected) !== 0) {
                throw new CaptchaFailedException(
                    trans('moonwalkerz.contact::lang.captcha.error_hostname'),
                    "hostname {$hostname} != {$expected}"
                );
            }
        }

        if (! ChallengeStore::claimToken($token)) {
            throw new CaptchaFailedException(trans('moonwalkerz.contact::lang.captcha.error_replay'), 'token replay');
        }
    }

    protected function locale(): string
    {
        return str_replace('_', '-', strtolower(app()->getLocale()));
    }
}
