<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ScheduleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Schedule::class, 3)->create()->each(function ($schedule) {
            log::info('------------------------------------------------------------------------------');
            $enable = rand(0,1);
            $stateSchedule = $enable == 0 ? 'n' : 'a';
            $schedule->activo_horario = $stateSchedule;
            log::info('la hora de inicio sera: '.$schedule->horario_inicio);
            log::info('la hora de fin sera: '.$schedule->horario_fin);
            log::info('El dia sera : '.$schedule->dia);
            log::info('El activo sera : '.$schedule->activo_horario);

            $schedule->save();
        });
    }
}
