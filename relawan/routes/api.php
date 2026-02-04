<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KegiatanController;

// Route Testing (Tanpa Auth, Tanpa CSRF)
Route::post('/kegiatan/store', [KegiatanController::class, 'store']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
