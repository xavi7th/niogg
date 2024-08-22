<?php

namespace Modules\PublicPage\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Modules\PublicPage\DTOs\ContactFormMessageDTO;

class NewContactFormMessage extends Mailable implements ShouldBeUnique, ShouldQueue
{
  use Queueable, SerializesModels;

  public function __construct(private readonly ContactFormMessageDTO $message)
  {
  }

  public function envelope(): Envelope
  {
    return new Envelope(
        from: new Address(config('app.email'), config('app.name')),
        replyTo: [
          new Address($this->message->email, $this->message->name),
        ],
        subject: 'New Contact Form Message',
    );
  }

  public function content(): Content
  {
    return new Content(
        view: 'publicpage::mails.new-contact-form-message',
        with: [
          'msg' => $this->message,
        ],
    );
  }
}
