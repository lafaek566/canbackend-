<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class inviteMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The invite object instance.
     *
     * @var Invite
     */
    public $invite;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invite)
    {
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /*
        return $this->view('view.name');
        return $this->from('planner@fixleplanner.com.au')
        */
        $mail=\Config::get('mail.from.address');

        return $this->from($mail)
            ->view('mails.invite')
            ->with(
                [
                    'testVarOne' => '1',
                    'testVarTwo' => '2',
                ])
                ->attach(public_path('/images').'/logo.png', [
                        // 'as' => 'logo.png',
                        // 'mime' => 'image/png',
                ]);
    }
}
