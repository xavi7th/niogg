<?php

use Illuminate\Support\Facades\Route;
use Modules\PublicPage\Http\Controllers\PublicPageController;

Route::get('/', [PublicPageController::class, 'index'])->name('app.index');
Route::get('/about-us', [PublicPageController::class, 'about'])->name('app.about');
Route::get('/contact-us', [PublicPageController::class, 'contact'])->name('app.contact');
Route::post('/contact-us', [PublicPageController::class, 'contactUs'])->name('app.contact.store');
