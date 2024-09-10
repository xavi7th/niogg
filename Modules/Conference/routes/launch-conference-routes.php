<?php

use Illuminate\Support\Facades\Route;
use Modules\Conference\Http\Controllers\LaunchConferenceController;
use Modules\Conference\Http\Controllers\LaunchConferenceRegistrationController;

Route::name('app.conferences.')->prefix('conferences')->group(function (): void {
  Route::get('nigeria-leadership-quest-for-economy-growth', [LaunchConferenceController::class, 'index'])->name('launch.index');

  Route::post('nigeria-leadership-quest-for-economy-growth', [LaunchConferenceRegistrationController::class, 'store'])->name('launch.store');
  Route::get('verify-launch-conferenece-registration-payment', [LaunchConferenceRegistrationController::class, 'update'])->name('launch.payment.verify');
});
