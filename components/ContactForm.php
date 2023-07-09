<?php

namespace Moonwalkerz\Contact\Components;

use Cms\Classes\ComponentBase;
use Flash;
use Input;
use Mail;
use Moonwalkerz\Contact\Models\Contact;
use Moonwalkerz\Contact\Models\Settings;
use Redirect;
use ValidationException;
use Validator;

class ContactForm extends ComponentBase
{
    public $settings;

    public $is_phone_requested;

    public $is_gdpr_contact_requested;

    public $is_gdpr_promo_requested;

    public $is_gdpr_third_parties_requested;

    public $l;

    public function componentDetails()
    {
        return [
            'name' => 'Contact Form',
            'description' => 'Simple contact form',
        ];
    }

    public function defineProperties()
    {
        return [
            'emailto' => [
                'title' => 'moonwalkerz.contact::lang.properties.emailto',
                'description' => 'moonwalkerz.contact::lang.properties.emailto_desc',
                'default' => 'info@example.com',
            ],
            'emailtoname' => [
                'title' => 'moonwalkerz.contact::lang.properties.emailtoname',
                'description' => 'moonwalkerz.contact::lang.properties.emailtoname_desc',
                'default' => 'Administrator',
            ],
            'subject' => [
                'title' => 'moonwalkerz.contact::lang.properties.subject',
                'description' => 'moonwalkerz.contact::lang.properties.subject_desc',
                'default' => 'Message from website',
            ],
            'is_phone_requested' => [
                'title' => 'moonwalkerz.contact::lang.properties.is_phone_requested.title',
                'description' => 'moonwalkerz.contact::lang.properties.is_phone_requested.description',
                'type' => 'checkbox',
                'default' => false,
            ],
            'is_phone_mandatory' => [
                'title' => 'moonwalkerz.contact::lang.properties.is_phone_mandatory.title',
                'description' => 'moonwalkerz.contact::lang.properties.is_phone_mandatory.description',
                'type' => 'checkbox',
                'default' => false,
            ],
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

        $this->is_phone_requested = $this->page['is_phone_requested'] = $this->property('is_phone_requested');
        $this->is_gdpr_contact_requested = $this->page['is_gdpr_contact_requested'] = $this->property('is_gdpr_contact_requested');
        $this->is_gdpr_promo_requested = $this->page['is_gdpr_promo_requested'] = $this->property('is_gdpr_promo_requested');
        $this->is_gdpr_third_parties_requested = $this->page['is_gdpr_third_parties_requested'] = $this->property('is_gdpr_third_parties_requested');
    }

    public function onSend()
    {
        $data = post();
        $rules = [
            'name' => 'required|min:5',
            'email' => 'required|email',
            'message' => 'required',
        ];

        if ($this->property('is_phone_mandatory')) {
            $rules['phone'] = 'required';
        }
        if ($this->property('is_gdpr_contact_requested')) {
            $rules['sw_contact'] = 'required';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            Flash::error(trans('moonwalkerz.contact::lang.contactform.error'));

            throw new ValidationException($validator);
        } else {
            $contact = new Contact();

            $contact->name = Input::get('name');
            $contact->email = Input::get('email');
            $contact->message = Input::get('message');
            $contact->phone = Input::get('phone');
            $contact->sw_contact = Input::get('sw_contact') == 'on' ? 1 : 0;
            $contact->sw_promo = Input::get('sw_promo') == 'on' ? 1 : 0;
            $contact->sw_third_parties = Input::get('sw_third_parties') == 'on' ? 1 : 0;

            $contact->save();

            $vars = [
                'name' => $contact->name,
                'email' => $contact->email,
                'message' => $contact->message,
                'phone' => $contact->phone,
                'allow_contact' => Input::get('sw_contact') == 'on' ? 'yes' : 'no',
                'allow_promo' => Input::get('sw_promo') == 'on' ? 'yes' : 'no',
                'allow_third_parties' => Input::get('sw_third_parties') == 'on' ? 'yes' : 'no'
            ];
            $email = Input::get('email');
            $name = Input::get('name');
            /*
            "An exception has been thrown during the rendering of a template ("Object of class Illuminate\Mail\Message could not be converted to string")
            */

            Mail::send('moonwalkerz.contact::mail.message', $vars, function ($message) use ($email, $name) {
                $message->to($this->property('emailto'), $this->property('emailtoname'));
                $message->replyTo($email, $name);
                $message->subject($this->property('subject'));
            });

            Flash::success(trans('moonwalkerz.contact::lang.contactform.message_sent'));

            return Redirect::back();
        }
    }
}
