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


View::composer('*', function($view){
    $view_name = str_replace('.', '-', $view->getName());
    View::share('view_name', $view_name);
}); 


Route::redirect('/', '/login');
Route::redirect('/home', '/dashboard');

// Route::get('/home', 'HomeController@index')->name('home');

Route::get('/dashboard', 'ProjectController@dashboard');
Route::get('/project/add', 'ProjectController@add');
Route::post('/project/add', 'ProjectController@create');
Route::get('/project/delete/{project}', 'ProjectController@delete');

Route::get('/tool/version/{project}/{environment}', 'ToolController@version');
Route::get('/tool/database/snapshot/{project}/{environment}', 'ToolController@dbSnapshot');
Route::get('/tool/database/info/{project}/{environment}', 'ToolController@dbSnapshotInfo');
Route::get('/tool/database/pull/{project}', 'ToolController@dbSnapshotPull');
Route::get('/tool/database/download/{project}/{environment}', 'ToolController@dbSnapshotDownload');
Route::get('/tool/database/restore/{project}', 'ToolController@dbSnapshotRestore');
Route::get('/tool/media/snapshot/{project}/{environment}', 'ToolController@mediaSnapshot');
Route::get('/tool/media/info/{project}/{environment}', 'ToolController@mediaSnapshotInfo');
Route::get('/tool/media/pull/{project}', 'ToolController@mediaSnapshotPull');
Route::get('/tool/media/download/{project}/{environment}', 'ToolController@mediaSnapshotDownload');
Route::get('/tool/media/restore/{project}', 'ToolController@mediaSnapshotRestore');
Route::get('/tool/update/{project}/{environment}', 'ToolController@update');

