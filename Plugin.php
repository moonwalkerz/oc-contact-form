<?php

namespace MoonWalkerz\Contact;

use System\Classes\PluginBase;

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
     * Register mail templates.
     */
    public function registerMailTemplates()
    {
        return [
            'moonwalkerz.contact::mail.message' => 'Contact form message',
        ];
    }

    /**
     * Registers backend permissions.
     */
    public function registerPermissions()
    {
        return [
            'moonwalkerz.contact.access_contacts' => [
                'tab'   => 'moonwalkerz.contact::lang.plugin.name',
                'label' => 'moonwalkerz.contact::lang.plugin.access_contacts',
            ],
            'moonwalkerz.contact.access_agenda' => [
                'tab'   => 'moonwalkerz.contact::lang.plugin.name',
                'label' => 'moonwalkerz.contact::lang.plugin.access_agenda',
            ],
            'moonwalkerz.contact.manage_settings' => [
                'tab'   => 'moonwalkerz.contact::lang.plugin.name',
                'label' => 'moonwalkerz.contact::lang.plugin.manage_settings',
            ],
        ];
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
