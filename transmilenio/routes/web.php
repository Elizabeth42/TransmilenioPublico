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

Route::resource('station', 'StationController');
Route::get('trunk/{trunk}/station', 'StationTrunkController@get_stations_from_trunk');
Route::post('trunk/{trunk}/station', 'StationTrunkController@add_stations_to_trunk');
Route::put('trunk/{trunk}/station', 'StationTrunkController@delete_station_to_trunk');
Route::resource('trunk', 'TrunkController');
Route::resource('portal', 'PortalController');
