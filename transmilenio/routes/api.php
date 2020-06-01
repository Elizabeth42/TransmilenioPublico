<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// estas rutas permitiran establecer la CRUD basica de los modelos
Route::resource('trunk', 'TrunkController');
Route::resource('station', 'StationController');
Route::resource('trunkStation', 'TrunkStationController');
Route::resource('portal', 'PortalController');
Route::resource('platform', 'PlatformController');
Route::resource('wagon', 'WagonController');
Route::resource('route', 'RouteController');
Route::get('route/{route}/wagon', 'StopController@get_wagons_from_route');
Route::post('route/{route}/wagon', 'StopController@add_wagons_to_route');
Route::put('route/{route}/wagon', 'StopController@modify_wagons_to_route');
Route::delete('route/{route}/wagon/{wagon}', 'StopController@delete_wagon_to_route');
Route::resource('bustype', 'BusTypeController');
Route::resource('schedule', 'ScheduleController');
Route::resource('bus', 'BusController');
Route::resource('assignment', 'TimeRouteAssignmentController');
Route::resource('travel', 'TravelController');
/*
 * Permite generar aleatoriamente la cantidad de elementos que se ingresan por parametro, sin embargo,
 * estos elementos solo se generan no se almacenan ni en un archivo ni se guardan en algun medio
 * */
Route::get('trunk/factory/{amount}', 'TrunkController@getRandom');
Route::get('station/factory/{amount}', 'StationController@getRandom');
Route::get('trunkStation/factory/{amount}', 'TrunkStationController@getRandom');
Route::get('portal/factory/{amount}', 'PortalController@getRandom');
Route::get('platform/factory/{amount}', 'PlatformController@getRandom');
Route::get('wagon/factory/{amount}', 'WagonController@getRandom');
Route::get('route/factory/{amount}', 'RouteController@getRandom');
Route::get('bustype/factory/{amount}', 'BusTypeController@getRandom');
Route::get('bus/factory/{amount}', 'BusController@getRandom');
Route::get('schedule/factory/{amount}', 'ScheduleController@getRandom');
Route::get('assignment/factory/{amount}', 'TimeRouteAssignmentController@getRandom');
Route::get('travel/factory/{amount}', 'TravelController@getRandom');
Route::get('stop/factory/{amount}', 'StopController@getRandom');

/*
 * Este grupo de rutas se encarga generar automaticamente la cantidad de elementos que le ingrese por parametro,
 * es decir, genera los elementos automaticamente y los almacena directamente en la base de datos
 */
Route::get('trunk/factory/save/{amount}', 'TrunkController@saveRandom');
Route::get('station/factory/save/{amount}', 'StationController@saveRandom');
Route::get('trunkStation/factory/save/{amount}', 'TrunkStationController@saveRandom');
Route::get('portal/factory/save/{amount}', 'PortalController@saveRandom');
Route::get('platform/factory/save/{amount}', 'PlatformController@saveRandom');
Route::get('wagon/factory/save/{amount}', 'WagonController@saveRandom');
Route::get('route/factory/save/{amount}', 'RouteController@saveRandom');
Route::get('bustype/factory/save/{amount}', 'BusTypeController@saveRandom');
Route::get('bus/factory/save/{amount}', 'BusController@saveRandom');
Route::get('schedule/factory/save/{amount}', 'ScheduleController@saveRandom');
Route::get('assignment/factory/save/{amount}', 'TimeRouteAssignmentController@saveRandom');
Route::get('travel/factory/save/{amount}', 'TravelController@saveRandom');
Route::get('stop/factory/save/{amount}', 'StopController@saveRandom');

/*
 * Estas rutas sirven para que a partir de un archivo tipo Json pueda ser cargado a la base de datos,
 * si alguno de los elementos no es valido se registrara el debido error
 * */
Route::post('trunk/factory/fill', 'TrunkController@fillFromJson');
Route::post('station/factory/fill', 'StationController@fillFromJson');
Route::post('trunkStation/factory/fill', 'TrunkStationController@fillFromJson');
Route::post('portal/factory/fill', 'PortalController@fillFromJson');
Route::post('platform/factory/fill', 'PlatformController@fillFromJson');
Route::post('wagon/factory/fill', 'WagonController@fillFromJson');
Route::post('route/factory/fill', 'RouteController@fillFromJson');
Route::post('bustype/factory/fill', 'BusTypeController@fillFromJson');
Route::post('bus/factory/fill', 'BusController@fillFromJson');
Route::post('schedule/factory/fill', 'ScheduleController@fillFromJson');
Route::post('assignment/factory/fill', 'TimeRouteAssignmentController@fillFromJson');
Route::post('travel/factory/fill', 'TravelController@fillFromJson');
Route::post('stop/factory/fill', 'StopController@fillFromJson');

/*
 * estas rutas permitiran descargar un documento json con cierta cantidad de elementos aleatorios que se especifique
 * por parametro
 */
Route::get('trunk/factory/get/{amount}', 'TrunkController@saveFactoryJson');
Route::get('station/factory/get/{amount}', 'StationController@saveFactoryJson');
Route::get('trunkStation/factory/get/{amount}', 'TrunkStationController@saveFactoryJson');
Route::get('portal/factory/get/{amount}', 'PortalController@saveFactoryJson');
Route::get('platform/factory/get/{amount}', 'PlatformController@saveFactoryJson');
Route::get('wagon/factory/get/{amount}', 'WagonController@saveFactoryJson');
Route::get('route/factory/get/{amount}', 'RouteController@saveFactoryJson');
Route::get('bustype/factory/get/{amount}', 'BusTypeController@saveFactoryJson');
Route::get('bus/factory/get/{amount}', 'BusController@saveFactoryJson');
Route::get('schedule/factory/get/{amount}', 'ScheduleController@saveFactoryJson');
Route::get('assignment/factory/get/{amount}', 'TimeRouteAssignmentController@saveFactoryJson');
Route::get('travel/factory/get/{amount}', 'TravelController@saveFactoryJson');
Route::get('stop/factory/get/{amount}', 'StopController@saveFactoryJson');
/*
 * Esta ruta permite descargar los datos de un modelo especifico de la base de datos
 */
Route::get('/download/{model}', 'Controller@download');
