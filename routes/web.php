<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PressController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// Root redirects to default locale (French, per client requirement)
Route::get('/', fn () => redirect('/'.SetLocale::DEFAULT));

Route::get('/switch-language/{locale}', LanguageController::class)->name('language.switch');

Route::prefix('{locale}')->middleware(SetLocale::class)->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');

    Route::get('/press', [PressController::class, 'index'])->name('press.index');
    Route::get('/press/{slug}', [PressController::class, 'show'])->name('press.show');

    Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

    // Catch-all: resolves any other page by its language-neutral slug.
    // Must stay LAST so it doesn't swallow the routes above.
    Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');
});
