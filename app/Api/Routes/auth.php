<?php

Route::post('/auth/session', \Api\Http\Controllers\Auth\LoginController::class . '@login')->name('api.auth.session');
Route::post('/auth/session/refresh', \Api\Http\Controllers\Auth\RefreshController::class . '@refresh')->name('api.auth.refresh');

Route::middleware('auth:api')->get('/auth/user', \Api\Http\Controllers\Auth\UserController::class . '@get')->name('api.auth.session.destroy');
Route::middleware('auth:api')->post('/auth/session/destroy', \Api\Http\Controllers\Auth\LoginController::class . '@logout')->name('api.auth.session.destroy');
Route::middleware('auth:api')->put('/auth/update/locale', \Api\Http\Controllers\Auth\UserController::class . '@updateLocale')->name('api.auth.update.locale');
