<?php

namespace Modules\UserAuth\Http\Controllers;

use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
  public function create(): Response
  {
    abort(404);
  }

  /**
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(Request $request): RedirectResponse
  {
    abort(404);
  }
}
