<?php

use Illuminate\Database\Seeder;

class PortalSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {

        factory(App\Portal::class, 10)->make()->each(function($portal) {
            $valid = self::validate($portal);
            if($valid)
                $portal->save();
        });
    }

    public static function validate($portal){
        if ($portal->trunks()->first()->activo_troncal != 'n') {
            return true;
        }
        return false;
    }
}
