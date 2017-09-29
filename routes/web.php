<?php

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

use App\Breadcrumbs; 

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sistema/administrators/create', function () {
    $breadcrumbs = new Breadcrumbs(
        app()['request'],
        app()['config'],
        app()['router']
    );

    return dd($breadcrumbs->getLinks());
});

Route::resource('administrators', AdministratorController::class);
