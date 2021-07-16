<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;

//Route::get('/{key}', [IndexController::class,'index']);
Route::get('/cc', [IndexController::class,'cc']);
//Route::get('/{key}', [IndexController::class,'index']);
