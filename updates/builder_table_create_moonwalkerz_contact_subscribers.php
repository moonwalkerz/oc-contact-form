<?php namespace MoonWalkerz\Contact\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMoonWalkerzContactSubscribers extends Migration
{
    public function up()
    {
        Schema::create('moonwalkerz_contact_subscribers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('email')->nullable();
            $table->boolean('sw_contact')->nullable()->default(0);
            $table->boolean('sw_promo')->nullable()->default(0);
            $table->boolean('sw_third_parties')->nullable()->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('moonwalkerz_contact_subscribers');
    }
}
