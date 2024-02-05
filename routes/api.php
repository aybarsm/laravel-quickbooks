<?php

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksManagerInterface;
use Aybarsm\Laravel\QuickBooks\Enums\Resolver as QuickBooksResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('webhook')->post('webhook', function (Request $request, QuickBooksManagerInterface $quickbooks) {
    $profileName = $request->get('profile', 'default');

    return QuickBooksResolver::Webhook->resolve([
        'profile' => $quickbooks->profile($profileName),
    ]);
});

Route::name('oauth.')->prefix('oauth')->group(function () {
    Route::name('login')->get('login', function (Request $request, QuickBooksManagerInterface $quickbooks) {
        $profileName = $request->get('profile', 'default');

        return QuickBooksResolver::AuthLogin->resolve([
            'profile' => $quickbooks->profile($profileName),
        ]);
    });

    Route::name('callback')->get('callback', function (Request $request, QuickBooksManagerInterface $quickbooks) {
        $profileName = $request->get('profile', 'default');

        return QuickBooksResolver::AuthCallback->resolve([
            'profile' => $quickbooks->profile($profileName),
            'request' => $request,
        ]);
    });
});
