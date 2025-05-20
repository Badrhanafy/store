<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgetPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The password reset token
     *
     * @var string
     */
    public $token;

    /**
     * The user's email address
     *
     * @var string
     */
    public $email;

    /**
     * Create a new message instance.
     *
     * @param  string  $token
     * @param  string  $email
     * @return void
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Password Reset Request',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'resetUrl' => $this->getResetUrl(),
                'email' => $this->email,
                'token' => $this->token,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

    /**
     * Get the password reset URL.
     *
     * @return string
     */
    protected function getResetUrl()
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email,
        ], false));
    }
}