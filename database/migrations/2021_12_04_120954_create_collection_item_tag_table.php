<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionItemTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_item_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('collection_item_id')->index();
            $table->integer('tag_id')->index();
            $table->timestamps();

            $table->unique(['collection_item_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.¡
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collection_item_tag');
    }
}
