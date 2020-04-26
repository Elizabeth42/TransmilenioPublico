<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

       //$this->call(TrunkSeed::class);
        //$this->call(StationSeed::class);
        //$this->call(PortalSeed::class);
        $this->call(PlatformSeed::class);
    }
}
