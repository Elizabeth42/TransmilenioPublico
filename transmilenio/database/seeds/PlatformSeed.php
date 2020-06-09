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
        factory(App\Platform::class, 30)->make()->each(function($platform) {
            $valid = self::validate($platform);
            if($valid)
                $platform->save();
        });
    }


    public static function validate($platform){
        if ($platform->portal()->first()->activo_portal != 'n') {
            return true;
        }
        return false;
    }
}
