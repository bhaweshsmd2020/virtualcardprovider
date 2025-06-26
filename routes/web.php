<?php

use App\Http\Controllers\Web as WEB;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;

Route::patch('set-locale/{locale?}', [LocaleController::class, 'update'])->name('set-locale');

Route::get('/', [WEB\WebPageController::class, 'home'])->name('home');
Route::get('/about-us', [WEB\WebPageController::class, 'about']);
Route::get('/white-label', [WEB\WebPageController::class, 'whitelabel']);
Route::get('/pricing', [WEB\WebPageController::class, 'pricing']);
Route::get('/faqs', [WEB\WebPageController::class, 'faqs']);
Route::get('/callback', [WEB\WebPageController::class, 'callback']);
Route::resource('/contact-us', WEB\ContactController::class)->only('index', 'store');

Route::resource('/team', WEB\TeamController::class)->only(['index', 'show']);
Route::get('/integrations', [WEB\WebPageController::class, 'integrations']);
Route::resource('/services', WEB\ServiceController::class)->only(['index', 'show']);
Route::get('/service-categories/{slug}', [WEB\ServiceController::class, 'categoryShow'])->name('service-categories.show');
Route::resource('/projects', WEB\ProjectController::class)->only(['index', 'show']);
Route::post('/jobs/{job}/application', [WEB\JobController::class, 'application'])->name('jobs.application');
Route::resource('/jobs', WEB\JobController::class)->only(['index', 'show']);
Route::resource('/integrations', WEB\IntegrationController::class)->only(['index', 'show']);

Route::resource('/blogs', WEB\BlogController::class)->only(['index', 'show']);
Route::get('blogs/category/{slug}', [WEB\BlogController::class, 'category'])->name('blogs.category');
Route::get('blogs/tag/{slug}', [WEB\BlogController::class, 'tag'])->name('blogs.tag');
Route::post('/newsletter', [WEB\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

//**======================== Installler Route Group ====================**//
Route::resource('install',    App\Http\Controllers\Installer\InstallerController::class);
Route::post('install/verify', [App\Http\Controllers\Installer\InstallerController::class,'verify'])->name('install.verify');
Route::post('install/migrate', [App\Http\Controllers\Installer\InstallerController::class,'migrate'])->name('install.migrate');
//**======================== Installler Route End Group ====================**//

Route::get('/{slug}', [WEB\WebPageController::class, 'page']);