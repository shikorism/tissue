<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatedEjaculationTagsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS related_ejaculation_tags');
        DB::statement(
            <<<'SQL'
CREATE VIEW related_ejaculation_tags AS
SELECT e.id AS ejaculation_id, et.tag_id AS tag_id
FROM ejaculations e
INNER JOIN ejaculation_tag et ON e.id = et.ejaculation_id
UNION
SELECT e.id AS ejaculation_id, mt.tag_id AS tag_id
FROM ejaculations e
INNER JOIN metadata m ON e.normalized_link = m.url
INNER JOIN metadata_tag mt ON m.url = mt.metadata_url
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS related_ejaculation_tags');
    }
}
