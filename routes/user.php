<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User as USER;
use App\Http\Controllers\User\TwoFactorAuthenticationController;

Route::prefix('user')->as('user.')->middleware('auth','user')->group(function () {

   Route::get('last-login', [USER\DashboardController::class, 'getLastLogin']);

   Route::middleware(['user_profile_completed', '2fa', 'saas'])->group(function () {

      Route::get('dashboard', [USER\DashboardController::class, 'index'])->name('dashboard');
      Route::get('income-per-month', [USER\DashboardController::class, 'incomePerMonth'])->name('income.per.month');

      // account
      // Route::get('wallet', [USER\AccountController::class, 'index'])->name('wallet');
      Route::get('wallet', [USER\AccountController::class, 'account'])->name('wallet');
      Route::get('account', [USER\AccountController::class, 'account'])->name('account');
      Route::put('change-password', [USER\UserPanelController::class, 'updatePassword'])->name('update-password');
      Route::get('account-settings', [USER\UserPanelController::class, 'accountSetting'])->name('account-settings');
      Route::get('profile', [USER\UserPanelController::class, 'accountSetting'])->name('account-settings');
      Route::put('account-settings', [USER\UserPanelController::class, 'accountSettingUpdate'])->name('account-settings.update');
      Route::get('change-password', [USER\UserPanelController::class, 'changePassword'])->name('change-password');

      // kyc verification
      Route::withoutMiddleware('kyc_verified')->group(function () {
         Route::get('kyc/{kyc}/resubmit', [User\KYCVerificationController::class, 'resubmit'])->name('kyc.resubmit');
         Route::post('kyc/{kyc}/resubmit', [User\KYCVerificationController::class, 'resubmitUpdate']);
         Route::get('kyc/tips/{type}', [User\KYCVerificationController::class, 'tips'])->name('kyc.tips');
         Route::get('kyc/types/{type}', [User\KYCVerificationController::class, 'types'])->name('kyc.types');
         Route::get('kyc', [User\KYCVerificationController::class, 'index'])->name('kyc.index');
         Route::get('kyc/create/{id}', [User\KYCVerificationController::class, 'create'])->name('kyc.create');
         Route::post('kyc', [User\KYCVerificationController::class, 'store'])->name('kyc.store');
         Route::get('kyc/{kyc}', [User\KYCVerificationController::class, 'show'])->name('kyc.show');
         Route::get('kyc/{kyc}/edit', [User\KYCVerificationController::class, 'edit'])->name('kyc.edit');
         Route::put('kyc/{kyc}', [User\KYCVerificationController::class, 'update'])->name('kyc.update');
         Route::delete('kyc/{kyc}', [User\KYCVerificationController::class, 'destroy'])->name('kyc.destroy');
      });

      // two factor auth
      Route::get('account/two-factor-auth', [USER\TwoFactorAuthenticationController::class, 'store'])->name('account.two-factor-authentication.confirm.show');
     

      Route::resource('supports', USER\SupportController::class);

      // notifications
      Route::get('notifications', [USER\UserPanelController::class, 'userNotifications'])->name('notifications');
      Route::post('notifications/{notification}', [USER\UserPanelController::class, 'userNotificationsRead'])->name('notifications.read');
      Route::get('notifications/clear', [USER\UserPanelController::class, 'userNotificationsClear'])->name('notifications.clear');

      // subscription
      Route::get('subscription', [USER\SubscriptionController::class, 'index'])->name('subscription.index');
      Route::get('subscription/payment/{id}', [USER\SubscriptionController::class, 'payment'])->name('subscription.payment');
      Route::post('subscription/subscribe', [USER\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
      Route::get('subscription/plan/{status}', [USER\SubscriptionController::class, 'status']);

      // cards
      Route::resource('card-orders',   USER\CardOrderController::class);
      Route::get('card-issue', [USER\CardController::class, 'create'])->name('card-issue.create');
      Route::post('card-pay', [USER\CardPayController::class, 'store'])->name('card.pay');
      Route::get('card-pay/{status}', [USER\CardPayController::class, 'update'])->name('card.pay.status.update');

      // financial
      Route::get('deposits', [USER\DepositController::class, 'index'])->name('deposits.index');
      Route::post('deposits', [USER\DepositController::class, 'store'])->name('deposits.pay');
      Route::get('deposits/{id}', [USER\DepositController::class, 'show'])->name('deposits.show');
      Route::post('deposits-wallet', [USER\DepositController::class, 'wallet'])->name('deposits.wallet');
      Route::get('deposits/payment/{status}', [USER\DepositController::class, 'update'])->name('deposits.status.update');
      // balance and wallet
      Route::get('top-up', [USER\TopUpController::class, 'index'])->name('top-up.index');
      Route::get('top-up/create', [USER\TopUpController::class, 'create'])->name('top-up.create');
      Route::get('top-up/{id}', [USER\TopUpController::class, 'show'])->name('top-up.show');
      Route::get('top-up-wallet', [USER\TopUpController::class, 'createWallet'])->name('top-up.create-wallet');
      Route::post('top-up', [USER\TopUpController::class, 'store'])->name('top-up.store');
      Route::get('top-up/payment/{status}', [USER\TopUpController::class, 'update'])->name('top-up.status.update');


      Route::post('/wallet', [USER\WalletController::class, 'store'])->name('wallet.store');
      Route::get('exchange', [USER\ExchangeController::class, 'index'])->name('exchange.index');
      Route::post('exchange', [USER\ExchangeController::class, 'store'])->name('exchange.store');
      Route::get('exchange/logs', [USER\ExchangeController::class, 'logs'])->name('exchange.logs');
      Route::get('withdraw', [USER\WithdrawController::class, 'index'])->name('withdraw.index');
      Route::get('withdraw/create', [USER\WithdrawController::class, 'create'])->name('withdraw.create');
      Route::post('withdraw', [USER\WithdrawController::class, 'store'])->name('withdraw.store');
      Route::get('transfer', [USER\TransferController::class, 'index'])->name('transfer.index');
      Route::post('transfer', [USER\TransferController::class, 'store'])->name('transfer.store');
      Route::get('transfer/logs', [USER\TransferController::class, 'logs'])->name('transfer.logs');
      // payouts
      Route::get('/payout/setup/{method_id}',    [USER\PayoutController::class, 'setup'])->name('payout-setup');
      Route::post('/payout/sentotp/{method_id}', [USER\PayoutController::class, 'sentOtp'])->name('payout.otp');
      Route::get('/payout/confirmation',         [USER\PayoutController::class, 'confirmation'])->name('payout.confirmation');
      Route::resource('payout',                   USER\PayoutController::class);
      Route::post('/payout/make-request',        [USER\PayoutController::class, 'makeRequest'])->name('payout.otp.verify');
      Route::get('/payouts/logs',                [USER\PayoutController::class, 'logs'])->name('payout.logs');
      Route::get('/payout-track/{invoice_no}',   [USER\PayoutController::class, 'invoice'])->name('payout.details');

      // cards
      Route::resource('credit-cards',   USER\CreditCardController::class)->only(['index', 'show']);
      Route::get('credit-card/transactions',   [USER\CreditCardController::class, 'transactions'])
         ->name('credit-card.transactions');
      Route::get('credit-card/topups',   [USER\CreditCardController::class, 'topups'])
         ->name('credit-card.topups');
      Route::get('credit-card/topupdetails/{id}',   [USER\CreditCardController::class, 'topupdetails'])
         ->name('credit-card.topupdetails');
      Route::get('credit-card/balance/{id}',   [USER\CreditCardController::class, 'balance'])
         ->name('credit-card.balance');
      Route::get('credit-card/authorizations',   [USER\CreditCardController::class, 'authorizations'])
         ->name('credit-card.authorizations');
      Route::patch('credit-card/{id}/status-update/{status}',   [USER\CreditCardController::class, 'updateStatus'])
         ->name('credit-card.update-status');
      Route::get('credit-card/{id}/full-details', [USER\CreditCardController::class, 'getFullCardDetails'])->name('credit-card.full-details');
      Route::get('credit-card/activate-cards', [USER\CreditCardController::class, 'activateCards'])->name('credit-card.activate-cards');
      Route::post('credit-card/increase-limit', [USER\CreditCardController::class, 'increaseLimit'])->name('credit-card.increase-limit');
      Route::post('credit-card/update', [USER\CreditCardController::class, 'updatecard'])->name('credit-card.update');
      Route::post('card/topup',   [USER\CreditCardController::class, 'cardTopup'])
         ->name('card.topup');
   });

   Route::get('2fa/login', [TwoFactorAuthenticationController::class, 'index']);
   Route::post('google2fa/authenticate', [TwoFactorAuthenticationController::class, 'store']);
   Route::post('google2fa/disable', [TwoFactorAuthenticationController::class, 'destroy']);
});

// qr payments
Route::get('user/top-ups/{uuid}/payment', [USER\QrTopUpController::class, 'index'])->name('qr-top-up.index');
Route::get('user/top-ups/{invoice}/invoice', [USER\QrTopUpController::class, 'invoice'])->name('qr-top-up.invoice');
Route::post('user/top-ups/payment', [USER\QrTopUpController::class, 'store'])->name('qr-top-up.store');
Route::get('user/top-ups/{status}', [USER\QrTopUpController::class, 'update'])->name('qr-top-up.status.update');
