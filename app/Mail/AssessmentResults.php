<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssessmentResults extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {        
        $mail = \Config::get('mail.from.address');

        return $this->from($mail)
        ->subject('Assessment Results')
        ->view('mails.assessment-results')
        ->with(
            [
                'testVarOne' => '1',
                'testVarTwo' => '2',
            ]
        );
    }
}
