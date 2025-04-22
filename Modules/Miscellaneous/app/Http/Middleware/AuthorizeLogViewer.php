<?php

namespace Modules\Miscellaneous\Http\Middleware;

use Illuminate\Support\Facades\App;

class AuthorizeLogViewer
{
  public function handle($request, $next)
  {
    if (App::isProduction() && ! $request->user()) {
      abort(404, 'Resource not allocated');
    }

    return $next($request);
  }
}
