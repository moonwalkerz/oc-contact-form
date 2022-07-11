<?php

namespace Moonwalkerz\Contact\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableCreateMoonwalkerzContactContacts extends Migration
{
    public function up()
    {
        Schema::dropIfExists('moonwalkerz_contact_contacts');
        Schema::create('moonwalkerz_contact_contacts', function ($table) {
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
        Schema::dropIfExists('moonwalkerz_contact_contacts');
    }
}
