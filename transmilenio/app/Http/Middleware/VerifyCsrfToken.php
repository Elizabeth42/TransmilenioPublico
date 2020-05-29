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
//        "station",
//        "station/*",
//        "trunk",
//        "trunk/*",
//        "trunkStation",
//        "trunkStation/*",
//        "portal",
//        "portal/*",
//        "platform",
//        "platform/*",
//        "wagon",
//        "wagon/*",
//        "route",
//        "route/*",
//        "busType",
//        "busType/*",
//        "schedule",
//        "schedule/*",
//        "bus",
//        "bus/*",
//        "route_wagon/*",
//        "assignment",
//        "assignment/*",
//        "travel",
//        "travel/*"
        //
    ];
}
