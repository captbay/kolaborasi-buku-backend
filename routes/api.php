<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuDijualController;
use App\Http\Controllers\BukuKolaborasiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\KontenEventController;
use App\Http\Controllers\KontenFaqController;
use App\Http\Controllers\PaketPenerbitanController;
use App\Http\Controllers\TestimoniPembeliController;
use App\Http\Controllers\TransaksiPenjualanBukuController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
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

// broadcast
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// API without login
Route::group(['prefix' => 'auth'], function () {
    // login
    Route::post('login', [AuthController::class, 'login']);
    // register
    Route::post('register', [AuthController::class, 'register']);
    // verif and resend email
    Route::get('email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify'); // Make sure to keep this as your route name
    Route::post('email/resend', [AuthController::class, 'resendEmailVerification'])->name('verification.resend');
    // forgot password
    Route::post('forgotPassword', [AuthController::class, 'sendEmailForgotPassword'])->middleware('guest')->name('password.email');
    // reset password
    Route::post('resetPassword', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.reset');
});

// logout
Route::group(['prefix' => 'auth', 'middleware' => ['auth:sanctum', 'verified']], function () {
    //logout
    Route::get('logout', [AuthController::class, 'logout']);
    // change password
    Route::put('changePassword', [AuthController::class, 'changePassword']);
});

Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum', 'verified']], function () {
    //show data user by id
    Route::get('show/{id}', [UserController::class, 'show']);
    // update data user
    Route::put('update', [UserController::class, 'update']);
    // post member
    Route::post('uploadFileMember', [UserController::class, 'uploadFileMember']);
    // post photo profile
    Route::post('uploadFotoProfil', [UserController::class, 'uploadFotoProfil']);
    // notifikasi user
    Route::get('notifikasi', [UserController::class, 'notifikasi']);
    // baca notifikasi user
    Route::put('notifikasi/read', [UserController::class, 'readNotifikasi']);
    // hapus notifikasi
    Route::delete('notifikasi/delete', [UserController::class, 'deleteNotifikasi']);
});

// keranjang
Route::group(['prefix' => 'keranjang', 'middleware' => ['auth:sanctum', 'verified']], function () {
    // get all keranjang
    Route::get('all', [KeranjangController::class, 'index']);
    // add to keranjang
    Route::post('add', [KeranjangController::class, 'store']);
    // delete keranjang
    Route::delete('delete/{id}', [KeranjangController::class, 'destroy']);
});

// transaksi pembelian buku_dijual
Route::group(['prefix' => 'transaksi-buku-dijual', 'middleware' => ['auth:sanctum', 'verified']], function () {
    // index all
    Route::get('all', [TransaksiPenjualanBukuController::class, 'index']);
    // detail transaksi
    Route::get('detail/{id}', [TransaksiPenjualanBukuController::class, 'show']);
    // add transaksi
    Route::post('add', [TransaksiPenjualanBukuController::class, 'store']);
    // delete transaksi
    Route::put('gagal', [TransaksiPenjualanBukuController::class, 'gagal']);
    // upload bukti pembayaran
    Route::post('upload-bukti-pembayaran/{id}', [TransaksiPenjualanBukuController::class, 'uploadBuktiPembayaran']);
});

// buku
Route::group(['prefix' => 'buku'], function () {
    // get all buku
    Route::get('all', [BukuDijualController::class, 'index']);
    // get buku by slug
    Route::get('detail/{slug}', [BukuDijualController::class, 'show']);
    // terlaris
    Route::get('best-seller', [BukuDijualController::class, 'bestseller']);
});

// kolaborasi
Route::group(['prefix' => 'buku-kolaborasi'], function () {
    // get all kolaborasi
    Route::get('all', [BukuKolaborasiController::class, 'index']);
    // get kolaborasi by slug
    Route::get('detail/{slug}', [BukuKolaborasiController::class, 'show']);
});

//* DONE!
// event
Route::group([
    'prefix' => 'event'
], function () {
    // get all event
    Route::get('all', [KontenEventController::class, 'index']);
});

//* DONE!
// testimoni
Route::group(['prefix' => 'testimoni'], function () {
    // get all testimoni
    Route::get('all', [TestimoniPembeliController::class, 'index']);
});

//* DONE!
// testimoni
Route::group(['prefix' => 'kategori'], function () {
    // get all testimoni
    Route::get('all', [KategoriController::class, 'index']);
});

//* DONE!
// FAQ
Route::group(['prefix' => 'faq'], function () {
    // get all faq
    Route::get('all', [KontenFaqController::class, 'index']);
});

//* DONE!
// paket
Route::group(['prefix' => 'paket'], function () {
    // get all paket
    Route::get('all', [PaketPenerbitanController::class, 'index']);
});
