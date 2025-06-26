<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwilioService
{
    protected $config;

    public function __construct()
    {
        $this->config = [
            'accountSid' => config('twilio.account_sid'),
            'token' => config('twilio.auth_token'),
            'from' => config('twilio.from'),
        ];
    }

    public function sendMessage($to, $message)
    {
        return Http::withBasicAuth($this->config['accountSid'], $this->config['token'])
            ->asForm()
            ->post('https://api.twilio.com/2010-04-01/Accounts/' . $this->config['accountSid'] . '/Messages.json', [
                'To' => $to,
                'From' => $this->config['from'],
                'Body' => $message
            ]);
    }
}
