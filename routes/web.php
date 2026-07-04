<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AuthUser;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
// Halaman utama / login (Bisa diakses siapa saja)
Route::get('/', [AuthUser::class, 'index'])->name('login_page.index')->name('login');
Route::post('/login/proses', [AuthUser::class, 'login'])->name('login.proses.index');
Route::post('/logout', [AuthUser::class, 'logout'])->name('logout.index');

// Halaman-halaman yang WAJIB login terlebih dahulu
Route::middleware(['auth'])->group(function () {
Route::get('/chat', [ChatController::class, 'chat'])->name('chat.index');
Route::get('/newchat', [ChatController::class, 'newchat'])->name('chat.newchat');
Route::get('/newchat/list', [ChatController::class, 'listcontact'])->name('chat.listcontact');
Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
Route::post('/chat/kirim', [ChatController::class, 'kirim'])->name('chat.kirim');
Route::get('/chat/ambil', [ChatController::class, 'ambilData'])->name('chat.ambil');
Route::get('/chat/list', [ChatController::class, 'listpesan'])->name('chat.list');
Route::get('/chat/filter', [ChatController::class, 'listfilter'])->name('chat.filter');
Route::get('/main', [ChatController::class, 'index'])->name('chat1.index');
Route::get('/setting/profile', [SettingController::class, 'profile'])->name('setting.profile');
Route::get('/setting/notification', [SettingController::class, 'notification'])->name('setting.notification');
Route::get('/setting/account', [SettingController::class, 'account'])->name('setting.account');
Route::post('/setting/upfoto', [SettingController::class, 'upfoto'])->name('setting.upfoto');
Route::post('/setting/update', [SettingController::class, 'update'])->name('setting.update');
});