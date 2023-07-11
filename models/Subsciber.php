<?php namespace MoonWalkerz\Contact\Models;

use Model;

/**
 * Model
 */
class Subsciber extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'moonwalkerz_contact_subscribers';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
