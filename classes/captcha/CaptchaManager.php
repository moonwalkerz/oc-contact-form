<?php

namespace MoonWalkerz\Contact\Classes\Captcha;

use Illuminate\Support\Facades\Log;
use MoonWalkerz\Contact\Classes\Captcha\Providers\HCaptcha;
use MoonWalkerz\Contact\Classes\Captcha\Providers\MathCaptcha;
use MoonWalkerz\Contact\Classes\Captcha\Providers\RecaptchaEnterprise;
use MoonWalkerz\Contact\Classes\Captcha\Providers\RecaptchaV2;
use MoonWalkerz\Contact\Classes\Captcha\Providers\RecaptchaV3;
use MoonWalkerz\Contact\Classes\Captcha\Providers\Turnstile;
use MoonWalkerz\Contact\Models\Settings;

/**
 * Entry point used by the form components.
 */
class CaptchaManager
{
    const PROVIDERS = [
        'recaptcha_v2'         => RecaptchaV2::class,
        'recaptcha_v3'         => RecaptchaV3::class,
        'recaptcha_enterprise' => RecaptchaEnterprise::class,
        'hcaptcha'             => HCaptcha::class,
        'turnstile'            => Turnstile::class,
        'math'                 => MathCaptcha::class,
    ];

    protected static int $instances = 0;

    /**
     * Selected provider id, or "none".
     */
    public static function activeId(): string
    {
        $id = Settings::get('captcha_provider');

        // Settings saved before 1.3.0: captcha 1 = invisible v2, 2 = checkbox v2.
        if ($id === null || $id === '') {
            return (int) Settings::get('captcha') > 0 ? 'recaptcha_v2' : 'none';
        }

        return array_key_exists($id, self::PROVIDERS) ? $id : 'none';
    }

    /**
     * Active and fully configured provider, or null.
     */
    public static function provider(): ?Provider
    {
        $id = self::activeId();

        if ($id === 'none') {
            return null;
        }

        $class = self::PROVIDERS[$id];
        $provider = new $class;

        if (! $provider->isConfigured()) {
            Log::warning("Contact form captcha \"{$id}\" is selected but its keys are missing: captcha disabled.");

            return null;
        }

        return $provider;
    }

    public static function honeypotEnabled(): bool
    {
        return (bool) Settings::get('captcha_honeypot', true);
    }

    public static function timeTrap(): int
    {
        return max(0, (int) Settings::get('captcha_time_trap', 3));
    }

    /**
     * Full captcha HTML: widget, token field, honeypot and time trap.
     *
     * @param string $refreshHandler AJAX handler used by the math captcha to fetch a new question.
     * @param string $action         Name of the protected surface (letters, digits, "_" and "-", max 32 chars).
     */
    public static function render(string $refreshHandler = '', string $action = ''): string
    {
        $provider = self::provider();
        $html = '';

        if ($provider) {
            self::$instances++;
            $html .= $provider->withAction(self::action($action))->render('mm-captcha-' . self::$instances);
            $html .= '<input type="hidden" name="mm_captcha_token" class="mm-captcha-token" value="" autocomplete="off">';
        }

        if (self::honeypotEnabled()) {
            $name = ChallengeStore::honeypotName();
            $html .= '<div class="mm-captcha-hp" aria-hidden="true">'
                . '<label for="' . e($name) . '-' . self::$instances . '">' . e(trans('moonwalkerz.contact::lang.captcha.honeypot_label')) . '</label>'
                . '<input type="text" id="' . e($name) . '-' . self::$instances . '" name="' . e($name) . '" value="" tabindex="-1" autocomplete="off">'
                . '</div>';
        }

        if (self::timeTrap() > 0) {
            $html .= '<input type="hidden" name="mm_captcha_ts" value="' . e(ChallengeStore::signTimestamp()) . '">';
        }

        if ($html === '') {
            return '';
        }

        return '<div class="mm-captcha-field" data-mm-refresh="' . e($refreshHandler) . '">' . $html . '</div>';
    }

    /**
     * Verifies the submitted form.
     *
     * @param string $action Must match the action the widget was rendered with.
     *
     * @throws CaptchaFailedException
     */
    public static function verify(array $post, string $action = ''): void
    {
        try {
            self::checkExtras($post);

            $provider = self::provider();

            if (! $provider) {
                return;
            }

            try {
                $provider->withAction(self::action($action))->verify(self::token($provider, $post), [
                    'post'     => $post,
                    'remoteip' => request()->ip(),
                ]);
            } catch (CaptchaFailedException $e) {
                if ($e->transport && Settings::get('captcha_fail_open')) {
                    Log::warning('Contact form captcha unreachable, accepted (fail-open): ' . $e->detail);

                    return;
                }

                throw $e;
            }
        } catch (CaptchaFailedException $e) {
            Log::info('Contact form captcha rejected: ' . ($e->detail ?: $e->getMessage()), ['ip' => request()->ip()]);

            throw $e;
        }
    }

    /**
     * Honeypot and minimum fill time.
     *
     * @throws CaptchaFailedException
     */
    protected static function checkExtras(array $post): void
    {
        if (self::honeypotEnabled()) {
            $value = $post[ChallengeStore::honeypotName()] ?? '';

            if (! is_scalar($value) || trim((string) $value) !== '') {
                throw new CaptchaFailedException(trans('moonwalkerz.contact::lang.captcha.error_spam'), 'honeypot filled');
            }
        }

        $min = self::timeTrap();

        if ($min > 0 && isset($post['mm_captcha_ts'])) {
            $age = ChallengeStore::readTimestamp($post['mm_captcha_ts']);

            if ($age !== false && $age < $min) {
                throw new CaptchaFailedException(
                    trans('moonwalkerz.contact::lang.captcha.error_too_fast'),
                    "submitted after {$age}s (min {$min}s)"
                );
            }
        }
    }

    /**
     * Normalises an action name to what the providers accept.
     */
    protected static function action(string $action): string
    {
        $action = preg_replace('/[^A-Za-z0-9_-]/', '_', $action);

        return substr((string) $action, 0, 32);
    }

    protected static function token(Provider $provider, array $post): string
    {
        foreach (['mm_captcha_token', $provider->responseField()] as $field) {
            if (isset($post[$field]) && is_scalar($post[$field]) && trim((string) $post[$field]) !== '') {
                return trim((string) $post[$field]);
            }
        }

        return '';
    }
}
