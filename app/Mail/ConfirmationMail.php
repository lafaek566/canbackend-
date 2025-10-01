<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The confirm object instance.
     *
     * @var Confirm
     */
    public $confirm;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($confirm)
    {
        $this->confirm = $confirm;
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
        */
        $mail = \Config::get('mail.from.address');

        return $this->from($mail)
            ->view('mails.confirmation')
            ->with(
                [
                    'testVarOne' => '1',
                    'testVarTwo' => '2',
                ]
            );
        // ->attach(public_path('/images') . '/logo.png', [
        //     // 'as' => 'logo.png',
        //     // 'mime' => 'image/png',
        // ]);
    }
}
