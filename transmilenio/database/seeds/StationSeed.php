<?php

use Illuminate\Database\Seeder;

class StationSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Station::class, 7)->create()->each(function ($station) {
           $station->save();
        });
    }
}
