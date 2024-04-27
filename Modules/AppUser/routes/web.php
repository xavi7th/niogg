<?php

use Illuminate\Support\Facades\Route;
use Modules\AppUser\Http\Controllers\AppUserController;

Route::group([], function (): void {
  Route::resource('appuser', AppUserController::class)->names('appuser');
});
