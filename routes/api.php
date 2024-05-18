<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuDijualController;
use App\Http\Controllers\BukuKolaborasiController;
use App\Http\Controllers\BukuLunasUserController;
use App\Http\Controllers\BukuPermohonanTerbitController;
use App\Http\Controllers\ConfigWebController;
use App\Http\Controllers\JasaTambahanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\KontenEventController;
use App\Http\Controllers\KontenFaqController;
use App\Http\Controllers\PaketPenerbitanController;
use App\Http\Controllers\TestimoniPembeliController;
use App\Http\Controllers\TransaksiKolaborasiBukuController;
use App\Http\Controllers\TransaksiPaketPenerbitanController;
use App\Http\Controllers\TransaksiPenjualanBukuController;
use App\Http\Controllers\UserBabBukuKolaborasiController;
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
    // hubungi kami
    Route::post('hubungi-kami', [AuthController::class, 'hubungiKami']);
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
    // get count all item in keranjang
    Route::get('count', [KeranjangController::class, 'count']);
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

// transaksi pembelian bab buku kolaborasi
Route::group(['prefix' => 'transaksi-buku-kolaborasi', 'middleware' => ['auth:sanctum', 'verified']], function () {
    // index all
    Route::get('all', [TransaksiKolaborasiBukuController::class, 'index']);
    // detail transaksi
    Route::get('detail/{id}', [TransaksiKolaborasiBukuController::class, 'show']);
    // add
    Route::post('add', [TransaksiKolaborasiBukuController::class, 'store']);
    // delete transaksi
    Route::put('gagal', [TransaksiKolaborasiBukuController::class, 'gagal']);
    // upload bukti pembayaran
    Route::post('upload-bukti-pembayaran/{id}', [TransaksiKolaborasiBukuController::class, 'uploadBuktiPembayaran']);
});

// transaksi paket penerbitan
Route::group(['prefix' => 'transaksi-paket-penerbitan', 'middleware' => ['auth:sanctum', 'verified']], function () {
    // add
    Route::post('add', [TransaksiPaketPenerbitanController::class, 'store']);
    // index all
    Route::get('all', [TransaksiPaketPenerbitanController::class, 'index']);
    // detail transaksi
    Route::get('detail/{id}', [TransaksiPaketPenerbitanController::class, 'show']);
    // status failed transaksi
    Route::put('gagal', [TransaksiPaketPenerbitanController::class, 'gagal']);
    // status failed but want to transaksi again
    Route::get('transaction-again/{id}', [TransaksiPaketPenerbitanController::class, 'transactionAgain']);
    // upload bukti pembayaran dp
    Route::post('upload-bukti-pembayaran/{id}', [TransaksiPaketPenerbitanController::class, 'uploadBuktiPembayaran']);
    // detail paket
    Route::get('detail-paket/{id}', [PaketPenerbitanController::class, 'show']);
    // JasaTambahan
    // all jasa
    Route::get('jasa-tambahan/{id}', [JasaTambahanController::class, 'index']);
});

// koleksi-buku-user
Route::group(['prefix' => 'koleksi-buku-user', 'middleware' => ['auth:sanctum', 'verified']], function () {
    // get all koleksi buku user
    Route::get('all', [BukuLunasUserController::class, 'index']);
    // download buku base buku_dijual id
    Route::get('download/{id}', [BukuLunasUserController::class, 'download']);
    // add testimoni
    Route::post('add-testimoni/{id}', [TestimoniPembeliController::class, 'store']);
});

// koleksi-buku-kolaborasi-user
Route::group(['prefix' => 'koleksi-buku-kolaborasi-user', 'middleware' => ['auth:sanctum', 'verified']], function () {
    // get all koleksi buku user
    Route::get('all', [UserBabBukuKolaborasiController::class, 'index']);
    // detail
    Route::get('detail/{id}', [UserBabBukuKolaborasiController::class, 'show']);
    // upload file mou
    Route::post('uploadMou/{id}', [UserBabBukuKolaborasiController::class, 'uploadMou']);
    // download file_hak_cipta
    Route::get('downloadFileHakCipta/{id}', [BukuKolaborasiController::class, 'downloadFileHakCipta']);
    // gagal bcs time exp
    Route::put('failedKolaborasi/{id}', [UserBabBukuKolaborasiController::class, 'failedKolaborasi']);
    // upload bab file
    Route::post('uploadBab/{id}', [UserBabBukuKolaborasiController::class, 'uploadBab']);
});

// koleksi-buku-penerbitan-user
Route::group(['prefix' => 'koleksi-buku-penerbitan-user', 'middleware' => ['auth:sanctum', 'verified']], function () {
    // get all koleksi penerbitan buku user
    Route::get('all', [BukuPermohonanTerbitController::class, 'index']);
    // detail
    Route::get('detail/{id}', [BukuPermohonanTerbitController::class, 'show']);
    // download buku base buku_dijual id
    Route::get('download/{id}', [BukuPermohonanTerbitController::class, 'download']);
});

// mou
Route::group(['prefix' => 'mou', 'middleware' => ['auth:sanctum', 'verified']], function () {
    // download file mou
    Route::get('downloadMou/{filter}', [UserBabBukuKolaborasiController::class, 'downloadMou']);
});

// config
Route::group(['prefix' => 'config', 'middleware' => ['auth:sanctum', 'verified']], function () {
    // download file mou
    Route::get('getRekening', [ConfigWebController::class, 'getRekening']);
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
