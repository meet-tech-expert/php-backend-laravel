<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApplyInternship extends Mailable
{
    use Queueable, SerializesModels;

    public $bccMail = [];
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
        array_push($this->bccMail, $this->application->company->office_email1);
        if ($this->application->company->office_email2) {
            array_push($this->bccMail, $this->application->company->office_email2);
        }
        if ($this->application->company->office_email3) {
            array_push($this->bccMail, $this->application->company->office_email3);
        }
        return $this->subject('【対応依頼】求人広告に応募がありました。')
            ->cc($this->application->student->email_valid)
            ->bcc($this->bccMail)
            ->markdown('email-template.apply-internship')
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
                    'internshipPostLink' => env('USER_SITE_URL') .
                        'internship-detail/' .
                        $this->application->internshipPost->id .
                        '/' .
                        rawurldecode($this->application->internshipPost->title)
                ]
            );
    }
}
