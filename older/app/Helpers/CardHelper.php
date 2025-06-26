<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Models\AccessToken;
use App\Models\CardEmail;
use App\Models\User;
use App\Models\CardlimitRequest;
use App\Models\CreditCard;
use App\Models\Setting;
use Carbon\Carbon;

class CardHelper
{
    public function __construct()
    {
        $this->baseUrl = Setting::where('name', 'base_url')->first()->value;
        $this->clientId = Setting::where('name', 'client_id')->first()->value;
        $this->clientSecret = Setting::where('name', 'client_secret')->first()->value;
        $this->redirectUrl = Setting::where('name', 'redirect_url')->first()->value;
        $this->cardInterval = Setting::where('name', 'interval')->first()->value;
    }

    public function createToken()
    {
        $token = AccessToken::orderBy('id', 'desc')->whereNotNull('refresh_token')->first();
        $grant_type = 'refresh_token';
        $refresh_token = $token->refresh_token;
        
        $url = $this->baseUrl.'token';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $payloads = "grant_type=".$grant_type."&refresh_token=".$refresh_token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloads);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $response = json_decode($result, true);
        curl_close($ch);
        
        AccessToken::where('status', '1')->update(['status' => '0']);
        AccessToken::create([
            'access_token' => $response['access_token'],
            'token_type' => $response['token_type'],
            'expires_in' => $response['expires_in'],
            'refresh_token' => null,
            'scope' => $response['scope']
        ]);
        
