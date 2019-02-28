<?php namespace MartiniMultimedia\Contact\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMartinimultimediaContactContacts5 extends Migration
{
    public function up()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->string('name', 191)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->string('name', 191)->nullable(false)->change();
        });
    }
}
