<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EjaculationSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sources = ['web', 'csv', 'webhook', 'api'];
        foreach ($sources as $source) {
            DB::table('ejaculation_sources')->insertOrIgnore(['name' => $source]);
        }
    }
}
