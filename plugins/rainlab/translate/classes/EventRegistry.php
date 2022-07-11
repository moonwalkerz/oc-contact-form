<?php

namespace RainLab\Translate\Classes;

use App;
use Cms\Classes\Content;
use Cms\Classes\Page;
use Exception;
use File;
use RainLab\Translate\Models\Locale as LocaleModel;
use RainLab\Translate\Models\Message;
use Str;
use System\Classes\MailManager;
use System\Classes\PluginManager;

/**
 * EventRegistry for bootstrapping events
 *
 * @author Alexey Bobkov, Samuel Georges
 */
class EventRegistry
{
    use \October\Rain\Support\Traits\Singleton;

    //
    // Editor
    //

    /**
     * extendEditorPageToolbar
     */
    public function extendEditorPageToolbar($dataHolder)
    {
        if (! LocaleModel::isAvailable()) {
            return;
        }

        $locales = LocaleModel::listAvailable();
        $defaultLocale = LocaleModel::getDefault()->code ?? null;

        $properties = [];
        foreach ($locales as $locale => $label) {
            if ($locale == $defaultLocale) {
                continue;
            }

            $properties[] = [
                'property' => 'localeUrl.'.$locale,
                'title' => 'cms::lang.editor.url',
                'tab' => $label,
                'type' => 'string',
            ];

            $properties[] = [
                'property' => 'localeTitle.'.$locale,
                'title' => 'cms::lang.editor.title',
                'tab' => $label,
                'type' => 'string',
            ];

            $properties[] = [
                'property' => 'localeDescription.'.$locale,
                'title' => 'cms::lang.editor.description',
                'tab' => $label,
                'type' => 'text',
            ];

            $properties[] = [
                'property' => 'localeMeta_title.'.$locale,
                'title' => 'cms::lang.editor.meta_title',
                'tab' => $label,
                'type' => 'string',
            ];

            $properties[] = [
                'property' => 'localeMeta_description.'.$locale,
                'title' => 'cms::lang.editor.meta_description',
                'tab' => $label,
                'type' => 'text',
            ];
        }

        $dataHolder->buttons[] = [
            'button' => 'rainlab.translate::lang.plugin.name',
            'icon' => 'octo-icon-globe',
            'popupTitle' => 'Translate Page Properties',
            'properties' => $properties,
        ];
    }

    //
    // Utility
    //

    /**
     * registerFormFieldReplacements
     */
    public function registerFormFieldReplacements($widget)
    {
        // Replace with ML Controls for translatable attributes
        $this->registerModelTranslation($widget);

        // Handle URL translations
        $this->registerPageUrlTranslation($widget);

        // Handle RainLab.Pages MenuItem translations
        if (PluginManager::instance()->exists('RainLab.Pages')) {
            $this->registerMenuItemTranslation($widget);
        }
    }

    /**
     * registerMenuItemTranslation for RainLab.Pages MenuItem data
     *
     * @param  Backend\Widgets\Form  $widget
     */
    public function registerMenuItemTranslation($widget)
    {
        if ($widget->model instanceof \RainLab\Pages\Classes\MenuItem) {
            $defaultLocale = LocaleModel::getDefault();
            $availableLocales = LocaleModel::listAvailable();
            $fieldsToTranslate = ['title', 'url'];

            // Replace specified fields with multilingual versions
            foreach ($fieldsToTranslate as $fieldName) {
                $widget->fields[$fieldName]['type'] = 'mltext';

                foreach ($availableLocales as $code => $locale) {
                    if (! $defaultLocale || $defaultLocale->code === $code) {
                        continue;
                    }

                    // Add data locker fields for the different locales under the `viewBag[locale]` property
                    $widget->fields["viewBag[locale][$code][$fieldName]"] = [
                        'cssClass' => 'hidden',
                        'attributes' => [
                            'data-locale' => $code,
                            'data-field-name' => $fieldName,
                        ],
                    ];
                }
            }
        }
    }

    //
    // Translate URLs
    //

    /**
     * registerPageUrlTranslation
     */
    public function registerPageUrlTranslation($widget)
    {
        if (! $model = $widget->model) {
            return;
        }

        if (
            $model instanceof Page &&
            isset($widget->fields['settings[url]'])
        ) {
            $widget->fields['settings[url]']['type'] = 'mltext';
        } elseif (
            $model instanceof \RainLab\Pages\Classes\Page &&
            isset($widget->fields['viewBag[url]'])
        ) {
            $widget->fields['viewBag[url]']['type'] = 'mltext';
        }
    }

    //
    // Translatable behavior
    //

    /**
     * registerModelTranslation automatically replaces form fields for multi-lingual equivalents
     */
    public function registerModelTranslation($widget)
    {
        if ($widget->isNested) {
            return;
        }

        if (! $model = $widget->model) {
            return;
        }

        if (! method_exists($model, 'isClassExtendedWith')) {
            return;
        }

        if (
            ! $model->isClassExtendedWith(\RainLab\Translate\Behaviors\TranslatableModel::class) &&
            ! $model->isClassExtendedWith(\RainLab\Translate\Behaviors\TranslatablePage::class) &&
            ! $model->isClassExtendedWith(\RainLab\Translate\Behaviors\TranslatableCmsObject::class)
        ) {
            return;
        }

        if (! $model->hasTranslatableAttributes()) {
            return;
        }

        if (! empty($widget->fields)) {
            $widget->fields = $this->processFormMLFields($widget->fields, $model);
        }

        if (! empty($widget->tabs['fields'])) {
            $widget->tabs['fields'] = $this->processFormMLFields($widget->tabs['fields'], $model);
        }

        if (! empty($widget->secondaryTabs['fields'])) {
            $widget->secondaryTabs['fields'] = $this->processFormMLFields($widget->secondaryTabs['fields'], $model);
        }
    }

