<?php namespace Moonwalkerz\Contact;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['RainLab.Translate'];

    public function registerComponents()
    {
    	return [
                'Moonwalkerz\Contact\Components\ContactForm' => 'contactform',
                'Moonwalkerz\Contact\Components\NewsletterForm' => 'newsletterform'
    	];
    }
    public function registerPageSnippets()
    {
        return [
            'Moonwalkerz\Contact\Components\ContactForm' => 'contactform',
            'Moonwalkerz\Contact\Components\NewsletterForm' => 'newsletterform'
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
                'label'       => 'moonwalkerz.contact::lang.plugin.name',
                'description' => 'moonwalkerz.contact::lang.plugin.manage_settings',
                'category'    => 'system::lang.system.categories.cms',
                'icon'        => 'icon-envelope',
                'class'       => 'Moonwalkerz\Contact\Models\Settings',
                'order'       => 500,
                'keywords'    => 'search',
                'permissions' => ['moonwalkerz.contact.manage_settings']
            ],
        ];
    }
}
