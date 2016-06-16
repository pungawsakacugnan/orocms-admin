<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePluginHooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plugin_hooks', function($table) {
            $table->increments('id')->unsigned;
            $table->integer('plugin_id')->unsigned;
            $table->string('event', 80);
            $table->string('class', 255);
            $table->text('params');
            $table->boolean('published');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('plugin_hooks');
    }
}
