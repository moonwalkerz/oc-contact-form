<?php

namespace MoonWalkerz\Contact\Models;

use Model;

/**
 * Model
 */
class Contact extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
        'name'    => 'required|min:2|max:100',
        'email'   => 'required|email|max:191',
        'message' => 'required|max:5000',
        'phone'   => 'nullable|max:30',
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = true;

    public $table = 'moonwalkerz_contact_contacts';
}
