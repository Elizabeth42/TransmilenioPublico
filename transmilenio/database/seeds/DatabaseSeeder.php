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
        //$this->call(RouteSeed::class);
        //$this->call(BusTypeSeed::class);
        //$this->call(BusSeed::class);
        //$this->call(ScheduleSeed::class);
        //$this->call(TimeRouteAssignmentSeeder::class);
        $this->call(TravelSeeder::class);
    }
}
