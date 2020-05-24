<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceToEjaculations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ejaculation_sources', function (Blueprint $table) {
            $table->string('name');
            $table->primary('name');
        });
        Schema::table('ejaculations', function (Blueprint $table) {
            $table->string('source')->nullable();
            $table->foreign('source')->references('name')->on('ejaculation_sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ejaculations', function (Blueprint $table) {
            $table->dropColumn('source');
        });
        Schema::drop('ejaculation_sources');
    }
}
