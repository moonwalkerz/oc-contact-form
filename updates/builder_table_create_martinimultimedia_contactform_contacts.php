<?php namespace MartiniMultimedia\Contact\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMartinimultimediaContactContacts extends Migration
{
    public function up()
    {
        Schema::create('martinimultimedia_contact_contacts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('martinimultimedia_contact_contacts');
    }
}
