<?php

use Illuminate\Support\Facades\Route;

Route::post('/ping', \Api\Http\Controllers\DeviceController::class . '@ping')->name('api.devices.ping');
