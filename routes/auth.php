<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerifyOtpController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\VerifyPhoneController;
use App\Http\Controllers\Auth\AccountSetupController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

Route::middleware('guest')->group(function () {
    // register
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // forgot password
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    // reset password
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::middleware('auth')->group(function () {

    // email verification
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');

    // phone verification
    Route::get('verify/phone', [VerifyPhoneController::class, 'index'])->name('phone.verification.index');
    Route::post('verify/phone', [VerifyPhoneController::class, 'store'])->middleware(['throttle:2,1'])->name('phone.verification.store');
    Route::get('verify/otp', [VerifyOtpController::class, 'index'])->name('otp.verification.index');
    // Route::post('verify/otp', [VerifyOtpController::class, 'store'])->middleware('throttle:6,1')->name('otp.verification.store');
    Route::post('/verify-firebase-otp', [VerifyOtpController::class, 'verifyFirebaseOtp']);

    // password update
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // setup location
    Route::get('/account/setup', [AccountSetupController::class, 'index'])->name('account.setup');
    Route::post('/account/setup', [AccountSetupController::class, 'store']);
});

Route::get('auth/{provider}', [SocialLoginController::class, 'redirectTo']);
Route::get('auth/callback/{provider}', [SocialLoginController::class, 'handleCallback']);


