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
//Route::any('hello', function ()
//{
//    $resolve = resolve('hello');
//    var_dump(get_class($resolve));
//});

Route::any('hello', function (\App\Contracts\Interfaces\Sms $sms)
{
//    var_dump(get_class($sms));
    var_dump($sms);
});