<?php

use Illuminate\Support\Facades\Route;
use Modules\Conference\Http\Controllers\LaunchConferenceController;
use Modules\Conference\Http\Controllers\LaunchConferenceRegistrationController;

Route::name('app.conferences.')->prefix('conferences')->group(function (): void {
  Route::get('nigeria-leadership-quest-for-economy-growth', [LaunchConferenceController::class, 'index'])->name('launch.index');

  Route::get('nigeria-leadership-quest-for-economy-growth/registration-status', [LaunchConferenceRegistrationController::class, 'show'])->name('launch.payment.status');
  Route::post('nigeria-leadership-quest-for-economy-growth', [LaunchConferenceRegistrationController::class, 'store'])->name('launch.store');
  Route::get('verify-launch-conference-registration-payment', [LaunchConferenceRegistrationController::class, 'update'])->name('launch.payment.verify');
});

Route::middleware('auth')->name('conferences.registration.')->prefix('conferences/launch')->group(function (): void {
  Route::get('registration-transactions', [LaunchConferenceRegistrationController::class, 'index'])->name('index')->middleware('password.confirm:auth.password.confirm');
  Route::put('nigeria-leadership-quest-for-economy-growth/{reg:registration_id}', [LaunchConferenceRegistrationController::class, 'update'])->name('manual-verification');
  Route::delete('verify-launch-conference-registration-payment/{reg:registration_id}', [LaunchConferenceRegistrationController::class, 'destroy'])->name('revoke');
});
