<?php namespace MartiniMultimedia\Contact\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMartinimultimediaContactContacts extends Migration
{
    public function up()
    {
        Schema::dropIfExists('martinimultimedia_contact_contacts');
        Schema::create('martinimultimedia_contact_contacts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('name', 191)->nullable();
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->string('phone', 191)->nullable();
            $table->boolean('sw_gdpr')->default(1);
            $table->boolean('sw_contact')->default(0);
            $table->boolean('sw_promo')->default(0);
            $table->boolean('sw_third_parties')->default(0);
            
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('martinimultimedia_contact_contacts');
    }
}
