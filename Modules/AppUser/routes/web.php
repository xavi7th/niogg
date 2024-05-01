<?php

use Illuminate\Support\Facades\Route;
use Modules\AppUser\Http\Controllers\AppUserProfileController;
use Modules\AppUser\Http\Controllers\AppUserDashboardController;

Route::middleware('auth')->name('appuser.')->prefix('user/')->group(function (): void {
  Route::get('dashboard', AppUserDashboardController::class)->middleware(['auth', 'verified:auth.verification.notice'])->name('dashboard');

  Route::get('profile', [AppUserProfileController::class, 'edit'])->name('profile.edit')->middleware('password.confirm:auth.password.confirm');
  Route::patch('profile', [AppUserProfileController::class, 'update'])->name('profile.update');
  Route::delete('profile', [AppUserProfileController::class, 'destroy'])->name('profile.destroy');
});
