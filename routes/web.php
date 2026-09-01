<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\AgendaController;
use App\Http\Controllers\Public\BeritaController;
use App\Http\Controllers\Public\GaleriController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\KontakController;
use App\Http\Controllers\Public\LayananController;
use App\Http\Controllers\Public\PageController as PublicPageController;
use App\Http\Controllers\Public\PegawaiController as PublicPegawaiController;
use App\Http\Controllers\Public\PengumumanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profil/{slug}', [PublicPageController::class, 'show'])->name('public.profil.show');
Route::get('/pegawai', [PublicPegawaiController::class, 'index'])->name('public.pegawai.index');

Route::get('/berita', [BeritaController::class, 'index'])->name('public.berita.index');
Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('public.berita.show');
Route::post('/berita/{slug}/suka', [BeritaController::class, 'like'])->name('public.berita.like');

Route::get('/agenda', [AgendaController::class, 'index'])->name('public.agenda.index');
Route::get('/agenda/{slug}', [AgendaController::class, 'show'])->name('public.agenda.show');

Route::get('/galeri', [GaleriController::class, 'index'])->name('public.galeri.index');
Route::get('/galeri/{slug}', [GaleriController::class, 'show'])->name('public.galeri.show');

Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('public.pengumuman.index');
Route::get('/pengumuman/{slug}', [PengumumanController::class, 'show'])->name('public.pengumuman.show');

Route::get('/layanan', [LayananController::class, 'index'])->name('public.layanan.index');
Route::get('/layanan/cek-status', [LayananController::class, 'checkStatus'])->name('public.layanan.check-status');
Route::post('/layanan/cek-status', [LayananController::class, 'lookupStatus'])->name('public.layanan.lookup-status');
Route::get('/layanan/{slug}', [LayananController::class, 'show'])->name('public.layanan.show');
Route::post('/layanan/{slug}', [LayananController::class, 'store'])->name('public.layanan.store');

Route::get('/kontak', [KontakController::class, 'index'])->name('public.kontak.index');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
