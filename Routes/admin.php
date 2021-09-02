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
*/

// 魔法
Route::get('cube/list', 'CubeController@list');
Route::get('cube/ajaxList', 'CubeController@ajaxList');
Route::any('cube/edit', 'CubeController@edit');
Route::post('cube/del', 'CubeController@del');

