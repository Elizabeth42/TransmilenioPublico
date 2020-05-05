<?php

use Illuminate\Database\Seeder;

class TrunkSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Trunk::class, 7)->create()->each(function ($trunk) {

            $trunk->save();

        });
    }
}
