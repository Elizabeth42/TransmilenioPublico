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
        factory(App\Station::class, 3)->create()->each(function ($station) {
            $station->save();
            //   $illness->vaccines()->attach([10, 11]);
        });
    }
}
