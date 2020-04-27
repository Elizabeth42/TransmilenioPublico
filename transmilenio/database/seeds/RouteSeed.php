<?php

use Illuminate\Database\Seeder;

class RouteSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Route::class, 3)->create()->each(function ($route) {
            $route->save();
        });
    }
}
