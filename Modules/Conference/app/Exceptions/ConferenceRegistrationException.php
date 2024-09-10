<?php

namespace Modules\Conference\Exceptions;

use Modules\Miscellaneous\Exceptions\NIOGGAppException;

class ConferenceRegistrationException extends NIOGGAppException
{
  private static $redirect_url;

  public static function conferenceTagNotFound(): self
  {
    return new self('<b>Oops!!</b> <br /> <br /> An invalid Conference Tag was sent. 🤷‍♂️😕🤷‍♀️ Reload the page and try the registration again.' .
      '<br /> <br /> <small>If this issue persists, please try the manual registration. We will look into this glitch ASAP.</small>.', 404);
  }

  public static function duplicateRegistrationAttempt(string $conference_desc): self
  {
    return new self('<h6>Duplicate Registration!!</h6> <br /> <br /> You already have a confirmed registration for the <b><i>' . $conference_desc . '</i></b>. <br /> ' .
                    '<br /> <small>If you believe this to be an error, contact us immediately or try again with another email address..</small>.', 422);
  }

  public static function paymentError(string $conference_description, ?string $redirect_url = NULL): self
  {
    self::$redirect_url = $redirect_url;

    return new self('<b>Oops!</b> <br /> <br />We were unable to process your subscription for the <b><i>' . $conference_description .
                '</i></b>. <br /> Please try again or contact our support team.', 449);
  }

  public function render($request)
  {
    $url = $request->user()?->getDashboardRoute() ? route($request->user()->getDashboardRoute()) : self::$redirect_url;

    return $request->header('X-Inertia') ? back()->withFlash(['error' => $this->getMessage()]) : redirect()->to($url)->withFlash(['error' => $this->getMessage()]);
  }
}
