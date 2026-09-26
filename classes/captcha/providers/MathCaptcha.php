<?php

namespace MoonWalkerz\Contact\Classes\Captcha\Providers;

use MoonWalkerz\Contact\Classes\Captcha\CaptchaFailedException;
use MoonWalkerz\Contact\Classes\Captcha\ChallengeStore;
use MoonWalkerz\Contact\Classes\Captcha\Provider;

/**
 * Built-in math captcha: no external service, no keys.
 */
class MathCaptcha extends Provider
{
    public function id(): string
    {
        return 'math';
    }

    public function requiredKeys(): array
    {
        return [];
    }

    public function responseField(): string
    {
        return 'mm_captcha_answer';
    }

    public function freshChallenge(): array
    {
        return ChallengeStore::create((int) $this->config('ttl', 600));
    }

    public function render(string $id): string
    {
        $challenge = $this->freshChallenge();
        $label = (string) $this->config('challenge_text', trans('moonwalkerz.contact::lang.captcha.math_default_text'));
        $fieldId = $id . '-answer';

        $inner = sprintf(
            '<label class="mm-captcha-math-label" for="%s">%s <span class="mm-captcha-math-question">%s</span> =</label> ',
            e($fieldId),
            e($label),
            e($challenge['question'])
        );
        $inner .= sprintf(
            '<input type="text" class="mm-captcha-math-input form-control" id="%s" name="%s" value="" size="4" inputmode="numeric" autocomplete="off" required>',
            e($fieldId),
            e($this->responseField())
        );
        $inner .= sprintf(
            '<input type="hidden" class="mm-captcha-math-id" name="mm_captcha_cid" value="%s">',
            e($challenge['id'])
        );

        return $this->widget($id, ['mode' => 'math'], 'mm-captcha-math', $inner);
    }

    public function verify(string $token, array $ctx): void
    {
        $answer = $ctx['post']['mm_captcha_answer'] ?? '';
        $cid = $ctx['post']['mm_captcha_cid'] ?? '';

        if (! is_scalar($answer) || ! is_scalar($cid)) {
            throw new CaptchaFailedException(trans('moonwalkerz.contact::lang.captcha.math_wrong'), 'malformed');
        }

        if (trim((string) $answer) === '') {
            throw new CaptchaFailedException(trans('moonwalkerz.contact::lang.captcha.math_empty'), 'empty answer');
        }

        $outcome = ChallengeStore::consume((string) $cid, (string) $answer);

        if ($outcome === true) {
            return;
        }

        $message = match ($outcome) {
            'expired' => trans('moonwalkerz.contact::lang.captcha.math_expired'),
            'replay', 'invalid' => trans('moonwalkerz.contact::lang.captcha.math_invalid'),
            default => trans('moonwalkerz.contact::lang.captcha.math_wrong'),
        };

        throw new CaptchaFailedException($message, 'math: ' . $outcome);
    }
}
