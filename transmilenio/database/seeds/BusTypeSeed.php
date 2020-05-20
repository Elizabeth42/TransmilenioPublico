<?php

use Illuminate\Database\Seeder;

class BusTypeSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\BusType::class, 3)->create();
    }
}
