<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KycMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $KYCRequest;

    public function __construct($KYCRequest)
    {
        $this->KYCRequest = $KYCRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kyc Approved - [' . env('APP_NAME') . ']',
        );
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        dd($this->KYCRequest);
        return $this->subject('Kyc Approved - [' . env('APP_NAME') . ']')
            ->markdown('mail.kycmail')
            ->with('link', $this->KYCRequest);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
