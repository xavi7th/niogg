<?php

namespace Modules\Conference\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\UserAuth\Services\AuthService;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Conference\Models\ConferenceRegistrant;

/**
 * @method User user()
 */
class LaunchConferenceRegistrationRequest extends FormRequest
{
  public function authorize(): bool
  {
    return TRUE;
  }

  public function rules(): array
  {
    return [
      'full_name' => ['required', 'max:50'],
      'phone' => ['required', 'max:25'],
      'email' => ['required', 'email'],
      'amount' => ['required', 'numeric'],
      'conference_tag' => ['required', 'max:20'],
      'is_manual_reg' => ['required', 'bool'],
    ];
  }

  public function registrant(): ConferenceRegistrant
  {
    if ($this->existing_user) {
      return ConferenceRegistrant::whereEmail($this->email)->sole();
    }

    return DB::transaction(fn (): ConferenceRegistrant => AuthService::createConferenceRegistrant(collect($this->validated())->except(['amount', 'is_manual_reg'])->toArray()));
  }
}
