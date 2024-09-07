<?php

use Illuminate\Support\Facades\Route;
use Modules\Conference\Http\Controllers\LaunchConferenceController;

Route::name('app.conferences.')->prefix('conferences')->group(function (): void {
  Route::get('nigeria-leadership-quest-for-economy-growth', [LaunchConferenceController::class, 'index'])->name('launch.index');
});
