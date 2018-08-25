<?php namespace MartiniMultimedia\Contact\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMartinimultimediaContactContacts extends Migration
{
    public function up()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->string('phone', 191);
        });
    }
    
    public function down()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->dropColumn('phone');
        });
    }
}
