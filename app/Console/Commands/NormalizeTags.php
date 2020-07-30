<?php

namespace App\Console\Commands;

use App\Tag;
use App\Utilities\Formatter;
use DB;
use Illuminate\Console\Command;

class NormalizeTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tissue:tag:normalize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize tags';

    private $formatter;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Formatter $formatter)
    {
        parent::__construct();
        $this->formatter = $formatter;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = hrtime(true);

        DB::transaction(function () {
            /** @var Tag $tag */
            foreach (Tag::query()->cursor() as $tag) {
                $normalizedName = $this->formatter->normalizeTagName($tag->name);
                $this->line("{$tag->name} : {$normalizedName}");
                $tag->normalized_name = $normalizedName;
                $tag->save();
            }
        });

        $elapsed = (hrtime(true) - $start) / 1e+9;
        $this->info("Done! ({$elapsed} sec)");
    }
}
