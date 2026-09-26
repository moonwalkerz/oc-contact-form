<?php

namespace MoonWalkerz\Contact\Traits;

use Flash;
use MoonWalkerz\Contact\Classes\Captcha\CaptchaFailedException;
use MoonWalkerz\Contact\Classes\Captcha\CaptchaManager;
use MoonWalkerz\Contact\Classes\Captcha\Providers\MathCaptcha;
use October\Rain\Exception\ValidationException;

/**
 * Captcha support for form components.
 *
 * Templates print the field with {{ __SELF__.captchaField()|raw }} and an
 * error placeholder with data-validate-for="captcha".
 */
trait HandlesCaptcha
{
    /**
     * Registers the front-end assets. Call from onRun().
     */
    protected function addCaptchaAssets(): void
    {
        $provider = CaptchaManager::provider();

        if (! $provider && ! CaptchaManager::honeypotEnabled()) {
            return;
        }

        $this->addCss('assets/css/captcha.css');

        if (! $provider) {
            return;
        }

        // Both deferred so our loader always runs before the provider's onload callback.
        $this->addJs('assets/js/captcha.js', ['defer' => 'defer']);

        if ($url = $provider->scriptUrl()) {
            $this->addJs($url, ['defer' => 'defer']);
        }
    }

    public function captchaField(): string
    {
        return CaptchaManager::render($this->alias . '::onRefreshCaptcha');
    }

    /**
     * Throws a validation error on the "captcha" field when verification fails.
     */
    protected function verifyCaptcha(): void
    {
        try {
            CaptchaManager::verify(post());
        } catch (CaptchaFailedException $e) {
            Flash::error($e->getMessage());
            throw new ValidationException(['captcha' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: new math question after a failed submission.
     */
    public function onRefreshCaptcha()
    {
        $provider = CaptchaManager::provider();

        if (! $provider instanceof MathCaptcha) {
            return [];
        }

        return $provider->freshChallenge();
    }
}
