<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEjaculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ejaculations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->timestamp('ejaculated_date');
            $table->string('note', 500)->default('');
            $table->double('geo_latitude')->nullable();
            $table->double('geo_longitude')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->unique(['user_id', 'ejaculated_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ejaculations');
    }
}
