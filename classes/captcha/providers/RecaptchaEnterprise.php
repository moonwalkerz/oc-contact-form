<?php

namespace MoonWalkerz\Contact\Classes\Captcha\Providers;

use MoonWalkerz\Contact\Classes\Captcha\CaptchaFailedException;
use MoonWalkerz\Contact\Classes\Captcha\Provider;

/**
 * Google reCAPTCHA Enterprise (Google Cloud createAssessment API).
 */
class RecaptchaEnterprise extends Provider
{
    public function id(): string
    {
        return 'recaptcha_enterprise';
    }

    public function requiredKeys(): array
    {
        return ['site_key', 'project_id', 'api_key'];
    }

    public function isScore(): bool
    {
        return $this->config('mode', 'score') === 'score';
    }

    public function scriptUrl(): string
    {
        return 'https://www.google.com/recaptcha/enterprise.js?' . http_build_query([
            'render' => $this->isScore() ? (string) $this->config('site_key', '') : 'explicit',
            'onload' => 'mmCaptchaOnload',
        ]);
    }

    public function responseField(): string
    {
        return 'g-recaptcha-response';
    }

    public function render(string $id): string
    {
        $html = $this->widget($id, [
            'api'    => 'grecaptcha.enterprise',
            'mode'   => $this->isScore() ? 'score' : 'checkbox',
            'params' => [
                'sitekey' => (string) $this->config('site_key', ''),
                'action'  => $this->action(),
                'theme'   => (string) $this->config('theme', 'light'),
            ],
        ], 'mm-captcha-recaptcha');

        return $this->config('hide_badge') ? $html . $this->googleLegalNote() : $html;
    }

    public function verify(string $token, array $ctx): void
    {
        if ($token === '') {
            throw new CaptchaFailedException(trans('moonwalkerz.contact::lang.captcha.error_missing'), 'missing token');
        }

        $url = sprintf(
            'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s',
            rawurlencode((string) $this->config('project_id', '')),
            rawurlencode((string) $this->config('api_key', ''))
        );

        $event = array_filter([
            'token'          => $token,
            'siteKey'        => (string) $this->config('site_key', ''),
            'expectedAction' => $this->action(),
            'userIpAddress'  => $ctx['remoteip'] ?? '',
            'userAgent'      => substr((string) request()->userAgent(), 0, 500),
        ]);

        $data = $this->remoteCall($url, ['event' => $event], true);

        if (isset($data['error'])) {
            throw new CaptchaFailedException(
                trans('moonwalkerz.contact::lang.captcha.error_config'),
                'enterprise API: ' . ($data['error']['message'] ?? 'unknown error')
            );
        }

        $props = $data['tokenProperties'] ?? [];

        if (empty($props['valid'])) {
            throw new CaptchaFailedException(
                trans('moonwalkerz.contact::lang.captcha.error_failed'),
                'invalidReason: ' . ($props['invalidReason'] ?? 'UNKNOWN')
            );
        }

        $expected = $this->action();

        if (isset($props['action']) && $expected !== '' && $props['action'] !== $expected) {
            throw new CaptchaFailedException(
                trans('moonwalkerz.contact::lang.captcha.error_failed'),
                "action {$props['action']} != {$expected}"
            );
        }

        if ($this->isScore()) {
            $threshold = (float) $this->config('score', 0.5);
            $score = (float) ($data['riskAnalysis']['score'] ?? 0.0);

            if ($score < $threshold) {
                throw new CaptchaFailedException(
                    trans('moonwalkerz.contact::lang.captcha.error_score'),
                    sprintf('score %.2f < %.2f %s', $score, $threshold, implode(',', (array) ($data['riskAnalysis']['reasons'] ?? [])))
                );
            }
        }

        $this->commonChecks($props['hostname'] ?? null, $token);
    }

    protected function action(): string
    {
        return (string) $this->config('action', 'contact_form');
    }
}
