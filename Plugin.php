<?php namespace MartiniMultimedia\Contact;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['Rainlab.Translate'];

    public function registerComponents()
    {
    	return [
    			'MartiniMultimedia\Contact\Components\ContactForm' => 'contactform'
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
                'label'       => 'martinimultimedia.contact::lang.plugin.name',
                'description' => 'martinimultimedia.contact::lang.plugin.manage_settings',
                'category'    => 'system::lang.system.categories.cms',
                'icon'        => 'icon-envelope',
                'class'       => 'MartiniMultimedia\Contact\Models\Settings',
                'order'       => 500,
                'keywords'    => 'search',
                'permissions' => ['martinimultimedia.contact.manage_settings']
            ],
        ];
    }
}
