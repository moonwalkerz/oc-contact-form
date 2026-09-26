<?php

namespace MoonWalkerz\Contact\Classes\Captcha;

use Exception;

/**
 * Thrown by providers when verification fails. The message is shown to the
 * visitor; the detail is only logged.
 */
class CaptchaFailedException extends Exception
{
    public string $detail;

    /**
     * True when the provider could not be reached (eligible for fail-open).
     */
    public bool $transport;

    public function __construct(string $message, string $detail = '', bool $transport = false)
    {
        parent::__construct($message);
        $this->detail = $detail;
        $this->transport = $transport;
    }
}
