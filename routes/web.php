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

/*Route::get('/', function () {
    return view('comun.layout');
});*/
Route::get('/','ShowController@showMain');
Route::get('/classesAll/{clase}', 'ShowController@showAll');
//Route::get('/subasta','SubastaController@index');
Route::get('/api','ApiController@index');
//Route::get('/extract','ExtractController@treatJson');
Route::get('/items','ApiController@items');