        return $response['access_token'];        
    }

    public function postFunction($url, $headers, $payloads)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloads));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $response = json_decode($result, true);
        curl_close($ch);
        return $response;
    }

    public function getFunction($url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $response = json_decode($result, true);
        curl_close($ch);
        return $response;
    }

    public function patchFunction($url, $headers, $payloads)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloads));
        $result = curl_exec($ch);
        $response = json_decode($result, true);
        curl_close($ch);
        return $response;
    }

    public function createUser()
    {
        $accessToken = self::createToken();
        
        $check_email = CardEmail::where('status', '0')->first();
        if(empty($check_email)){
            return 'invalid';
        }
        $email = $check_email->email;
        $first_name = auth()->user()->first_name;
        $last_name = auth()->user()->last_name;
        
        $url = $this->baseUrl.'users/deferred';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'email' => $email,
            'first_name' => $first_name,
            'idempotency_key' => Str::uuid(),
            'last_name' => $last_name,
            'role' => 'BUSINESS_USER',
            'department_id' => 'b58bc60c-5d31-441b-be7b-1bfadb29362b',
        ];
        
        $response = $this->postFunction($url, $headers, $payloads);

        CardEmail::where('email', $email)->update([
            'user_id' => auth()->user()->id,
            'invite_id' => $response['id'],
            'status' => '1'
        ]);
    } 

    public function userStatus($userId)
    {
        $accessToken = self::createToken();
        $check_user = CardEmail::where('user_id', $userId)->first();
        $inviteId = $check_user->invite_id;
        
        $url = $this->baseUrl.'users/deferred/status/'.$inviteId;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $response = $this->getFunction($url, $headers);
        if(!empty($response['data']['error'])){
            return $response['data']['error'];
        }

        CardEmail::where('user_id', $userId)->update([
            'card_user_id' => $response['data']['user_id'],
        ]);
        
        $userUrl = $this->baseUrl.'users/'.$response['data']['user_id'];      

        $userResponse = $this->getFunction($userUrl, $headers);
        if(!empty($userResponse['data']['error'])){
            return $userResponse['data']['error'];
        }

        User::where('id', $userId)->update([
            'card_user_id' => $response['data']['user_id'],
            'card_user_status' => $userResponse['status'],
        ]);

        if($userResponse['status'] == 'USER_ACTIVE'){
            return 1;
        }elseif($userResponse['status'] == 'INVITE_PENDING'){
            return 2;
        }elseif($userResponse['status'] == 'INVITE_EXPIRED'){
            return 3;
        }
    } 

    public function createCard($subtotal, $name_on_card)
    {
        $accessToken = self::createToken(); 
        $userId = auth()->user()->card_user_id;
        
        $url = $this->baseUrl.'cards/deferred/virtual';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'display_name' => $name_on_card,
            'idempotency_key' => Str::uuid(),
            'spending_restrictions' => [
                'amount' => $subtotal,
                'currency' => 'USD',
                'interval' => $this->cardInterval,
                'lock_date' => Carbon::now()->addYears(2)->toIso8601String(),
            ],
            'user_id' => $userId,
        ];
        
        $response = $this->postFunction($url, $headers, $payloads);
        $inviteId = $response['id']; 
        return $inviteId;        
    }  

    public function cardStatus($inviteId)
    {
        $accessToken = self::createToken();
                
        $inviteUrl = $this->baseUrl.'cards/deferred/status/'.$inviteId;  
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $inviteResponse = $this->getFunction($inviteUrl, $headers);
        if($inviteResponse['status'] == 'SUCCESS'){
            $cardId = $inviteResponse['data']['card_id'];
            $cardUrl = $this->baseUrl.'cards/'.$cardId;        
            $cardResponse = $this->getFunction($cardUrl, $headers); 
            return $cardResponse;         
        }else{
            return 1;
        }        
    }
    
    public function cardDetails($cardId)
    {
        $accessToken = self::createToken(); 

        $cardUrl = $this->baseUrl.'cards/'.$cardId; 
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $cardResponse = $this->getFunction($cardUrl, $headers);
        
        return $cardResponse;
    } 
    
    public function cardStatusUpdate($cardId, $status)
    {
        $accessToken = self::createToken(); 

        if($status == 'inactive'){
            $url = $this->baseUrl.'cards/'.$cardId.'/deferred/suspension';
        }else{
            $url = $this->baseUrl.'cards/'.$cardId.'/deferred/unsuspension';
        }      
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'idempotency_key' => Str::uuid()
        ];
        
        $response = $this->postFunction($url, $headers, $payloads);
    }

    public function cardTerminate($cardId)
    {
        $accessToken = self::createToken(); 

        $url = $this->baseUrl.'cards/'.$cardId.'/deferred/termination';     
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'idempotency_key' => Str::uuid()
        ];
        
        $response = $this->postFunction($url, $headers, $payloads);
    }

    public function cardTransactions($cardId)
    {
        $accessToken = self::createToken(); 

        $url = $this->baseUrl.'transactions?card_id='.$cardId.'&page_size=10';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $response = $this->getFunction($url, $headers); 
        return $response;
    }

    public function spandTransactions($cardId)
    {        
        $currentDate = Carbon::now('UTC');
        $firstDate = $currentDate->copy()->startOfMonth()->startOfDay();
        $lastDate = $currentDate->copy()->endOfMonth()->setTime(23, 59, 59);
        $firstDateFormatted = $firstDate->toISOString();
        $lastDateFormatted = $lastDate->toISOString();

        $accessToken = self::createToken();

        $url = $this->baseUrl.'transactions?card_id='.$cardId.'&from_date='.$firstDateFormatted.'&to_date='.$lastDateFormatted;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $response = $this->getFunction($url, $headers); 
        return $response;
    }

    public function allCardsTransactions($userId)
    {
        $accessToken = self::createToken(); 

        $url = $this->baseUrl.'transactions?user_id='.$userId;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $response = $this->getFunction($url, $headers);
        return $response;
    }

    public function allAdminCardsTransactions()
    {
        $accessToken = self::createToken(); 

        $url = $this->baseUrl.'transactions';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $response = $this->getFunction($url, $headers);
        return $response;
    }

    public function cardUpdate($cardId, $name_on_card)
    {
        $accessToken = self::createToken(); 
        
        $url = $this->baseUrl.'cards/'.$cardId;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'display_name' => $name_on_card
        ];
        
        $response = $this->patchFunction($url, $headers, $payloads);
    }

    public function cardUsesLimit($id)
    {
        $cardlimit = CardlimitRequest::findOrFail($id);
        $card = CreditCard::findOrFail($cardlimit->card_id);
        $cardId = $card->card;

        $accessToken = self::createToken(); 
        
        $url = $this->baseUrl.'cards/'.$cardId;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'spending_restrictions' => [
                'amount' => $cardlimit->subtotal,
                'transaction_amount_limit' => $cardlimit->subtotal
            ],
        ];
        
        $response = $this->patchFunction($url, $headers, $payloads);
    }

    public function cardTopup($card_id, $sub_total)
    {
        $card = CreditCard::findOrFail($card_id);
        $cardId = $card->card;

        $accessToken = self::createToken();      
        
        $url = $this->baseUrl.'cards/'.$cardId;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $cardResponse = $this->getFunction($url, $headers);
        
        $topup_amount = $cardResponse['spending_restrictions']['amount'] + $sub_total;
        
        $payloads = [
            'spending_restrictions' => [
                'amount' => $topup_amount
            ],
        ];
        
        $response = $this->patchFunction($url, $headers, $payloads);
    }
}
