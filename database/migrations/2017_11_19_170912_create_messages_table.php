<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->longText('text');
            $table->string('draft')->nullable();
            $table->boolean('pin')->default(false);
            $table->boolean('is_reply')->default(false);
            $table->unsignedInteger('user_id');
//            $table->unsignedInteger('group_id');
            $table->string('link')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->enum('type',['message','message_survey'])->default('message');
            $table->foreign('user_id')->references('id')->on('users');
//            $table->foreign('group_id')->references('id')->on('groups');

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
        Schema::dropIfExists('messages');
    }
}
