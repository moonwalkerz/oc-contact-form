<?php

namespace MoonWalkerz\Contact\Updates;

use MoonWalkerz\Contact\Models\Settings;
use October\Rain\Database\Updates\Migration;

/**
 * Moves the pre-1.3 reCAPTCHA settings (captcha 0/1/2, google_api_key,
 * google_secret_key) to the multi-provider captcha settings.
 */
class MigrateCaptchaSettings extends Migration
{
    public function up()
    {
        if (! Settings::isConfigured() || Settings::get('captcha_provider')) {
            return;
        }

        $legacy = (int) Settings::get('captcha');

        Settings::set([
            'captcha_provider'        => $legacy > 0 ? 'recaptcha_v2' : 'none',
            'recaptcha_v2_mode'       => $legacy === 1 ? 'invisible' : 'checkbox',
            'recaptcha_v2_site_key'   => (string) Settings::get('google_api_key'),
            'recaptcha_v2_secret_key' => (string) Settings::get('google_secret_key'),
        ]);
    }

    public function down()
    {
    }
}
