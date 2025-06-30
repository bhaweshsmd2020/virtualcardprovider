<?php

use App\Http\Controllers\Api\CreditCardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RampController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['throttle:api'], 'as' => 'api.',], function () {

    Route::get('get-states-by-country/{country_id}', function ($country_id) {
        return \App\Models\State::where('country_id', $country_id)->get();
    })->name('get-states-by-country');

    Route::get('get-cities-by-state/{state_id}', function ($state_id) {
        return \App\Models\City::where('state_id', $state_id)->get();
    })->name('get-cities-by-state');

    Route::get('credit-card-total-spend/{uuid}', [CreditCardController::class, 'credit_card_current_spend'])
        ->name('credit_card_current_spend')->middleware('auth');

    Route::post('/stripe/ephemeral-key', [CreditCardController::class, 'createEphemeralKey'])
        ->name('credit_card_ephemeral_key')->middleware('auth');
});

Route::post('create-token', [RampController::class, 'createToken']);
Route::post('create-new-token', [RampController::class, 'createNewToken']);
Route::post('revoke-token', [RampController::class, 'revokeToken']);
Route::post('get-token', [RampController::class, 'getToken']);

Route::post('users-list', [RampController::class, 'usersList']);
Route::post('user-details', [RampController::class, 'userDetails']);
Route::post('user-create', [RampController::class, 'userCreate']);
Route::post('user-invite-status', [RampController::class, 'userInviteStatus']);
Route::post('user-update', [RampController::class, 'userUpdate']);
Route::post('user-deactivate', [RampController::class, 'userDeactivate']);
Route::post('user-reactivate', [RampController::class, 'userReactivate']);

Route::post('cards-list', [RampController::class, 'cardsList']);
Route::post('card-details', [RampController::class, 'cardDetails']);
Route::post('card-create', [RampController::class, 'cardCreate']);
Route::post('physical-card-create', [RampController::class, 'physicalCardCreate']);
Route::post('card-create-status', [RampController::class, 'cardCreateStatus']);
Route::post('card-update', [RampController::class, 'cardUpdate']);
Route::post('card-block', [RampController::class, 'cardBlock']);
Route::post('card-unblock', [RampController::class, 'cardUnblock']);
Route::post('card-terminate', [RampController::class, 'cardTerminate']);

Route::post('all-transactions', [RampController::class, 'allTransactions']);
Route::post('card-transactions', [RampController::class, 'cardTransactions']);
Route::post('user-transactions', [RampController::class, 'userTransactions']);
Route::post('transaction-details', [RampController::class, 'transactionDetails']);

Route::post('departments-list', [RampController::class, 'departmentsList']);
Route::post('statements-list', [RampController::class, 'statementsList']);
Route::post('statements-list-new', [RampController::class, 'statementsListNew']);
