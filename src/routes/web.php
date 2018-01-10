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


Auth::routes();


Route::get('/', function () {
    return view('home');
});

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/dashboard', 'ProjectController@dashboard');
Route::get('/project/add', 'ProjectController@add');
Route::post('/project/add', 'ProjectController@create');
Route::get('/project/delete/{project}', 'ProjectController@delete');

Route::get('/tool/version/{project}/{environment}', 'ToolController@version');
Route::get('/tool/database/snapshot/{project}/{environment}', 'ToolController@dbSnapshot');
Route::get('/tool/database/info/{project}/{environment}', 'ToolController@dbSnapshotInfo');
Route::get('/tool/media/snapshot/{project}/{environment}', 'ToolController@mediaSnapshot');
Route::get('/tool/media/info/{project}/{environment}', 'ToolController@mediaSnapshotInfo');
Route::get('/tool/update/{project}/{environment}', 'ToolController@update');

