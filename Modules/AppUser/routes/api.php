<?php

use Illuminate\Support\Facades\Route;
use Modules\AppUser\Http\Controllers\AppUserController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function (): void {
  Route::apiResource('appuser', AppUserController::class)->names('appuser');
});
