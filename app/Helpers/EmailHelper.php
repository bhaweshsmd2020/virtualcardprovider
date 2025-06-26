<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Models\AccessToken;
use App\Models\EmailConfig;
use App\Models\User;
use App\Models\CardlimitRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailHelper
{
    public function __construct()
    {       
    }

    public function sendEmail($email, $subject, $content)
    {   
        $data = [];
        $data = array(
            'to'      => $email,
            'subject' => $subject,
            'content' => $content,
        );

        $emailConfig = EmailConfig::where(['email_protocol' => 'smtp', 'status' => '1'])->first();

        if ($emailConfig) {
            $this->setupEmailConfig($emailConfig);

            Mail::send('mail.email', ['subject' => $data['subject'], 'content' => $data['content']], function ($message) use ($data, $emailConfig) {
                $message->to($data['to'])
                        ->subject($data['subject'])
                        ->from($emailConfig->from_address, $emailConfig->from_name)
                        ->cc('fibantech@gmail.com');
            });
        }
    }

    public function setupEmailConfig()
    {
        $emailConfig = EmailConfig::where(['email_protocol' => 'smtp', 'status' => '1'])->first();

        Config::set([
            'mail.driver'     => isset($emailConfig->email_protocol) ? $emailConfig->email_protocol : '',
            'mail.host'       => isset($emailConfig->smtp_host) ? $emailConfig->smtp_host : '',
            'mail.port'       => isset($emailConfig->smtp_port) ? $emailConfig->smtp_port : '',
            'mail.from'       => ['address' => isset($emailConfig->from_address) ? $emailConfig->from_address : '', 'name' => isset($emailConfig->from_name) ? $emailConfig->from_name : ''],
            'mail.encryption' => isset($emailConfig->email_encryption) ? $emailConfig->email_encryption : '',
            'mail.username'   => isset($emailConfig->smtp_username) ? $emailConfig->smtp_username : '',
            'mail.password'   => isset($emailConfig->smtp_password) ? $emailConfig->smtp_password : '',
        ]);
    }
}