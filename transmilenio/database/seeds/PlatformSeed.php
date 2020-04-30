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
        $stations = App\Station::all();
        factory(App\Platform::class, 7)->create()->each(function ($platform) use ($portals, $stations) {
            $r =  rand(0, 1);
            if ($r == 0) // se asumira que es asociada a un portal
            {
                $random = $portals->random();
                $platform->id_portal = $random->id_portal;
                $platform->save();
            } else { // se asumira que es asociada a una estacion
                $random = $stations->random();
                $trunks = $random->trunks()->get();
                // Dejo registro de que el error era por la forma en la que estabas borrando los datos de la DB
                // El borrado solo era temporal pero no se aplicaba por eso salia 1 siempre
                if($trunks->Count() > 0 && Platform::whereNotNull('ID_ESTACION')->where('ID_ESTACION','=',$random->id_estacion)->count() == 0 ) { // esto es para garantizar que la estacion tenga asociada una troncal
                    $platform->id_estacion = $random->id_estacion;
                    $platform->id_troncal = $trunks->random()->id_troncal;
                    $platform->numero_plataforma=1;
                    $platform->save();
                } else
                    $platform->delete(); // si ya se asigno la estacion se borra el registro de la plataforma creada
            }
        });

    }
}
