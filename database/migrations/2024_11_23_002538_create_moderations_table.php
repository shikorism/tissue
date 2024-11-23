<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moderations', function (Blueprint $table) {
            $table->id();
            $table->integer('moderator_id')->nullable();
            $table->bigInteger('report_id')->nullable();
            $table->integer('target_user_id');
            $table->integer('ejaculation_id')->nullable();
            $table->string('action');
            $table->string('comment', 1000)->default('');
            $table->boolean('send_email');
            $table->timestamps();

            $table->foreign('moderator_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ejaculation_id')->references('id')->on('ejaculations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moderations');
    }
};