    /**
     * processFormMLFields function to replace standard fields with multi-lingual equivalents
     *
     * @param  array  $fields
     * @param  Model  $model
     * @return array
     */
    protected function processFormMLFields($fields, $model)
    {
        $typesMap = [
            'text' => 'mltext',
            'textarea' => 'mltextarea',
            'richeditor' => 'mlricheditor',
            'markdown' => 'mlmarkdowneditor',
            'repeater' => 'mlrepeater',
            'nestedform' => 'mlnestedform',
            'mediafinder' => 'mlmediafinder',
        ];

        $translatable = array_flip($model->getTranslatableAttributes());

        /*
         * Special: A custom field "markup_html" is used for Content templates.
         */
        if ($model instanceof Content && array_key_exists('markup', $translatable)) {
            $translatable['markup_html'] = true;
        }

        foreach ($fields as $name => $config) {
            if (! array_key_exists($name, $translatable)) {
                continue;
            }

            $type = array_get($config, 'type', 'text');

            if (array_key_exists($type, $typesMap)) {
                $fields[$name]['type'] = $typesMap[$type];
            }
        }

        return $fields;
    }

    //
    // Theme
    //

    /**
     * importMessagesFromTheme
     */
    public function importMessagesFromTheme($themeCode)
    {
        try {
            (new ThemeScanner)->scanThemeConfigForMessages($themeCode);
        } catch (Exception $ex) {
        }
    }

    //
    // CMS objects
    //

    /**
     * setMessageContext for translation caching.
     */
    public function setMessageContext($page)
    {
        if (! $page) {
            return;
        }

        $translator = Translator::instance();

        Message::setContext($translator->getLocale(), $page->url);
    }

    /**
     * findTranslatedContentFile adds language suffixes to content files.
     *
     * @return string|null
     */
    public function findTranslatedContentFile($controller, $fileName)
    {
        if (! strlen(File::extension($fileName))) {
            $fileName .= '.htm';
        }

        /*
         * Splice the active locale in to the filename
         * - content.htm -> content.en.htm
         */
        $locale = Translator::instance()->getLocale();
        $fileName = substr_replace($fileName, '.'.$locale, strrpos($fileName, '.'), 0);
        if (($content = Content::loadCached($controller->getTheme(), $fileName)) !== null) {
            return $content;
        }
    }

    //
    // Static pages
    //

    /**
     * pruneTranslatedContentTemplates removes localized content files from templates collection
     *
     * @param  \October\Rain\Database\Collection  $templates
     * @return \October\Rain\Database\Collection
     */
    public function pruneTranslatedContentTemplates($templates)
    {
        $locales = LocaleModel::listAvailable();

        $extensions = array_map(function ($ext) {
            return '.'.$ext;
        }, array_keys($locales));

        return $templates->filter(function ($template) use ($extensions) {
            return ! Str::endsWith($template->getBaseFileName(), $extensions);
        });
    }

    /**
     * findLocalizedMailViewContent adds language suffixes to mail view files.
     *
     * @param  \October\Rain\Mail\Mailer  $mailer
     * @param  \Illuminate\Mail\Message  $message
     * @param  string  $view
     * @param  array  $data
     * @param  string  $raw
     * @param  string  $plain
     * @return bool|void Will return false if the translation process successfully replaced the original message with a translated version to prevent the original version from being processed.
     */
    public function findLocalizedMailViewContent($mailer, $message, $view, $data, $raw, $plain)
    {
        // Raw content cannot be localized at this level
        if (! empty($raw)) {
            return;
        }

        // Get the locale to use for this template
        $locale = ! empty($data['_current_locale']) ? $data['_current_locale'] : App::getLocale();

        $factory = $mailer->getViewFactory();

        if (! empty($view)) {
            $view = $this->getLocalizedView($factory, $view, $locale);
        }

        if (! empty($plain)) {
            $plain = $this->getLocalizedView($factory, $plain, $locale);
        }

        $code = $view ?: $plain;
        if (empty($code)) {
            return null;
        }

        $plainOnly = empty($view);

        if (MailManager::instance()->addContentToMailer($message, $code, $data, $plainOnly)) {
            // the caller who fired the event is expecting a FALSE response to halt the event
            return false;
        }
    }

    /**
     * getLocalizedView searches mail view files based on locale
     *
     * @param  \October\Rain\Mail\Mailer  $mailer
     * @param  \Illuminate\Mail\Message  $message
     * @param  string  $code
     * @param  string  $locale
     * @return string|null
     */
    public function getLocalizedView($factory, $code, $locale)
    {
        $locale = strtolower($locale);

        $searchPaths[] = $locale;

        if (str_contains($locale, '-')) {
            [$lang] = explode('-', $locale);
            $searchPaths[] = $lang;
        }

        foreach ($searchPaths as $path) {
            $localizedView = sprintf('%s-%s', $code, $path);

            if ($factory->exists($localizedView)) {
                return $localizedView;
            }
        }

        return null;
    }
}
