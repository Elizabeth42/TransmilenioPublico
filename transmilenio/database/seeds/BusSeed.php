<?php

use Illuminate\Database\Seeder;

class BusSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Bus::class, 30)->make()->each(function($bus) {
            $valid = self::validate($bus);
            if($valid)
                $bus->save();
        });
    }

    public static function validate($bus){
        if ($bus->busType()->first()->activo_tipo_bus != 'n' &&
            $bus->where('placabus','=',$bus->placabus)->count()==0) {
            return true;
        }
      return false;
    }
}
