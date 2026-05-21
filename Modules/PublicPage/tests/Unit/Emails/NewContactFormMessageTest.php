<?php

namespace Modules\PublicPage\Tests\Unit\Emails;

use Modules\PublicPage\Tests\TestCase;
use Modules\PublicPage\DTOs\ContactFormMessageDTO;
use Modules\PublicPage\Emails\NewContactFormMessage;

class NewContactFormMessageTest extends TestCase
{
  public function test_mailable_has_correct_content(): void
  {
    $dto = new ContactFormMessageDTO(
        name: 'John',
        email: 'john@example.com',
        phone: '123',
        message: 'Test message',
    );

    $mail = new NewContactFormMessage($dto);
    $content = $mail->content();

    $this->assertNotNull($content->view);
    $this->assertStringContainsString('new-contact-form-message', $content->view);
    $this->assertEquals('john@example.com', $content->with['msg']->email);
  }

  public function test_mailable_builds_correctly(): void
  {
    config(['app.email' => 'app@example.com', 'app.name' => 'Test App']);

    $dto = new ContactFormMessageDTO(
        name: 'John',
        email: 'john@example.com',
        phone: '123',
        message: 'Hello',
    );

    $mail = new NewContactFormMessage($dto);
    $rendered = $mail->render();

    $this->assertStringContainsString('John', $rendered);
    $this->assertStringContainsString('john@example.com', $rendered);
    $this->assertStringContainsString('Hello', $rendered);
  }
}
