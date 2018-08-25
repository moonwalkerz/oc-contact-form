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

class ContactForm extends ComponentBase
{

	var $is_phone_requested;

	public function componentDetails(){
		return [
		'name' => 'Contact Form',
		'description' => 'Simple contact form'
		];
	}


	public function defineProperties() {
		return [
			'emailto' => [
				'title' => 'Email To',
				'description' => 'Where we send the email to.',
				'default' => 'info@example.com'
			],
			'emailtoname' => [
				'title' => 'Name',
				'description' => 'Name of the email to.',
				'default' => 'Administrator'
			],
			'subject' => [
				'title' => 'Subject',
				'description' => 'Subject of the email',
				'default' => 'Message from website' 
			],
			'is_phone_requested' => [
				'title' => 'Phone?',
				'description' => 'is phone requested?',
				'default' => false 
			],
			'is_phone_mandatory' => [
				'title' => 'Phone mandatory?',
				'description' => 'is phone mandatory?',
				'default' => false 
			]

		];
	}

	public function onRun() {
        
        $this->is_phone_requested=$this->page['is_phone_requested'] = $this->property('is_phone_requested');

     //   dd($this->slider);
    }


	/*

*/
	public function onSend()
	{

		
		$data = post();
		$rules = [
			'name'=> 'required|min:5',
			'email'=> 'required|email',
			'message'=> 'required',
		];

		if ($this->property('is_phone_mandatory')){
			$rules['phone']= 'required';
		}

		$validator = Validator::make($data,$rules);


		if ($validator->fails()){
			throw new ValidationException($validator);
		} else {


			$contact=new Contact();

			$contact->name=Input::get('name');
			$contact->email=Input::get('email');
			$contact->message=Input::get('message');
			$contact->phone=Input::get('phone');
			
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