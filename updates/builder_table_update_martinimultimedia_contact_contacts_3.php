<?php namespace MartiniMultimedia\Contact\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMartinimultimediaContactContacts3 extends Migration
{
    public function up()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->dropColumn('sw_gdpr');
            $table->dropColumn('txt_gdpr');
            $table->dropColumn('txt_contact');
            $table->dropColumn('txt_promo');
            $table->dropColumn('txt_third_parties');
        });
    }
    
    public function down()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->boolean('sw_gdpr')->default(1);
            $table->text('txt_gdpr');
            $table->text('txt_contact');
            $table->text('txt_promo');
            $table->text('txt_third_parties');
        });
    }
}
