<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AccessToken;

class RampController extends Controller
{
    public function __construct()
    {
        $this->baseUrl = 'https://api.ramp.com/developer/v1/';
        $this->clientId  = 'ramp_id_wXAiKetROrN1PoicmpkUhDBcbN5RKZBM4ILUIfPR';
        $this->clientSecret = 'ramp_sec_OoNgIiOw8l7Ap4BpL3kSWqOiyUIULydfFQeC55HOPuC7B9kp';
        $this->redirectUrl = 'https://cards.lubypay.com/callback';
        $this->cardLimit = '100';
        $this->cardCurrency = 'USD';
        $this->cardInterval = 'MONTHLY';
        $this->cardTransLimit = '100';
    }
    
    public function postFunction($url, $headers, $payloads)
    {
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
        return $response;
    }
    
    public function patchFunction($url, $headers, $payloads)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloads);
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
    
    public function createToken(Request $request)
    {
        $grant_type = 'authorization_code';
        $code = $request->code;
        
        $url = $this->baseUrl.'token';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $payloads = "grant_type=".$grant_type."&code=".$code."&redirect_uri=".$this->redirectUrl;
        
        $response = $this->postFunction($url, $headers, $payloads);
        if(empty($response['error_v2']['message'])){
            AccessToken::truncate();
            AccessToken::create([
                'access_token' => $response['access_token'],
                'token_type' => $response['token_type'],
                'expires_in' => $response['expires_in'],
                'refresh_token' => $response['refresh_token'],
                'scope' => $response['scope']
            ]);
            
            return response()->json([
                'status' => '200',
                'message' => "Token created successfully!",
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function createNewToken(Request $request)
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
        
        $response = $this->postFunction($url, $headers, $payloads);
        if(empty($response['error_v2']['message'])){
            AccessToken::where('status', '1')->update(['status' => '0']);
            AccessToken::create([
                'access_token' => $response['access_token'],
                'token_type' => $response['token_type'],
                'expires_in' => $response['expires_in'],
                'refresh_token' => null,
                'scope' => $response['scope']
            ]);
            
            return response()->json([
                'status' => '200',
                'message' => "New Token created successfully!",
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function revokeToken(Request $request)
    {
        $token = AccessToken::orderBy('id', 'desc')->whereNotNull('refresh_token')->first();
        
        $url = $this->baseUrl.'token/revoke';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $payloads = "token=".$token->refresh_token."&token_type_hint=refresh_token";
        
        $response = $this->postFunction($url, $headers, $payloads);
        if(empty($response['error_v2']['message'])){
            $access_token = AccessToken::where('status', '1')->orderBy('id', 'desc')->first();
            
            return response()->json([
                'status' => '200',
                'message' => $response,
                'data' => $access_token,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function getToken(Request $request)
    {
        $access_token = AccessToken::where('status', '1')->orderBy('id', 'desc')->first();
        if(!empty($access_token)){
            return response()->json([
                'status' => '200',
                'message' => 'Tokens fetched successfully.',
                'data' => $access_token,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => 'Tokens not found!',
                'data' => null,
            ]);
        }
    }
    
    public function usersList(Request $request)
    {
        $accessToken = $request->access_token;
        
        $url = $this->baseUrl.'users';
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Users list fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function userDetails(Request $request)
    {
        $accessToken = $request->access_token;
        $userId = $request->user_id;
        
        $url = $this->baseUrl.'users/'.$userId;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'User details fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function userCreate(Request $request)
    {
        $accessToken = $request->access_token;
        $email = $request->email;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        
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
        ];
        
        $response = $this->postFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'User creation invite sent successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function userInviteStatus(Request $request)
    {
        $accessToken = $request->access_token;
        $inviteId = $request->invite_id;
        
        $url = $this->baseUrl.'users/deferred/status/'.$inviteId;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'User invite status fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function userUpdate(Request $request)
    {
        $accessToken = $request->access_token;
        $user_id = $request->user_id;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        
        $url = $this->baseUrl.'users/'.$user_id;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'first_name' => $first_name,
            'last_name' => $last_name,
        ];
        
        $response = $this->patchFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'User updated successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function userDeactivate(Request $request)
    {
        $accessToken = $request->access_token;
        $user_id = $request->user_id;
        
        $url = $this->baseUrl.'users/'.$user_id.'/deactivate';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [];
        
        $response = $this->patchFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'User deactivated successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function userReactivate(Request $request)
    {
        $accessToken = $request->access_token;
        $user_id = $request->user_id;
        
        $url = $this->baseUrl.'users/'.$user_id.'/reactivate';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [];
        
        $response = $this->patchFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'User reactivated successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function cardsList(Request $request)
    {
        $accessToken = $request->access_token;
        
        $url = $this->baseUrl.'cards';
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Cards list fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function cardDetails(Request $request)
    {
        $accessToken = $request->access_token;
        $cardId = $request->card_id;
        
        $url = $this->baseUrl.'cards/'.$cardId;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Card details fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function cardCreate(Request $request)
    {
        $accessToken = $request->access_token;
        $name_on_card = $request->name_on_card;
        $user_id = $request->user_id;
        
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
                'amount' => $this->cardLimit,
                'currency' => $this->cardCurrency,
                'interval' => $this->cardInterval,
                'transaction_amount_limit' => $this->cardTransLimit,
            ],
            'user_id' => $user_id,
        ];
        
        $response = $this->postFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Card created successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function physicalCardCreate(Request $request)
    {
        $accessToken = $request->access_token;
        $name_on_card = $request->name_on_card;
        $user_id = $request->user_id;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $address = $request->address;
        $city = $request->city;
        $state = $request->state;
        $country = $request->country;
        $postal_code = $request->postal_code;
        
        $url = $this->baseUrl.'cards/deferred/physical';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'display_name' => $name_on_card,
            'fulfillment' => [
                'shipping' => [
                    'recipient_address' => [
                        'address1' => $address,
                        'city' => $city,
                        'country' => $country,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'postal_code' => $postal_code,
                        'state' => $state
                    ]
                ]
            ],
            'idempotency_key' => Str::uuid(),
            'spending_restrictions' => [
                'amount' => $this->cardLimit,
                'currency' => $this->cardCurrency,
                'interval' => $this->cardInterval,
                'transaction_amount_limit' => $this->cardTransLimit,
            ],
            'user_id' => $user_id
        ];
        
        $response = $this->postFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Physical card applied successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function cardCreateStatus(Request $request)
    {
        $accessToken = $request->access_token;
        $cardId = $request->card_id;
        
        $url = $this->baseUrl.'cards/deferred/status/'.$cardId;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Card create status fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function cardUpdate(Request $request)
    {
        $accessToken = $request->access_token;
        $card_id = $request->card_id;
        $name_on_card = $request->name_on_card;
        
        $url = $this->baseUrl.'cards/'.$card_id;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'display_name' => $name_on_card
        ];
        
        $response = $this->patchFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Card updated successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function cardBlock(Request $request)
    {
        $accessToken = $request->access_token;
        $card_id = $request->card_id;
        
        $url = $this->baseUrl.'cards/'.$card_id.'/deferred/suspension';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'idempotency_key' => Str::uuid()
        ];
        
        $response = $this->postFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Card blocked successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function cardUnblock(Request $request)
    {
        $accessToken = $request->access_token;
        $card_id = $request->card_id;
        
        $url = $this->baseUrl.'cards/'.$card_id.'/deferred/unsuspension';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'idempotency_key' => Str::uuid()
        ];
        
        $response = $this->postFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Card unblocked successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function cardTerminate(Request $request)
    {
        $accessToken = $request->access_token;
        $card_id = $request->card_id;
        
        $url = $this->baseUrl.'cards/'.$card_id.'/deferred/termination';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'idempotency_key' => Str::uuid()
        ];
        
        $response = $this->postFunction($url, $headers, json_encode($payloads));
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Card terminated successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function allTransactions(Request $request)
    {
        $accessToken = $request->access_token;
        
        $url = $this->baseUrl.'transactions';
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'All transactions fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function cardTransactions(Request $request)
    {
        $accessToken = $request->access_token;
        $card_id = $request->card_id;
        
        $url = $this->baseUrl.'transactions?card_id='.$card_id;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Card transactions fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function userTransactions(Request $request)
    {
        $accessToken = $request->access_token;
        $user_id = $request->user_id;
        
        $url = $this->baseUrl.'transactions?user_id='.$user_id;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'User transactions fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
    
    public function transactionDetails(Request $request)
    {
        $accessToken = $request->access_token;
        $trans_id = $request->trans_id;
        
        $url = $this->baseUrl.'transactions/'.$trans_id;
        
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Transaction details fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }

    public function departmentsList(Request $request)
    {
        $accessToken = $request->access_token;
        
        $url = $this->baseUrl.'departments';
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Departments list fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }

    public function statementsList(Request $request)
    {
        $accessToken = $request->access_token;
        
        $url = $this->baseUrl.'statements';
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $response = $this->getFunction($url, $headers);
        if(empty($response['error_v2']['message'])){
            return response()->json([
                'status' => '200',
                'message' => 'Statements list fetched successfully.',
                'data' => $response,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'message' => $response['error_v2']['message'],
                'data' => null,
            ]);
        }
    }
}
