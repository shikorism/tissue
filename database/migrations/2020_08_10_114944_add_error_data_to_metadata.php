<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddErrorDataToMetadata extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metadata', function (Blueprint $table) {
            $table->timestamp('error_at')->nullable();
            $table->string('error_exception_class')->nullable();
            $table->integer('error_http_code')->nullable();
            $table->text('error_body')->nullable();
            $table->integer('error_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metadata', function (Blueprint $table) {
            $table->dropColumn(['error_at', 'error_exception_class', 'error_http_code', 'error_body', 'error_count']);
        });
    }
}
