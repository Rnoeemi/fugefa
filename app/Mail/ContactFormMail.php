<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, phone: ?string, message: string}  $payload
     */
    public function __construct(
        public array $payload,
        public string $siteName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kapcsolati üzenet – '.$this->siteName,
            replyTo: [
                new Address($this->payload['email'], $this->payload['name']),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form',
            with: [
                'name' => $this->payload['name'],
                'email' => $this->payload['email'],
                'phone' => $this->payload['phone'] ?? null,
                'body' => $this->payload['message'],
                'siteName' => $this->siteName,
            ],
        );
    }
}
