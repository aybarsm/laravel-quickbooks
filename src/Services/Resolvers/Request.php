<?php

namespace Aybarsm\Laravel\QuickBooks\Services\Resolvers;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Enums\Request as RequestType;
use Aybarsm\Laravel\QuickBooks\Enums\Resolver;
use Aybarsm\Laravel\QuickBooks\Enums\Response as ResponseType;
use Aybarsm\Laravel\QuickBooks\Services\QuickBooksHelper;
use Aybarsm\Laravel\QuickBooks\Traits\ResolverHelperTrait;
use Illuminate\Http\Client\PendingRequest;

class Request
{
    use ResolverHelperTrait;

    protected string $method;

    protected string $url;

    protected array $clientOptions = [];

    protected function sendRequest(): void
    {
        try {
            $this->httpResponse = $this->httpRequest->send($this->method, $this->url, $this->clientOptions);
        } catch (\Exception $requestException) {
            $this->previousException = $requestException;
            Resolver::Error->resolve($this->getErrorParameters());

            return;
        }

        Resolver::Response->resolve([
            'profile' => $this->profile,
            'type' => $this->responseType,
            'response' => $this->httpResponse,
        ]);
    }

    protected function execGrantRefreshToken(): void
    {
        $this->method = 'POST';
        $this->url = config('quickbooks.oauth.accessTokenUrl');
        $this->errorMessage = 'Failed to grant refresh code.';
        $this->responseType = ResponseType::GrantRefreshToken;

        $this->httpRequest->acceptJson()->withToken($this->profile->getAuthorisationBasic(), 'Basic')
            ->withBody([
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->profile->getRefreshToken(),
            ])->asForm();

        $this->sendRequest();
    }

    protected function execGrantAuthorizationCode(): void
    {
        $this->method = 'POST';
        $this->url = config('quickbooks.oauth.accessTokenUrl');
        $this->errorMessage = 'Failed to grant authorization code.';
        $this->responseType = ResponseType::GrantAuthorizationCode;

        $this->httpRequest->acceptJson()->withToken($this->profile->getAuthorisationBasic(), 'Basic')
            ->withBody([
                'grant_type' => 'authorization_code',
                'code' => $this->profile->getToken('pendingCode'),
                'redirect_uri' => $this->profile->getRedirectUrl(),
            ])->asForm();

        $this->sendRequest();
    }

    public function __invoke(QuickBooksProfileInterface $profile, RequestType $type)
    {
        $this->requestType = $type;
        $this->profile = $profile;
        $this->httpRequest = new PendingRequest();
        $method = QuickBooksHelper::getExecMethod($type);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

    }
}
