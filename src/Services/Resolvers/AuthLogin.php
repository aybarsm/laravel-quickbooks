<?php

namespace Aybarsm\Laravel\QuickBooks\Services\Resolvers;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Traits\ResolverHelperTrait;

class AuthLogin
{
    use ResolverHelperTrait;

    public function __invoke(QuickBooksProfileInterface $profile): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $pendingState = $profile->getId().'|'.time();
        $profile->setToken('pendingState', $pendingState);

        $params = [
            'client_id' => $profile->getClientId(),
            'response_type' => 'code',
            'scope' => $profile->getScope(),
            'redirect_uri' => $profile->getRedirectUrl(),
            'state' => $pendingState,
        ];

        $redirectUrl = config('quickbooks.oauth.authUrl').'?'.http_build_query($params);

        return redirect($redirectUrl);
    }
}
