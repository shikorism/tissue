<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_providers', function (Blueprint $table) {
            $table->string('host');
            $table->text('robots')->nullable();
            $table->timestamp('robots_cached_at');
            $table->boolean('is_blocked')->default(false);
            $table->integer('access_interval_sec')->default(5);
            $table->timestamps();

            $table->primary('host');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_providers');
    }
}
