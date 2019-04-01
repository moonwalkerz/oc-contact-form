<?php
namespace MartiniMultimedia\Contact\Components;

use Cms\Classes\ComponentBase;
use Input;
use Mail;
use Validator;
use ValidationException;
use Redirect;
use Flash;
use MartiniMultimedia\Contact\Models\Contact;
use MartiniMultimedia\Contact\Models\Settings;
//use Log;

class ContactForm extends ComponentBase
{
	var $settings;
	var $is_phone_requested;
	var $is_gdpr_enabled;
	var $enable_gdpr_marketing;
	var $enable_gdpr_newsletter;
	var $l;

	public function componentDetails(){
		return [
		'name' => 'Contact Form',
		'description' => 'Simple contact form'
		];
	}


	public function defineProperties() {
		return [
			'emailto' => [
				'title' =>  'martinimultimedia.contact::lang.properties.emailto',
				'description' => 'martinimultimedia.contact::lang.properties.emailto_desc',
				'default' => 'info@example.com'
			],
			'emailtoname' => [
				'title' => 'martinimultimedia.contact::lang.properties.emailtoname',
				'description' => 'martinimultimedia.contact::lang.properties.emailtoname_desc',
				'default' => 'Administrator'
			],
			'subject' => [
				'title' => 'martinimultimedia.contact::lang.properties.subject',
				'description' => 'martinimultimedia.contact::lang.properties.subject_desc',
				'default' => 'Message from website' 
			],
			'is_phone_requested' => [
				'title' => 'martinimultimedia.contact::lang.properties.is_phone_requested.title',
				'description' => 'martinimultimedia.contact::lang.properties.is_phone_requested.description',
				'type' => 'checkbox',
				'default' => false 
			],
			'is_phone_mandatory' => [
				'title' =>  'martinimultimedia.contact::lang.properties.is_phone_mandatory.title',
				'description' => 'martinimultimedia.contact::lang.properties.is_phone_mandatory.description',
				'type' => 'checkbox',
				'default' => false 
			],
			'is_gdpr_enabled' => [
				'title' => 'martinimultimedia.contact::lang.properties.is_gdpr_enabled.title',
				'description' =>  'martinimultimedia.contact::lang.properties.is_gdpr_enabled.description',
				'type' => 'checkbox',
				'default' => true 
			],
			'enable_gdpr_promo' => [
				'title' => 'martinimultimedia.contact::lang.properties.is_gdpr_promo.title',
				'description' => 'martinimultimedia.contact::lang.properties.is_gdpr_promo.description',
				'type' => 'checkbox',
				'default' => false 
			],
			'enable_gdpr_third' => [
				'title' => 'martinimultimedia.contact::lang.properties.is_gdpr_third.title',
				'description' => 'martinimultimedia.contact::lang.properties.is_gdpr_third.description',
				'type' => 'checkbox',
				'default' => false 
			]

		];
	}

	public function onRun() {
		
		$this->addCss('assets/css/contact.css');
		$this->settings=$this->page['settings'] = Settings::instance();
		//Log::info('->'.$this->settings);
	
        $this->is_phone_requested=$this->page['is_phone_requested'] = $this->property('is_phone_requested');
		$this->is_gdpr_enabled=$this->page['is_gdpr_enabled'] = $this->property('is_gdpr_enabled');
		$this->enable_gdpr_marketing=$this->page['enable_gdpr_marketing'] = $this->property('enable_gdpr_marketing');
		$this->enable_gdpr_newsletter=$this->page['enable_gdpr_newsletter'] = $this->property('enable_gdpr_newsletter');

    }


	public function onSend()
	{

		//Log::info('onsend');
		
		$data = post();
		$rules = [
			'name'=> 'required|min:5',
			'email'=> 'required|email',
			'message'=> 'required',
		];

		if ($this->property('is_phone_mandatory')){
			$rules['phone']= 'required';
		}
		if ($this->property('is_gdpr_enabled')){
			$rules['gdpr']= 'required';
		}

		$validator = Validator::make($data,$rules);


		if ($validator->fails()){
			//Log::info('validator fail->'.$validator->messages());
			throw new ValidationException($validator);
		} else {

			//Log::info('validator ok');
			$contact=new Contact();

			$contact->name=Input::get('name');
			$contact->email=Input::get('email');
			$contact->message=Input::get('message');
			$contact->phone=Input::get('phone');
			$contact->gdpr=Input::get('gdpr');
			$contact->promo=Input::get('promo');
			$contact->third_parties=Input::get('third_parties');
			
			$contact->save();

			$vars = [
				'name'=>Input::get('name'),
				'phone'=>Input::get('phone'),
				'email'=> Input::get('email'),
				'msg'=> Input::get('message')
			];
			$email=Input::get('email');
			$name=Input::get('name');
			/*
			"An exception has been thrown during the rendering of a template ("Object of class Illuminate\Mail\Message could not be converted to string") 
			*/

			Mail::send('martinimultimedia.contact::mail.message',$vars, function ($message) use ($email,$name) {
				$message->to($this->property('emailto'),$this->property('emailtoname'));
				$message->replyTo($email, $name);
				$message->subject($this->property('subject'));
				});
			
			Flash::success('Messaggio inviato');
			return Redirect::back();

		}


	}

}


?>