<?php

namespace MoonWalkerz\Contact;

use System\Classes\PluginBase;
use System\Classes\MailManager;

class Plugin extends PluginBase
{
    public $require = ['RainLab.Translate'];

    public function registerComponents()
    {
        return [
            'MoonWalkerz\Contact\Components\ContactForm' => 'contactform',
    //        'MoonWalkerz\Contact\Components\NewsletterForm' => 'newsletterform',
        ];
    }

    public function registerPageSnippets()
    {
        return [
            'MoonWalkerz\Contact\Components\ContactForm' => 'contactform',
 //           'MoonWalkerz\Contact\Components\NewsletterForm' => 'newsletterform',
        ];
    }

    /**
     * registerMailer templates
     */
    public function register()
    {
        MailManager::registerCallback(function ($manager) {
            $manager->registerMailTemplates([
                'moonwalkerz.contact::mail.message',
            ]);
        });
    }

    /**
     * Registers any back-end settings.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'config' => [
                'label' => 'moonwalkerz.contact::lang.plugin.name',
                'description' => 'moonwalkerz.contact::lang.plugin.manage_settings',
                'category' => 'system::lang.system.categories.cms',
                'icon' => 'icon-envelope',
                'class' => 'MoonWalkerz\Contact\Models\Settings',
                'order' => 500,
                'keywords' => 'search',
                'permissions' => ['moonwalkerz.contact.manage_settings'],
            ],
        ];
    }
}
