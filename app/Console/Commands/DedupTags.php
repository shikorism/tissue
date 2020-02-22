<?php

namespace App\Console\Commands;

use App\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DedupTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tissue:tag:dedup {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deduplicate tags';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('dry-run')) {
            $this->warn('dry-runモードで実行します。');
        } else {
            if (!$this->confirm('dry-runオプションが付いてないけど、本当に実行しますか？')) {
                return;
            }
        }

        DB::transaction(function () {
            $duplicatedTags = DB::table('tags')
                ->select('name', DB::raw('count(*)'))
                ->groupBy('name')
                ->having(DB::raw('count(*)'), '>=', 2)
                ->get();

            $this->info($duplicatedTags->count() . ' duplicated tags found.');

            foreach ($duplicatedTags as $tag) {
                $this->line('Tag name: ' . $tag->name);

                $tagIds = Tag::where('name', $tag->name)->orderBy('id')->pluck('id');
                $newId = $tagIds->first();
                $dropIds = $tagIds->slice(1);

                $this->line('  New ID: ' . $newId);
                $this->line('  Drop IDs: ' . $dropIds->implode(', '));

                if ($this->option('dry-run')) {
                    continue;
                }

                // 同じタグ名でIDが違うものについて、全て統一する
                foreach (['ejaculation_tag', 'metadata_tag'] as $table) {
                    DB::table($table)
                        ->whereIn('tag_id', $dropIds)
                        ->update(['tag_id' => $newId]);
                }
                DB::table('tags')->whereIn('id', $dropIds)->delete();

                // 統一した上で、重複しているレコードを削除する
                DB::delete(
                    <<<SQL
DELETE FROM ejaculation_tag
WHERE id IN (
    SELECT id
    FROM (
        SELECT id, row_number() OVER (PARTITION BY ejaculation_id, tag_id ORDER BY id) AS ord
        FROM ejaculation_tag
    ) t
    WHERE ord > 1
)
SQL
                );
                DB::delete(
                    <<<SQL
DELETE FROM metadata_tag
WHERE id IN (
    SELECT id
    FROM (
        SELECT id, row_number() OVER (PARTITION BY metadata_url, tag_id ORDER BY id) AS ord
        FROM metadata_tag
    ) t
    WHERE ord > 1
)
SQL
                );
            }
        });

        $this->info('Done!');
    }
}
