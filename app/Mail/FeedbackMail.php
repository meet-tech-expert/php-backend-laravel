<?php

namespace App\Mail;

use App\Models\Feedbacks;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $feedback;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Feedbacks $feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('企業からフィードバックが届きました！')
        ->markdown('email-template.feedback')->with(
            [
                'studentName' => $this->feedback->student->family_name.' '.$this->feedback->student->first_name,
                'url' => env('USER_SITE_URL').'redirect/?page=MyPageParent&tab=feedback',
                'company_name' => $this->feedback->companies->name
            ]
        );
    }
}
