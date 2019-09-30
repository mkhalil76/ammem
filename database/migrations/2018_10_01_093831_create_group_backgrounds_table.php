<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupBackgroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_backgrounds', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('group_id')->unsigned();;
            $table->String('background');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('group_backgrounds', function (Blueprint $table) {
            $table->foreign('group_id')
                ->references('id')->on('groups')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_backgrounds');
    }
}
