<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    public function sendTestEmail()
    {
        Mail::raw('This is a test email', function($message){
            $message->to('naufal.rpl25@hyperdata.biz')
            ->subject('Test Email');
        });

        return response()->json(['status' => 'Email sent Successfully']);
    }
}
