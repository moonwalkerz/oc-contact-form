<?php

namespace RainLab\Translate\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class CreateIndexesTable extends Migration
{
    public function up()
    {
        Schema::create('rainlab_translate_indexes', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('locale')->index();
            $table->string('model_id')->index()->nullable();
            $table->string('model_type')->index()->nullable();
            $table->string('item')->nullable()->index();
            $table->mediumText('value')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rainlab_translate_indexes');
    }
}
