<?php

namespace Modules\PublicPage\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Traits\Conditionable;

class ContactFormMessageDTO
{
  use Conditionable;

  public function __construct(
      public readonly string $name,
      public readonly string $email,
      public readonly string $phone,
      public readonly string $message,
      public readonly string $how_did_you_hear_about_us,
  ) {
  }

  public static function fromRequest(Request $request): self
  {
    return new self(
        $request->input('name'),
        $request->input('email'),
        $request->input('phone'),
        $request->input('message'),
        $request->input('how_did_you_hear_about_us'),
    );
  }

  public function toArray(): array
  {
    return [
      'name' => $this->name,
      'email' => $this->email,
      'phone' => $this->phone,
      'message' => $this->message,
      'how_did_you_hear_about_us' => $this->how_did_you_hear_about_us,
    ];
  }

  public function toObject(): object
  {
    return (object) $this->toArray();
  }
}
