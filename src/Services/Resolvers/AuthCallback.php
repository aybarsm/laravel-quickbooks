<?php

namespace Aybarsm\Laravel\QuickBooks\Services\Resolvers;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Enums\Request as RequestType;
use Aybarsm\Laravel\QuickBooks\Enums\Resolver;
use Aybarsm\Laravel\QuickBooks\Traits\ResolverHelperTrait;
use Illuminate\Http\Request;

class AuthCallback
{
    use ResolverHelperTrait;

    public function __invoke(QuickBooksProfileInterface $profile, ?Request $request = null): \Illuminate\Http\JsonResponse
    {
        $this->request = $request ?? Request::instance();
        $state = $this->request->get('state');
        $code = $this->request->get('code');
        $realmId = $this->request->get('realmId');

        if (! $code || ! $realmId || $state !== $profile->getToken('pendingState')) {

            Resolver::Error->resolve($this->getErrorParameters());

            return response()->json(['success' => false]);
        }

        $profile->setToken('pendingCode', $code);
        $profile->setToken('pendingRealmId', $realmId);

        Resolver::Request->resolve([
            'profile' => $profile,
            'type' => RequestType::GrantAuthorizationCode,
        ]);

        return response()->json(['success' => true]);
    }
}
