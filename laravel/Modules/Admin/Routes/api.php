<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:v1')->get('/admin', function (Request $request) {
    return $request->user();
});

Route::get('admin/index', 'AdminController@index');

//可选参数路由
//Route::get('admin/{id}/test/{pid?}', function ($id,$pid = null){
//    return 'Admin'.$id. ':pid->'.$pid;
//});


Route::post('admin/{action}', function (Modules\Admin\Http\Controllers\AdminController $admin, $action){
    return $admin->$action();
});
