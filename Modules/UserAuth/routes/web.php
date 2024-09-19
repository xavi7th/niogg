<?php

use Illuminate\Support\Facades\Route;
use Modules\UserAuth\Http\Controllers\PasswordController;
use Modules\UserAuth\Http\Controllers\NewPasswordController;
use Modules\UserAuth\Http\Controllers\VerifyEmailController;
use Modules\UserAuth\Http\Controllers\RegisteredUserController;
use Modules\UserAuth\Http\Controllers\PasswordResetLinkController;
use Modules\UserAuth\Http\Controllers\ConfirmablePasswordController;
use Modules\UserAuth\Http\Controllers\AuthenticatedSessionController;
use Modules\UserAuth\Http\Controllers\EmailVerificationPromptController;
use Modules\UserAuth\Http\Controllers\EmailVerificationNotificationController;

Route::name('auth.')->prefix('auth/v1')->group(function (): void {
  Route::middleware('guest')->group(function (): void {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
  });

  Route::middleware('auth')->group(function (): void {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
  });
});
