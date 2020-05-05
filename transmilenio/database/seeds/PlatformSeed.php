<?php

use App\Platform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PlatformSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $portals = App\Portal::all();
        factory(App\Platform::class, 7)->create()->each(function ($platform) use ($portals) {
            $random = $portals->random();
            $platform->id_portal = $random->id_portal;
            $platform->save();
        });

    }
}
