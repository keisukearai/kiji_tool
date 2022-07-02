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

Route::get('/', function () {
    return view('welcome');
});

// 初期表示
Route::get('/kiji', 'KijiToolController@index');
// 計算ボタン押下処理
Route::post('/kiji_calc', 'KijiToolController@calc');