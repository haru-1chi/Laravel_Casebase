<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResultController;
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
Route::get('/', [AdminController::class, 'index'])->name('index');
Route::get('about',function () {
    return view('about');
})->name('about');

Route::get('CBR_form',[AdminController::class,'CBR_form'])->name('CBR_form');
Route::post('/result', [ResultController::class, 'showResult'])->name('result');
Route::fallback(function () {
    return "<h1>ไม่พบหน้าเว็บ</h1>";

});