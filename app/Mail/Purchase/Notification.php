<?php

namespace App\Mail\Purchase;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class Notification extends Mailable
{
   use Queueable, SerializesModels;

   protected $data;

   /**
    * Create a new message instance.
    *
    * @param array $data
    */
   public function __construct(array $data)
   {
       $this->data = $data;
   }

   /**
    * Get the message envelope.
    */
   public function envelope()
   {
       return new Envelope(
           subject: $this->data['subject'],
           from: new Address(config('mail.from.address'), 'MATIX ONE SYSTEM'),
           to: [new Address($this->data['to_email'])]
       );
   }

   /**
    * Get the message content definition.
    */
   public function content()
   {
       return new Content(
            view: 'emails.purchase.notification',
            with: [
                'id' => $this->data['id']
            ]
       );
   }
}