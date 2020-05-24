<?php

use App\Ejaculation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNonNullableSourceOnEjaculations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('UPDATE ejaculations SET source = ? WHERE source IS NULL', [Ejaculation::SOURCE_WEB]);
        Schema::table('ejaculations', function (Blueprint $table) {
            $table->string('source')->nullable(false)->change();
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
            $table->string('source')->nullable()->change();
        });
    }
}
