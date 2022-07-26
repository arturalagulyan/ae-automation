<?php

use Illuminate\Support\Facades\Route;

Route::post('/upload', \Api\Http\Controllers\UploadController::class . '@upload')->name('api.upload');
