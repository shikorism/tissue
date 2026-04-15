<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outgoing_webhooks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->boolean('on_checkin_created')->default(false);
            $table->boolean('on_checkin_updated')->default(false);
            $table->boolean('on_checkin_deleted')->default(false);
            $table->timestamps();

            $table->index('user_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_webhooks');
    }
};
