<?php

use App\Http\Controllers\Server\GetLocations;
use App\Http\Controllers\Server\Index;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/servers', Index::class)->name('servers.index');
Route::get('/servers/locations', GetLocations::class)->name('servers.locations');
