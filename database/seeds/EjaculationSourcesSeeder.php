<?php

use Illuminate\Database\Seeder;

class EjaculationSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sources = ['web', 'csv', 'webhook'];
        foreach ($sources as $source) {
            DB::table('ejaculation_sources')->insertOrIgnore(['name' => $source]);
        }
    }
}
