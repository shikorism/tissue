<?php

namespace App\Console\Commands;

use App\Ejaculation;
use App\Utilities\Formatter;
use DB;
use Illuminate\Console\Command;

class NormalizeEjaculationLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tissue:ejaculation:normalize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize ejaculation links';

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
            /** @var Ejaculation $ejaculation */
            foreach (Ejaculation::query()->where('link', '<>', '')->cursor() as $ejaculation) {
                $normalized = $this->formatter->normalizeUrl($ejaculation->link);
                $this->line("#{$ejaculation->id} {$ejaculation->link} : {$normalized}");
                $ejaculation->normalized_link = $normalized;
                $ejaculation->save();
            }
        });

        $elapsed = (hrtime(true) - $start) / 1e+9;
        $this->info("Done! ({$elapsed} sec)");
    }
}
