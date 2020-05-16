<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToTagRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ejaculation_tag', function (Blueprint $table) {
            $table->unique(['ejaculation_id', 'tag_id']);
        });
        Schema::table('metadata_tag', function (Blueprint $table) {
            $table->unique(['metadata_url', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ejaculation_tag', function (Blueprint $table) {
            $table->dropUnique(['ejaculation_id', 'tag_id']);
        });
        Schema::table('metadata_tag', function (Blueprint $table) {
            $table->dropUnique(['metadata_url', 'tag_id']);
        });
    }
}
