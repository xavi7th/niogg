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
  ) {
  }

  public static function fromRequest(Request $request): self
  {
    return new self(
        $request->input('name'),
        $request->input('email'),
        $request->input('phone'),
        $request->input('message'),
    );
  }

  public function toArray(): array
  {
    return [
      'name' => $this->name,
      'email' => $this->email,
      'phone' => $this->phone,
      'message' => $this->message,
    ];
  }

  public function toObject(): object
  {
    return (object) $this->toArray();
  }
}
