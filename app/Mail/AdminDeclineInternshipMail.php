<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminDeclineInternshipMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $application;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('学生がインターンシップを辞退しました。')
                ->markdown('email-template.admin-decline-internship')
                ->with(
                    [
                        'internalInternshipId' => $this->application->internshipPost->internal_internship_id,
                        'internshipPostTitle' => $this->application->internshipPost->title,
                        'studentFamilyName' => $this->application->student->family_name,
                        'studentFirstName' => $this->application->student->first_name,
                    ]
                );
    }
}
