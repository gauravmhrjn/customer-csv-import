<?php

use App\Http\Controllers\ConvertCsvController;
use Illuminate\Support\Facades\Route;

Route::get('/convert/csv', ConvertCsvController::class);
