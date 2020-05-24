<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::resource('trunk', 'TrunkController');
Route::resource('station', 'StationController');
Route::resource('trunkStation', 'TrunkStationController')->except('PATCH');
//Route::get('trunk/{trunk}/station', 'StationTrunkController@get_stations_from_trunk');
//Route::post('trunk/{trunk}/station', 'StationTrunkController@add_stations_to_trunk');
//Route::put('trunk/{trunk}/station', 'StationTrunkController@delete_station_to_trunk');
Route::resource('portal', 'PortalController');
Route::resource('platform', 'PlatformController');
Route::resource('wagon', 'WagonController');
Route::resource('route', 'RouteController');
Route::get('route/{route}/wagon', 'StopController@get_wagons_from_route');
Route::post('route/{route}/wagon', 'StopController@add_wagons_to_route');
Route::put('route/{route}/wagon', 'StopController@modify_wagons_to_route');
Route::delete('route/{route}/wagon/{wagon}', 'StopController@delete_wagon_to_route');
Route::resource('busType', 'BusTypeController');
Route::resource('schedule', 'ScheduleController');
Route::resource('bus', 'BusController');
Route::resource('assignment', 'TimeRouteAssignmentController');
Route::resource('travel', 'TravelController');

Route::get('trunk/factory/{amount}', 'TrunkController@getRandom');
Route::get('station/factory/{amount}', 'StationController@getRandom');
Route::get('trunkStation/factory/{amount}', 'TrunkStationController@getRandom');
Route::get('portal/factory/{amount}', 'PortalController@getRandom');
Route::get('platform/factory/{amount}', 'PlatformController@getRandom');
Route::get('wagon/factory/{amount}', 'WagonController@getRandom');
Route::get('route/factory/{amount}', 'RouteController@getRandom');
Route::get('busType/factory/{amount}', 'BusTypeController@getRandom');
Route::get('bus/factory/{amount}', 'BusController@getRandom');
Route::get('schedule/factory/{amount}', 'ScheduleController@getRandom');
Route::get('assignment/factory/{amount}', 'TimeRouteAssignmentController@getRandom');
Route::get('travel/factory/{amount}', 'TravelController@getRandom');
Route::get('stop/factory/{amount}', 'StopController@getRandom');

Route::get('trunk/factory/save/{amount}', 'TrunkController@saveRandom');
Route::get('station/factory/save/{amount}', 'StationController@saveRandom');
Route::get('trunkStation/factory/save/{amount}', 'TrunkStationController@saveRandom');
Route::get('portal/factory/save/{amount}', 'PortalController@saveRandom');
Route::get('platform/factory/save/{amount}', 'PlatformController@saveRandom');
Route::get('wagon/factory/save/{amount}', 'WagonController@saveRandom');
Route::get('route/factory/save/{amount}', 'RouteController@saveRandom');
Route::get('busType/factory/save/{amount}', 'BusTypeController@saveRandom');
Route::get('bus/factory/save/{amount}', 'BusController@saveRandom');
Route::get('schedule/factory/save/{amount}', 'ScheduleController@saveRandom');
Route::get('assignment/factory/save/{amount}', 'TimeRouteAssignmentController@saveRandom');
Route::get('travel/factory/save/{amount}', 'TravelController@saveRandom');
Route::get('stop/factory/save/{amount}', 'StopController@saveRandom');

Route::post('trunk/factory/fill', 'TrunkController@fillFromJson');
Route::post('station/factory/fill', 'StationController@fillFromJson');
Route::post('trunkStation/factory/fill', 'TrunkStationController@fillFromJson');
Route::post('portal/factory/fill', 'PortalController@fillFromJson');
Route::post('platform/factory/fill', 'PlatformController@fillFromJson');
Route::post('wagon/factory/fill', 'WagonController@fillFromJson');
Route::post('route/factory/fill', 'RouteController@fillFromJson');
Route::post('busType/factory/fill', 'BusTypeController@fillFromJson');
Route::post('bus/factory/fill', 'BusController@fillFromJson');
Route::post('schedule/factory/fill', 'ScheduleController@fillFromJson');
Route::post('assignment/factory/fill', 'TimeRouteAssignmentController@fillFromJson');
Route::post('travel/factory/fill', 'TravelController@fillFromJson');
Route::post('stop/factory/fill', 'StopController@fillFromJson');

/*estas rutas seran para descargar los datos desde la base de datos
lo unico es pasarle los modelos por parametro, por ejemplo
http://localhost:8000/download/route
http://localhost:8000/download/wagon
http://localhost:8000/download/stop
http://localhost:8000/download/trunk
*/
Route::get('/download/{model}', function ($model) {
    if($model == 'stop')
    {
        $content = collect();
        foreach (\App\Route::all() as $route)
            $content->add([ $route->getKey() => $route->wagons()->withPivot('estado_parada', 'orden')->get()]);
    }
    else {
        $instance = '\\App\\'.\Illuminate\Support\Str::studly($model);
        $content = $instance::all();
    }
    return response($content)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'Content-disposition' => 'attachment; filename='.$model.'s.json'
        ]);
});

