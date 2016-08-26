<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePluginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plugins', function($table) {
            $table->increments('id')->unsigned;
            $table->string('name', 50);
            $table->integer('priority')->nullable();
            $table->string('version', 10)->nullable();
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
        Schema::drop('plugins');
    }
}
