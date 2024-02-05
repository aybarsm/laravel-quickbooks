<?php

use Aybarsm\Laravel\QuickBooks\Enums\Resolver;

return [
    'oauth' => [
        'accessTokenUrl' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
        'authUrl' => 'https://appcenter.intuit.com/connect/oauth2',
        'baseUrl' => [
            'development' => 'https://sandbox-quickbooks.api.intuit.com/v3',
            'production' => 'https://quickbooks.api.intuit.com/v3',
        ],
    ],
    'profiles' => [
        'default' => [
            'clientId' => env('QUICKBOOKS_DEFAULT_CLIENT_ID'),
            'clientSecret' => env('QUICKBOOKS_DEFAULT_CLIENT_SECRET'),
            'scope' => env('QUICKBOOKS_DEFAULT_CLIENT_SCOPE', 'com.intuit.quickbooks.accounting'),
            'redirectUrl' => env('QUICKBOOKS_DEFAULT_CLIENT_REDIRECT_URL'),
            'redirectRouteName' => env('QUICKBOOKS_DEFAULT_CLIENT_REDIRECT_ROUTE_NAME', 'api.quickbooks.oauth.callback'),
            'baseUrl' => 'development',
            'macros' => [],
            'token' => [
                'webhookVerifierToken' => env('QUICKBOOKS_DEFAULT_WEBHOOK_VERIFIER_TOKEN'),
                'refreshThreshold' => 60,
            ],
        ],
    ],
    'concretes' => [
        'manager' => \Aybarsm\Laravel\QuickBooks\Concretes\Manager::class,
        'profile' => \Aybarsm\Laravel\QuickBooks\Concretes\Profile::class,
        'error' => \Aybarsm\Laravel\QuickBooks\Concretes\Exception::class,
    ],
    'resolvers' => [
        //        Resolver::ProfileId->value => fn (array $profile): string => md5("{$profile['clientId']}|{$profile['clientSecret']}|{$profile['scope']}"),
        //        Resolver::AuthLogin->value => \Aybarsm\Laravel\QuickBooks\Services\Resolvers\AuthLogin::class,
    ],
    'registerRoutes' => true,
    'models' => [

    ],
];
