<?php namespace MartiniMultimedia\Contact\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMartinimultimediaContactContacts2 extends Migration
{
    public function up()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->boolean('sw_gdpr')->default(1);
            $table->boolean('sw_contact')->default(0);
            $table->boolean('sw_promo')->default(0);
            $table->boolean('sw_third_parties')->default(0);
            $table->text('txt_gdpr');
            $table->text('txt_contact');
            $table->text('txt_promo');
            $table->text('txt_third_parties');
        });
    }
    
    public function down()
    {
        Schema::table('martinimultimedia_contact_contacts', function($table)
        {
            $table->dropColumn('sw_gdpr');
            $table->dropColumn('sw_contact');
            $table->dropColumn('sw_promo');
            $table->dropColumn('sw_third_parties');
            $table->dropColumn('txt_gdpr');
            $table->dropColumn('txt_contact');
            $table->dropColumn('txt_promo');
            $table->dropColumn('txt_third_parties');
        });
    }
}
