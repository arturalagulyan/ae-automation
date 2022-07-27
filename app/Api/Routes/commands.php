<?php

use Illuminate\Support\Facades\Route;

Route::post('/commands', \Api\Http\Controllers\CommandsController::class . '@command')->name('api.commands');
