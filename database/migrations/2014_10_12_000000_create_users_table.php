<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('activity_name')->nullable();
            $table->string('organisation_name')->nullable();
            $table->unsignedInteger('activity_id')->nullable();
            $table->unsignedInteger('organization_id')->nullable();
            $table->unsignedInteger('interest_id')->nullable();
            $table->string('mobile')->unique();
            $table->enum('type',['admin','user'])->default('user');
            $table->enum('status',['active','block'])->default('active');
            $table->enum('gender',['male','female'])->default('male');
            $table->unsignedInteger('job_id')->nullable();
            $table->string('photo_id')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('activation_code')->nullable();
            $table->boolean('is_confirm')->default(false);
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->foreign('interest_id')->references('id')->on('interests');
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('job_id')->references('id')->on('jobs');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
