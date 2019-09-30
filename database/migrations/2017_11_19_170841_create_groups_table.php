<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('image');
            $table->string('slug');
            $table->longText('details');
            $table->unsignedInteger('type_id');
            $table->enum('status', ['closed', 'opened'])->default('closed');
            $table->unsignedInteger('user_id');
            $table->enum('admin_status', ['pending', 'accept', 'finish_duration'])->default('pending');
            $table->timestamp('start_duration')->nullable();
            $table->timestamp('end_duration')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('type_id')->references('id')->on('group_types');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
