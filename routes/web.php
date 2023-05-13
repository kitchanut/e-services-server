<?php

use Illuminate\Support\Facades\Artisan;
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

Route::get('/', function () {
    // $routes = [];
    // foreach (Route::getRoutes()->getIterator() as $route) {
    //     if (strpos($route->uri, 'api') !== false) {
    //         $data['medthods'] = $route->methods;
    //         $data['uri'] = $route->uri;
    //         $res[] = $data;
    //     }
    // }
    // return response()->json($res);

    // return view('welcome', compact('routes'));
});
