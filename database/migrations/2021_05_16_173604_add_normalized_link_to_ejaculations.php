<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNormalizedLinkToEjaculations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ejaculations', function (Blueprint $table) {
            $table->text('normalized_link')->default('');
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
            $table->dropColumn('normalized_link');
        });
    }
}
