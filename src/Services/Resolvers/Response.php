<?php

namespace Aybarsm\Laravel\QuickBooks\Services\Resolvers;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Enums\Resolver;
use Aybarsm\Laravel\QuickBooks\Enums\Response as ResponseType;
use Aybarsm\Laravel\QuickBooks\Services\QuickBooksHelper;
use Aybarsm\Laravel\QuickBooks\Traits\ResolverHelperTrait;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Arr;

class Response
{
    use ResolverHelperTrait;

    protected function settleTokens(): bool
    {
        $map = [
            'token_type' => 'setTokenType',
            'access_token' => 'setAccessToken',
            'expires_in' => 'setAccessTokenExpiresIn',
            'refresh_token' => 'setRefreshToken',
            'x_refresh_token_expires_in' => 'setRefreshTokenExpiresIn',
        ];

        $vars = $this->httpResponse->json();

        if (! Arr::has($vars, array_keys($map))) {
            Resolver::Error->resolve($this->getErrorParameters());

            return false;
        }

        foreach ($map as $varKey => $method) {
            $this->profile->{$method}($vars[$varKey]);
        }

        return true;
    }

    protected function execGrantRefreshToken(): void
    {
        $this->errorMessage = 'Invalid response for GrantRefreshToken from QuickBooks.';
        $this->settleTokens();
    }

    protected function execGrantAuthorizationCode(): void
    {
        $this->errorMessage = 'Invalid response for GrantAuthorizationCode from QuickBooks.';

        if (! $this->settleTokens()) {
            return;
        }

        $this->profile->setState($this->profile->getToken('pendingState'));
        $this->profile->setToken('pendingState', null);
        $this->profile->setCode($this->profile->getToken('pendingCode'));
        $this->profile->setToken('pendingCode', null);
        $this->profile->setRealmId($this->profile->getToken('pendingRealmId'));
        $this->profile->setToken('pendingRealmId', null);
    }

    public function __invoke(QuickBooksProfileInterface $profile, ResponseType $type, HttpResponse $response)
    {
        $this->responseType = $type;
        $this->profile = $profile;
        $this->httpResponse = $response;

        $method = QuickBooksHelper::getExecMethod($type);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

    }
}
