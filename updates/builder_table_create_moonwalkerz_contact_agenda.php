<?php

namespace MoonWalkerz\Contact\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableCreateMoonWalkerzContactAgenda extends Migration
{
    public function up()
    {
        if (Schema::hasTable('moonwalkerz_contact_agenda')) {
            return;
        }
        Schema::create('moonwalkerz_contact_agenda', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('name', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('message')->nullable();
            $table->string('address', 191)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->boolean('sw_gdpr')->default(1);
            $table->boolean('sw_contact')->default(0);
            $table->boolean('sw_promo')->default(0);
            $table->boolean('sw_third_parties')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('moonwalkerz_contact_agenda');
    }
}
