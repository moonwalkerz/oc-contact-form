<?php namespace MartiniMultimedia\Contact\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMartinimultimediaContactContacts4 extends Migration
{
    public function up()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->text('message')->nullable()->change();
            $table->string('phone', 191)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->text('message')->nullable(false)->change();
            $table->string('phone', 191)->nullable(false)->change();
        });
    }
}
