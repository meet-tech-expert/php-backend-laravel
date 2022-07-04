<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class UserEmailContactUs extends Mailable
{
    protected $data;
    protected $senderName;

    public function __construct($data, $senderName)
    {
        $this->data = $data;
        $this->senderName = $senderName;
    }

    public function build()
    {
        return $this->subject('お問い合わせを受け付けました。')
                ->from(config('from.address'), $this->senderName)
                ->markdown('email-template.userContactUs', [
                    'content' => (object) $this->data,
                ]);
    }
}
