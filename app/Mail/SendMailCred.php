<?php

namespace App\Mail;

use App\InvitationEmail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailCred extends Mailable
{
    use Queueable, SerializesModels;

    private $invitation;

    /**
     * Create a new message instance.
     *
     * @param InvitationEmail $invitation
     */
    public function __construct($invitation)
    {
        $this->invitation = $invitation;
        $this->subject = $invitation->title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.cred', ['invitation' => $this->invitation]);
    }
}
