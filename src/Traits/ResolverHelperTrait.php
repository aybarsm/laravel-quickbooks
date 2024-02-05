<?php

namespace Aybarsm\Laravel\QuickBooks\Traits;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Enums\Request as RequestType;
use Aybarsm\Laravel\QuickBooks\Enums\Response as ResponseType;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Http\Request;

trait ResolverHelperTrait
{
    protected QuickBooksProfileInterface $profile;

    protected Request $request;

    protected RequestType $requestType;

    protected ResponseType $responseType;

    protected PendingRequest $httpRequest;

    protected HttpResponse $httpResponse;

    protected string $errorMessage;

    protected \Exception $previousException;

    protected function getErrorParameters(): array
    {
        $params = [
            'message' => $this->errorMessage,
            'profile' => $this->profile,
        ];

        if (isset($this->previousException)) {
            $params['previous'] = $this->previousException;
        }

        foreach (['pendingRequest', 'requestType', 'httpResponse', 'responseType'] as $property) {
            if (isset($this->{$property})) {
                $params[$property] = $this->$this->{$property};
            }
        }

        return $params;
    }
}
