<?php namespace Martinimultimedia\Contact\Components;

use Cms\Classes\ComponentBase;
use Log;
use Validator;
use ValidationException;
use Redirect;
use Flash;
use Input;
use MartiniMultimedia\Contact\Models\Contact;

class NewsletterForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Newsletter Form',
            'description' => 'Newsletter Registration Form'
        ];
    }

    public function defineProperties()
    {
        return [];
    }




    public function onSave()
	{

//		Log::info("onSave -> newsletterform");	
		$data = post();
		$rules = [
			'email'=> 'required|email',
		];

		$validator = Validator::make($data,$rules);


		if ($validator->fails()){
			throw new ValidationException($validator);
		} else {


			$contact=new Contact();

			$contact->email=Input::get('email');
			$contact->save();

			Flash::success('Iscrizione effettuata');
			return Redirect::back();

		}


	}

}
