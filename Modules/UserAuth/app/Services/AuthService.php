<?php

namespace Modules\UserAuth\Services;

use Illuminate\Auth\Events\Registered;
use Modules\Conference\Models\ConferenceRegistrant;
use Modules\Conference\DTOs\ConferenceRegistrantDTO;

class AuthService
{
  public static function createConferenceRegistrant(array $params, bool $send_email_verification = FALSE): ConferenceRegistrant
  {
    $registrant = ConferenceRegistrant::updateOrCreate(['email' => $params['email']], (new ConferenceRegistrantDTO(...$params))->toArray());

    $registrant->registration_processed_at = NULL;

    if ($send_email_verification) {
      $registrant->save();
      // event(new Registered($registrant));

      return $registrant;
    }

    $registrant->email_verified_at = now();
    $registrant->save();

    // $registrant->notify(new StudentAccountCreated($params['password']));

    return $registrant;
  }
}
