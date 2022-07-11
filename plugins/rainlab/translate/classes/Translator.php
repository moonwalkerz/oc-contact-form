<?php

namespace RainLab\Translate\Classes;

use App;
use Config;
use RainLab\Translate\Models\Locale;
use Request;
use Schema;
use Session;

/**
 * Translator class
 *
 * @author Alexey Bobkov, Samuel Georges
 */
class Translator
{
    use \October\Rain\Support\Traits\Singleton;

    const SESSION_LOCALE = 'rainlab.translate.locale';

    const SESSION_CONFIGURED = 'rainlab.translate.configured';

    /**
     * @var string The locale to use on the front end.
     */
    protected $activeLocale;

    /**
     * @var string The default locale if no active is set.
     */
    protected $defaultLocale;

    /**
     * @var bool Determine if translate plugin is configured and ready to be used.
     */
    protected $isConfigured;

    /**
     * Initialize the singleton
     *
     * @return void
     */
    public function init()
    {
        $this->defaultLocale = $this->isConfigured() ? array_get(Locale::getDefault(), 'code', 'en') : 'en';
        $this->activeLocale = $this->defaultLocale;
    }

    /**
     * Changes the locale in the application and optionally stores it in the session.
     *
     * @param  string  $locale   Locale to use
     * @param  bool  $remember Set to false to not store in the session.
     * @return  bool Returns true if the locale exists and is set.
     */
    public function setLocale($locale, $remember = true)
    {
        if (! Locale::isValid($locale)) {
            return false;
        }

        App::setLocale($locale);

        $this->activeLocale = $locale;

        if ($remember) {
            $this->setSessionLocale($locale);
        }

        return true;
    }

    /**
     * Returns the active locale set by this instance.
     *
     * @param  bool  $fromSession Look in the session.
     * @return string
     */
    public function getLocale($fromSession = false)
    {
        if ($fromSession && ($locale = $this->getSessionLocale())) {
            return $locale;
        }

        return $this->activeLocale;
    }

    /**
     * Returns the default locale as set by the application.
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * Check if this plugin is installed and the database is available,
     * stores the result in the session for efficiency.
     *
     * @return bool
     */
    public function isConfigured()
    {
        if ($this->isConfigured !== null) {
            return $this->isConfigured;
        }

        if (Session::has(self::SESSION_CONFIGURED)) {
            $result = true;
        } elseif (App::hasDatabase() && Schema::hasTable('rainlab_translate_locales')) {
            Session::put(self::SESSION_CONFIGURED, true);
            $result = true;
        } else {
            $result = false;
        }

        return $this->isConfigured = $result;
    }

    //
    // Request handling
    //

    /**
     * handleLocaleRoute will check if the route contains a translated locale prefix (/en/)
     * and return that locale to be registered with the router.
     *
     * @return string
     */
    public function handleLocaleRoute()
    {
        if (Config::get('rainlab.translate::disableLocalePrefixRoutes', false)) {
            return '';
        }

        if (App::runningInBackend()) {
            return '';
        }

        if (! $this->isConfigured()) {
            return '';
        }

        if (! $this->loadLocaleFromRequest()) {
            return '';
        }

        $locale = $this->getLocale();
        if (! $locale) {
            return '';
        }

        return $locale;
    }

    /**
     * Sets the locale based on the first URI segment.
     *
     * @return bool
     */
    public function loadLocaleFromRequest()
    {
        $locale = Request::segment(1);

        if (! Locale::isValid($locale)) {
            return false;
        }

        $this->setLocale($locale);

        return true;
    }

    /**
     * Returns the current path prefixed with language code.
     *
     * @param  string  $locale optional language code, default to the system default language
     * @return string
     */
    public function getCurrentPathInLocale($locale = null)
    {
        return $this->getPathInLocale(Request::path(), $locale);
    }

    /**
     * Returns the path prefixed with language code.
     *
     * @param  string  $path Path to rewrite, already translate, with or without locale prefixed
     * @param  string  $locale optional language code, default to the system default language
     * @param  bool  $prefixDefaultLocale should we prefix the path when the locale = default locale
     * @return string
     */
    public function getPathInLocale($path, $locale = null, $prefixDefaultLocale = null)
    {
        $prefixDefaultLocale = (is_null($prefixDefaultLocale))
            ? Config::get('rainlab.translate::prefixDefaultLocale')
            : $prefixDefaultLocale;

        $segments = explode('/', $path);

        $segments = array_values(array_filter($segments, function ($v) {
            return $v != '';
        }));

        if (is_null($locale) || ! Locale::isValid($locale)) {
            $locale = $this->defaultLocale;
        }

        if (count($segments) == 0 || Locale::isValid($segments[0])) {
            $segments[0] = $locale;
        } else {
            array_unshift($segments, $locale);
        }

        // If we don't want te default locale to be prefixed
        // and the first segment equals the default locale
        if (
            ! $prefixDefaultLocale &&
            isset($segments[0]) &&
            $segments[0] == $this->defaultLocale
        ) {
            // Remove the default locale
            array_shift($segments);
        }

        return htmlspecialchars(implode('/', $segments), ENT_QUOTES, 'UTF-8');
    }

    //
    // Session handling
    //

    /**
     * Looks at the session storage to find a locale.
     *
     * @return bool
     */
    public function loadLocaleFromSession()
    {
        $locale = $this->getSessionLocale();

        if (! $locale) {
            return false;
        }

        $this->setLocale($locale);

        return true;
    }

    protected function getSessionLocale()
    {
        if (! Session::has(self::SESSION_LOCALE)) {
            return null;
        }

        return Session::get(self::SESSION_LOCALE);
    }

    protected function setSessionLocale($locale)
    {
        Session::put(self::SESSION_LOCALE, $locale);
    }
}
