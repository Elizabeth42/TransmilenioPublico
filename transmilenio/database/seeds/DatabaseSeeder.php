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
       //$this->call(TrunkStationSeed::class);
       //$this->call(PortalSeed::class);
       //$this->call(PlatformSeed::class);
        //$this->call(WagonSeed::class);
        $this->call(RouteSeed::class);

    }
}
