<?php

namespace Modules\Conference\DTOs;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Traits\Conditionable;

class ConferenceRegistrantDTO
{
  use Conditionable;

  public function __construct(
      public readonly string $full_name,
      public readonly string $email,
      public readonly string $phone,
      public readonly string $conference_tag,
      public readonly ?string $password = NULL,
  ) {
  }

  public static function fromRequest(Request $request): self
  {
    return new self(
        $request->input('full_name'),
        $request->input('email'),
        $request->input('phone'),
        $request->input('password'),
        $request->input('conference_tag'),
    );
  }

  public function toArray(): array
  {
    return [
      'full_name' => $this->full_name,
      'email' => $this->email,
      'phone' => $this->phone,
      'password' => $this->password ?? Hash::make($this->phone),
      'conference_tag' => $this->conference_tag,
      'registration_id' => Str::limit(Str::upper($this->conference_tag), 3, '-') . Str::limit(md5($this->email), 10, ''),
    ];
  }

  public function toObject(): object
  {
    return (object) $this->toArray();
  }
}
