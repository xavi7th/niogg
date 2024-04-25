<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
  /**
   * The list of the inputs that are never flashed to the session on validation exceptions.
   *
   * @var array<int, string>
   */
  protected $dontFlash = [
    'current_password',
    'password',
    'password_confirmation',
  ];

  public function register(): void
  {
    $this->reportable(function (Throwable $e): void {
    });
  }

  /**
   * Render an exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request  $request
   *
   * @throws Throwable
   */
  public function render($request, Throwable $exception): Response|RedirectResponse|JsonResponse
  {
    /** @var Response */
    $response = parent::render($request, $exception);

    if (in_array($response->status(), [500, 503, 404, 403, 429]) && $request->header('X-Inertia')) {
      return back()->withFlash(['error' => $exception->getMessage()]);
    }

    if (in_array($response->status(), [419]) && $request->header('X-Inertia')) {
      return back()->withFlash(['error' => 'Your session has expired. Please try again']);
    }

    return $response;
  }
}
