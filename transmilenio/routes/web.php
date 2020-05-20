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
Route::get('bus/factory/{amount}', 'BusController@getRandom');
Route::get('busType/factory/{amount}', 'BusTypeController@getRandom');
Route::get('schedule/factory/{amount}', 'ScheduleController@getRandom');
Route::get('assignment/factory/{amount}', 'TimeRouteAssignmentController@getRandom');
Route::get('travel/factory/{amount}', 'TravelController@getRandom');
Route::get('stop/factory/{amount}', 'StopController@getRandom');
Route::get('route/factory/{amount}', 'RouteController@getRandom');
Route::get('route/factory/save/{amount}', 'RouteController@saveRandom');
Route::post('trunk/factory/fill', 'TrunkController@fillFromJson');
Route::post('wagon/factory/fill', 'WagonController@fillFromJson');
