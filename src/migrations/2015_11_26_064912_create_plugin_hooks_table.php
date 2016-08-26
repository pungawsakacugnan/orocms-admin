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
            $table->string('event', 80)->nullable();
            $table->string('class', 255)->nullable();
            $table->text('params')->nullable();
            $table->boolean('published')->nullable();
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
