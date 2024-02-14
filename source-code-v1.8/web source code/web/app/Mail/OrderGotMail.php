<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderGotMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public int $orderId;
    public mixed $message;

    public function __construct($orderId, $message)
    {
        $this->orderId = $orderId;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject("You got a new order")->markdown('emails.orderGot');
    }
}
