<?php

use App\Http\Controllers\Api\V1\BeritaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Media Center → WordPress (Laravel Sanctum)
|--------------------------------------------------------------------------
|
| Base URL production: https://mediacenter.rejanglebongkab.go.id/api/v1
| Header wajib: Authorization: Bearer {token}
|
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/berita/terbaru', [BeritaController::class, 'latest'])->name('api.v1.berita.terbaru');
    Route::get('/berita', [BeritaController::class, 'index'])->name('api.v1.berita.index');
    Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('api.v1.berita.show');
});
