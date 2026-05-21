<?php

namespace Modules\PublicPage\Tests\Unit\DTOs;

use stdClass;
use Illuminate\Http\Request;
use Modules\PublicPage\Tests\TestCase;
use Modules\PublicPage\DTOs\ContactFormMessageDTO;

class ContactFormMessageDTOTest extends TestCase
{
  public function test_from_request_creates_dto(): void
  {
    $request = Request::create('/contact', 'POST', [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'phone' => '+1234567890',
      'message' => 'Test message',
    ]);

    $dto = ContactFormMessageDTO::fromRequest($request);

    $this->assertEquals('John Doe', $dto->name);
    $this->assertEquals('john@example.com', $dto->email);
    $this->assertEquals('+1234567890', $dto->phone);
    $this->assertEquals('Test message', $dto->message);
  }

  public function test_dto_has_all_required_fields(): void
  {
    $dto = new ContactFormMessageDTO(
        name: 'Jane',
        email: 'jane@example.com',
        phone: '+0987654321',
        message: 'Hello',
    );

    $this->assertNotNull($dto->name);
    $this->assertNotNull($dto->email);
    $this->assertNotNull($dto->phone);
    $this->assertNotNull($dto->message);
  }

  public function test_to_array_returns_all_fields(): void
  {
    $dto = new ContactFormMessageDTO(
        name: 'Test',
        email: 'test@example.com',
        phone: '123',
        message: 'Hi',
    );

    $array = $dto->toArray();

    $this->assertEquals('Test', $array['name']);
    $this->assertEquals('test@example.com', $array['email']);
    $this->assertEquals('123', $array['phone']);
    $this->assertEquals('Hi', $array['message']);
  }

  public function test_to_object_returns_stdclass(): void
  {
    $dto = new ContactFormMessageDTO(
        name: 'Test',
        email: 'test@example.com',
        phone: '123',
        message: 'Hi',
    );

    $object = $dto->toObject();

    $this->assertInstanceOf(stdClass::class, $object);
    $this->assertEquals('Test', $object->name);
  }
}
