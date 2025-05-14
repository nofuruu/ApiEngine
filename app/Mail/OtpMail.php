<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use SerializesModels;

    public $otp;
    public $name;

    /**
     * Create a new message instance.
     *
     * @param $otp
     */
    public function __construct($otp, $name)
    {
        $this->otp = $otp;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->subject('Your OTP Code')
            ->view('email.otp') // Pastikan Anda membuat tampilan email OTP
            ->with([
                'otp' => $this->otp,
                'name' => $this->name,
            ]);
    }
}
