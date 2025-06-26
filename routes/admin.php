<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin as ADMIN;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'admin']], function () {

    Route::get('dashboard', ADMIN\DashboardController::class)->name('dashboard');

    // saas
    Route::resource('plan', ADMIN\PlanController::class);
    Route::resource('order', ADMIN\OrderController::class);

    Route::resource('role', ADMIN\RoleController::class);
    Route::resource('admin', ADMIN\AdminController::class);
    Route::resource('gateways', ADMIN\GatewayController::class);
    Route::get('cron-job', ADMIN\CronjobController::class)
        ->name('cron-job.index');
    Route::resource('page', ADMIN\PageController::class);
    Route::resource('blog', ADMIN\BlogController::class);
    Route::resource('category', ADMIN\CategoryController::class);

    Route::resource('faq-category', ADMIN\FaqCategoryController::class);
    Route::resource('team', ADMIN\TeamController::class);
    Route::resource('tag', ADMIN\TagController::class);

    Route::resource('language', ADMIN\LanguageController::class);
    Route::resource('menu', ADMIN\MenuController::class);
    Route::patch('/menu-data/{id}', [ADMIN\MenuController::class, 'updateData'])->name('menu.data.update');


    Route::get('page-settings', [ADMIN\SettingsController::class, 'index'])->name('page-settings.index');
    Route::post('page-settings/{id}', [ADMIN\SettingsController::class, 'update'])->name('page-settings.update');

    Route::resource('seo', ADMIN\SeoController::class);
    Route::post('support-update/{id}',[ADMIN\SupportController::class,'update'])->name('support-update');
    Route::resource('support', ADMIN\SupportController::class);
    Route::resource('notification', ADMIN\NotifyController::class);
    Route::post('notifications/{notification}', [ADMIN\AdminController::class, 'adminNotificationsRead'])->name('notifications.read');
    Route::get('notifications/clear', [ADMIN\AdminController::class, 'adminNotificationsClear'])->name('notifications.clear');

    Route::resource('testimonials', ADMIN\TestimonialsController::class);
    Route::resource('faq', ADMIN\FaqController::class);
    Route::resource('developer-settings', ADMIN\DeveloperSettingsController::class);
    Route::resource('partner', ADMIN\PartnerController::class);
    Route::resource('update', ADMIN\UpdateController::class);

    Route::post('/language/addkey', [ADMIN\LanguageController::class, 'addKey']);
    Route::post('/menu-update/{id}', [ADMIN\MenuController::class, 'storeDate'])->name('menu.content.update');
    Route::get('profile', [ADMIN\ProfileController::class, 'settings'])->name('profile.setting');
    Route::put('profile/update/{type}', [ADMIN\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/option-update/{key}', [ADMIN\OptionController::class, 'update'])->name('option.update');

    Route::resource('users', ADMIN\UserController::class)
        ->only(['index', 'show', 'edit', 'update', 'destroy']);

    Route::put('users/assign-plan/{user}', [ADMIN\UserController::class, 'assignPlan'])->name('users.assign.plan');

    Route::resource('virtual-currency', ADMIN\VirtualCurrencyController::class)
        ->names('virtual-currency')
        ->except(['show']);

    // KYC
    Route::post('/kyc-method/mass-destroy', [ADMIN\KycMethodController::class, 'massDestroy'])->name('kyc-methods.mass-destroy');
    Route::resource('kyc-methods', ADMIN\KycMethodController::class);
    Route::delete('kyc-requests/destroy/mass', [ADMIN\KYCRequestController::class, 'destroyMass'])->name('kyc-requests.destroy.mass');
    Route::resource('kyc-requests', ADMIN\KYCRequestController::class);

    // Payouts
    Route::resource('payout-methods', ADMIN\PayoutMethodController::class);
    Route::post('payout-methods/delete-all', [ADMIN\PayoutMethodController::class, 'deleteAll'])->name('payout-methods.delete');
    Route::post('payouts/delete-all', [ADMIN\PayoutController::class, 'deleteAll'])->name('payouts.delete');
    Route::get('payouts/approved', [ADMIN\PayoutController::class, 'approved'])->name('payouts.approved');
    Route::get('payouts/reject', [ADMIN\PayoutController::class, 'reject'])->name('payouts.reject');
    Route::resource('payouts', ADMIN\PayoutController::class);
    // cards
    Route::resource('cards', ADMIN\CardController::class);
    Route::resource('card-orders', ADMIN\CardOrderController::class);
    Route::patch('card-orders/status/{id}', [ADMIN\CardOrderController::class, 'updateStatus'])
        ->name('card-orders.status.update');
    Route::patch('card-orders/terms/{id}', [ADMIN\CardOrderController::class, 'updateTerms'])
        ->name('card-orders.terms.update');

    // card emails
    Route::get('card-emails', [ADMIN\CardemailController::class, 'index'])->name('card.emails.index');
    Route::post('card-emails/store', [ADMIN\CardemailController::class, 'store'])->name('card.emails.store');
    Route::post('card-emails/update/{id}', [ADMIN\CardemailController::class, 'update'])->name('card.emails.update');
    Route::delete('card-emails/delete/{id}', [ADMIN\CardemailController::class, 'destroy'])->name('card.emails.delete');

    // card limits requests
    Route::get('card-limit-requests', [ADMIN\LimitrequestController::class, 'index'])->name('card.limit-requests.index');
    Route::post('card-limit-requests/update/{id}', [ADMIN\LimitrequestController::class, 'update'])->name('card.limit-requests.update');

    Route::resource('deposits', ADMIN\DepositController::class);
    Route::resource('top-up', ADMIN\TopUpController::class);
    // wallets
    Route::get('transfers', [ADMIN\TransferController::class, 'index'])
        ->name('transfers.index');
    Route::get('exchanges', [ADMIN\ExchangeTransactionController::class, 'index'])
        ->name('exchanges.index');
    Route::get('withdraw', [ADMIN\WithdrawController::class, 'index'])->name('withdraw.index');

    Route::resource('card-category', ADMIN\CardCategoryController::class);
    Route::resource('credit-cards', ADMIN\CreditCardController::class)->only(['index', 'show']);
    Route::patch('credit-card/{id}/status-update/{status}', [ADMIN\CreditCardController::class, 'updateStatus'])
        ->name('credit-card.update-status');
    Route::patch('credit-card/{id}/spending-update', [ADMIN\CreditCardController::class, 'updateSpending'])
        ->name('credit-card.update-spending');
    Route::get('credit-card/transactions', [ADMIN\CreditCardController::class, 'transactions'])
        ->name('credit-card.transactions');
    Route::get('credit-card/topups', [ADMIN\CreditCardController::class, 'topups'])
        ->name('credit-card.topups');
    Route::get('credit-card/topupdetails/{id}', [ADMIN\CreditCardController::class, 'topupdetails'])
        ->name('credit-card.topupdetails');
    Route::get('credit-card/balance/{id}',   [ADMIN\CreditCardController::class, 'balance'])
         ->name('credit-card.balance');
    Route::get('credit-card/authorizations', [ADMIN\CreditCardController::class, 'authorizations'])
        ->name('credit-card.authorizations');
    Route::patch('credit-card/{id}/terminate', [ADMIN\CreditCardController::class, 'terminate'])
        ->name('credit-card.terminate');
    Route::post('credit-card/update', [ADMIN\CreditCardController::class, 'updatecard'])
        ->name('credit-card.update');

    // locations
    Route::apiResource('countries', ADMIN\Locations\CountryController::class);
    Route::apiResource('states', ADMIN\Locations\StateController::class);
    Route::apiResource('cities', ADMIN\Locations\CityController::class);
    Route::resource('service-categories', ADMIN\ServiceCategoryController::class);
    Route::resource('services', ADMIN\ServiceController::class);
    Route::resource('project-categories', ADMIN\ProjectCategoryController::class);
    Route::resource('projects', ADMIN\ProjectController::class);
    Route::get('clear-cache', [ADMIN\SystemController::class, 'clearCache'])->name('clear-cache');

    Route::resource('email-templates', ADMIN\EmailTemplateController::class);
});
