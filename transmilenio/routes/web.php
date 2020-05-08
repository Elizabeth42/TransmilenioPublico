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
