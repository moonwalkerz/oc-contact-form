<?php

namespace Moonwalkerz\Contact\Components;

use Cms\Classes\ComponentBase;
use Flash;
use Input;
use Moonwalkerz\Contact\Models\Contact;
use Moonwalkerz\Contact\Models\Settings;

use Redirect;
use ValidationException;
use Validator;

class NewsletterForm extends ComponentBase
{
    public $settings;

    public $is_gdpr_contact_requested;

    public $is_gdpr_promo_requested;

    public $is_gdpr_third_parties_requested;

    public function componentDetails()
    {
        return [
            'name' => 'Newsletter Form',
            'description' => 'Newsletter Registration Form',
        ];
    }

    public function defineProperties()
    {
        return [
            'is_gdpr_contact_requested' => [
                'title' => 'moonwalkerz.contact::lang.properties.is_gdpr_contact_requested.title',
                'description' => 'moonwalkerz.contact::lang.properties.is_gdpr_contact_requested.description',
                'type' => 'checkbox',
                'default' => true,
            ],
            'is_gdpr_promo_requested' => [
                'title' => 'moonwalkerz.contact::lang.properties.is_gdpr_promo_requested.title',
                'description' => 'moonwalkerz.contact::lang.properties.is_gdpr_promo_requested.description',
                'type' => 'checkbox',
                'default' => false,
            ],
            'is_gdpr_third_parties_requested' => [
                'title' => 'moonwalkerz.contact::lang.properties.is_gdpr_third_parties_requested.title',
                'description' => 'moonwalkerz.contact::lang.properties.is_gdpr_third_parties_requested.description',
                'type' => 'checkbox',
                'default' => false,
            ],
        ];
    }

    public function onRun()
    {
        
        $this->settings = $this->page['settings'] = Settings::instance();

        if ($this->settings->captcha) {
            $this->addJs('https://www.google.com/recaptcha/api.js', [
                'async' => 'async',
                'defer' => 'defer',
            ]);
        }

        
        $this->is_gdpr_contact_requested = $this->page['is_gdpr_contact_requested'] = $this->property('is_gdpr_contact_requested');
        $this->is_gdpr_promo_requested = $this->page['is_gdpr_promo_requested'] = $this->property('is_gdpr_promo_requested');
        $this->is_gdpr_third_parties_requested = $this->page['is_gdpr_third_parties_requested'] = $this->property('is_gdpr_third_parties_requested');
    }

    public function onSave()
    {
        $data = post();
        $rules = [
            'email' => 'required|email',
        ];
        if ($this->property('is_gdpr_contact_requested')) {
            $rules['sw_contact'] = 'required';
        }
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            Flash::error(trans('moonwalkerz.contact::lang.contactform.error'));
            throw new ValidationException($validator);
        } else {
            $contact = new Contact();

            $contact->email = Input::get('email');
            $contact->save();

            Flash::success(trans('moonwalkerz.contact::lang.contactform.subscription_sent'));

            return Redirect::back();
        }
    }
}
