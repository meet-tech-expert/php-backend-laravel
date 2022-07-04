<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AdminEmailContactUs extends Mailable
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('お問い合わせが届きました。')
                ->markdown('email-template.adminContactUs', [
                    'content' => (object) $this->data,
                ]);
    }
}
