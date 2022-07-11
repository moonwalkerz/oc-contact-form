<?php

namespace RainLab\Translate\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('rainlab_translate_messages', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code')->index()->nullable();
            $table->mediumText('message_data')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rainlab_translate_messages');
    }
}
