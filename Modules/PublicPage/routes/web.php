<?php

use Illuminate\Support\Facades\Route;
use Modules\PublicPage\Http\Controllers\PublicPageController;

Route::get('/', [PublicPageController::class, 'index'])->name('app.index');
Route::get('/about-us', [PublicPageController::class, 'index'])->name('app.about');
Route::get('/contact-us', [PublicPageController::class, 'index'])->name('app.contact');
