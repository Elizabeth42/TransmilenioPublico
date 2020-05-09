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
        factory(App\BusType::class, 3)->create()->each(function ($BusType) {
            $enable = rand(0,1);
            $stateBusType = $enable == 0 ? 'n' : 'a';
            $BusType->activo_tipo_bus = $stateBusType;

            $BusType->save();
        });
    }
}
