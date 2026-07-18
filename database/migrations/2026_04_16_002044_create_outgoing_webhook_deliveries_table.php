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
        Schema::create('outgoing_webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('outgoing_webhook_id'); // ログ保全のため外部キーにしない
            $table->integer('user_id'); // ログ保全のため外部キーにしない
            $table->uuid('delivery_id');
            $table->string('event');
            $table->boolean('is_success');
            $table->integer('status_code')->nullable();
            $table->text('request_body');
            $table->text('response_body')->nullable();
            $table->dateTime('delivered_at');
            $table->timestamps();

            $table->index('outgoing_webhook_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_webhook_deliveries');
    }
};
