<?php namespace MartiniMultimedia\Contact\Models;

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
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = true;

    public $table = 'martinimultimedia_contact_contacts';
}