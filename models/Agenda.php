<?php

namespace MoonWalkerz\Contact\Models;

use Model;

class Agenda extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'name'    => 'nullable|min:2|max:191',
        'email'   => 'nullable|email|max:191',
        'phone'   => 'nullable|max:30',
        'message' => 'nullable|max:5000',
        'address' => 'nullable|max:191',
        'city'    => 'nullable|max:100',
        'zip'     => 'nullable|max:20',
        'state'   => 'nullable|max:100',
        'country' => 'nullable|max:100',
    ];

    public $timestamps = true;

    public $table = 'moonwalkerz_contact_agenda';
}
