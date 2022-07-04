<?php

namespace App\Mail;

use App\Models\Applications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeclineInternshipMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Applications $application)
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
        return $this->subject('【対応依頼】学生がインターンシップを辞退しました。')
        ->markdown('email-template.decline-internship')
        ->with(
            [
                'companyName' => $this->application->company->name,
                'internshipPostTitle' => $this->application->internshipPost->title,
                'studentFamilyName' => $this->application->student->family_name,
                'studentFirstName' => $this->application->student->first_name,
                'studentFamilyNameFurigana' => $this->application->student->family_name_furigana,
                'studentFirstNameFurigana' => $this->application->student->first_name_furigana,
                'email' => $this->application->student->email_valid,
                'university' => $this->application->student->educationFacility->name,
                'year' => $this->application->student->graduate_year,
                'month' => $this->application->student->graduate_month,
                'selfIntroduction' => $this->application->student->self_introduction,
                'internshipPostLink' => env('USER_SITE_URL').
                                        'internship-detail/'.
                                        $this->application->internshipPost->id.
                                        '/'.
                                        rawurldecode($this->application->internshipPost->title)
            ]
        );
    }
}
