<?php

use Illuminate\Support\Facades\Route;
use Modules\PublicPage\Http\Controllers\PublicPageController;

Route::get('/', [PublicPageController::class, 'index'])->name('app.index');
