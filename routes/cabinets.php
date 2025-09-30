<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CabinetController;

Route::resource('cabinets', CabinetController::class)
->middleware('permission:cabinets.manage');
