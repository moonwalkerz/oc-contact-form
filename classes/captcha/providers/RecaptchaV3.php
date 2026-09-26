<?php

namespace MoonWalkerz\Contact\Classes\Captcha\Providers;

use MoonWalkerz\Contact\Classes\Captcha\CaptchaFailedException;
use MoonWalkerz\Contact\Classes\Captcha\Provider;

/**
 * Google reCAPTCHA v3 (score based, no interaction).
 */
class RecaptchaV3 extends Provider
{
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public function id(): string
    {
        return 'recaptcha_v3';
    }

    public function scriptUrl(): string
    {
        return 'https://www.google.com/recaptcha/api.js?' . http_build_query([
            'render' => (string) $this->config('site_key', ''),
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
            'api'    => 'grecaptcha',
            'mode'   => 'score',
            'params' => [
                'sitekey' => (string) $this->config('site_key', ''),
                'action'  => $this->action(),
            ],
        ], 'mm-captcha-score');

        return $this->config('hide_badge') ? $html . $this->googleLegalNote() : $html;
    }

    public function verify(string $token, array $ctx): void
    {
        $data = $this->siteverify(self::VERIFY_URL, $token, $ctx);

        $expected = $this->action();

        if ($this->config('verify_action', true) && isset($data['action']) && $data['action'] !== $expected) {
            throw new CaptchaFailedException(
                trans('moonwalkerz.contact::lang.captcha.error_failed'),
                "action {$data['action']} != {$expected}"
            );
        }

        $threshold = (float) $this->config('score', 0.5);
        $score = (float) ($data['score'] ?? 0.0);

        if ($score < $threshold) {
            throw new CaptchaFailedException(
                trans('moonwalkerz.contact::lang.captcha.error_score'),
                sprintf('score %.2f < %.2f', $score, $threshold)
            );
        }

        $this->commonChecks($data['hostname'] ?? null, $token);
    }

    protected function action(): string
    {
        return (string) $this->config('action', 'contact_form');
    }
}
