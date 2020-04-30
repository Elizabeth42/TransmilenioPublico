<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "station",
        "station/*",
        "trunk",
        "trunk/*",
        "trunk_station/*",
        "portal",
        "portal/*",
        "platform",
        "platform/*",
        //
    ];
}
