<?php

namespace Modules\Miscellaneous\Exceptions;

use Exception;

class NIOGGAppException extends Exception
{
  /**
   * Report the exception.
   *
   * @return bool|null
   */
  public function report()
  {
    logger()->error($this->getMessage(), [
      'LOGGED_IN_USER' => request()->user()?->full_name,
      'LOGGED_IN_USER_EMAIL' => request()->user()?->email,
      'LOGGED_IN_USER_ROLE' => request()->user()?->getType(),
      'CURRENT_URL' => request()->url(),
      'TRACE' => $this->getTrace(), //NOTE: JSONLite extension can format this part of the log file for better clarity.
    ]);
  }

  /**
   * Render the exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function render($request)
  {
    return $request->header('X-Inertia')
      ? back()->withFlash(['error' => $this->getMessage()])
      : redirect()->route($request->user()?->getDashboardRoute() ?? 'app.index')->withFlash(['error' => $this->getMessage()]);
  }
}
