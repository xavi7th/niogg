<?php

use Illuminate\Support\Facades\Route;
use Modules\Conference\Http\Controllers\Award2024ConferenceController;
use Modules\Conference\Http\Controllers\Award2024ConferenceRegistrationController;

Route::name('app.conferences.')->prefix('conferences')->group(function (): void {
  Route::get('achievers-icon-merit-award-2024', [Award2024ConferenceController::class, 'index'])->name('award.index');
  Route::get('achievers-icon-merit-award-2024-awardees', [Award2024ConferenceController::class, 'awardees'])->name('award.awardees');

  Route::get('achievers-icon-merit-award-2024/registration-status', [Award2024ConferenceRegistrationController::class, 'show'])->name('award.payment.status');
  Route::post('achievers-icon-merit-award-2024', [Award2024ConferenceRegistrationController::class, 'store'])->name('award.store');
  Route::get('verify-launch-conference-registration-payment', [Award2024ConferenceRegistrationController::class, 'update'])->name('award.payment.verify');
});

Route::middleware('auth')->name('conferences.award.registration.')->prefix('conferences/launch')->group(function (): void {
  Route::get('registration-transactions', [Award2024ConferenceRegistrationController::class, 'index'])->name('index')->middleware('password.confirm:auth.password.confirm');
  Route::put('achievers-icon-merit-award-2024/{reg:registration_id}', [Award2024ConferenceRegistrationController::class, 'update'])->name('manual-verification');
  Route::delete('verify-achievers-award-registration-payment/{reg:registration_id}', [Award2024ConferenceRegistrationController::class, 'destroy'])->name('revoke');
});
