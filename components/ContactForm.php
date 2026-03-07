<?php

namespace MoonWalkerz\Contact\Components;

use Cms\Classes\ComponentBase;
use Flash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use MoonWalkerz\Contact\Models\Contact;
use MoonWalkerz\Contact\Models\Settings;
use October\Rain\Exception\ValidationException;

class ContactForm extends ComponentBase
{
    public $settings;

    public $is_phone_requested;

    public $is_gdpr_contact_requested;

    public $is_gdpr_promo_requested;

    public $is_gdpr_third_parties_requested;

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
        // Rate limiting: max 5 submissions per minute per IP
        $key = 'contact_form_' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            Flash::error(trans('moonwalkerz.contact::lang.contactform.error'));
            throw new ValidationException(['message' => 'Too many attempts. Please try again later.']);
        }
        RateLimiter::hit($key, 60);

        $settings = Settings::instance();

        // Server-side reCAPTCHA verification
        if ($settings->captcha && $settings->google_secret_key) {
            $token = post('g-recaptcha-response');
            if (empty($token)) {
                Flash::error(trans('moonwalkerz.contact::lang.contactform.error'));
                throw new ValidationException(['captcha' => 'Please complete the CAPTCHA.']);
            }
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $settings->google_secret_key,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);
            if (! ($response->json('success') ?? false)) {
                Flash::error(trans('moonwalkerz.contact::lang.contactform.error'));
                throw new ValidationException(['captcha' => 'CAPTCHA verification failed.']);
            }
        }

        $data = post();
        $rules = [
            'name'    => 'required|min:2|max:100',
            'email'   => 'required|email|max:191',
            'message' => 'required|max:5000',
        ];

        if ($this->property('is_phone_mandatory')) {
            $rules['phone'] = 'required|max:30';
        } elseif (! empty($data['phone'])) {
            $rules['phone'] = 'max:30';
        }
        if ($this->property('is_gdpr_contact_requested')) {
            $rules['sw_contact'] = 'accepted';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            Flash::error(trans('moonwalkerz.contact::lang.contactform.error'));
            throw new ValidationException($validator);
        }

        $contact = new Contact();
        $contact->name             = post('name');
        $contact->email            = post('email');
        $contact->message          = post('message');
        $contact->phone            = post('phone');
        $contact->sw_contact       = post('sw_contact') === 'on' ? 1 : 0;
        $contact->sw_promo         = post('sw_promo') === 'on' ? 1 : 0;
        $contact->sw_third_parties = post('sw_third_parties') === 'on' ? 1 : 0;
        $contact->save();

        $vars = [
            'name'                => $contact->name,
            'email'               => $contact->email,
            'msg'                 => $contact->message,
            'phone'               => $contact->phone,
            'allow_contact'       => $contact->sw_contact ? 'yes' : 'no',
            'allow_promo'         => $contact->sw_promo ? 'yes' : 'no',
            'allow_third_parties' => $contact->sw_third_parties ? 'yes' : 'no',
        ];

        $toEmail   = $this->property('emailto');
        $toName    = $this->property('emailtoname');
        $subject   = $this->property('subject');
        $fromEmail = $contact->email;
        $fromName  = $contact->name;

        Mail::send('moonwalkerz.contact::mail.message', $vars, function ($message) use ($toEmail, $toName, $fromEmail, $fromName, $subject) {
            $message->to($toEmail, $toName);
            $message->replyTo($fromEmail, $fromName);
            $message->subject($subject);
        });

        Flash::success(trans('moonwalkerz.contact::lang.contactform.message_sent'));
        return Redirect::back();
    }
}
