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
            $table->string('name', 15)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_protected');
            $table->boolean('accept_analytics');
            $table->string('display_name', 20);
            $table->string('description', 500)->default('');
            $table->bigInteger('twitter_id')->nullable();
            $table->string('twitter_name', 15)->default('');
            $table->rememberToken();
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
