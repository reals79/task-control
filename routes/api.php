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

Route::namespace('Auth')->group(function () {
    Route::get('permissions', 'PermissionController')->name('permission');
    Route::get('roles', 'RoleController')->name('roles');

    Route::get('permissions/get', 'PermissionController@get')->name('get-permission');
    Route::get('roles/get', 'RoleController@get')->name('get-roles');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
})->middleware('verified');
