<?php

namespace Aybarsm\Laravel\QuickBooks\Concretes;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksErrorInterface;
use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksManagerInterface;
use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;

class Exception extends \Exception implements QuickBooksErrorInterface
{
    protected ?QuickBooksManagerInterface $manager;

    protected ?QuickBooksProfileInterface $profile;

    protected ?PendingRequest $client;

    protected ?Request $request;

    public function __construct(
        $message,
        $code = 0,
        ?\Exception $previous = null,
        ...$args
    ) {
        parent::__construct($message, $code, $previous);
        foreach ($args as $arg) {
            if ($arg instanceof QuickBooksManagerInterface) {
                $this->manager = $arg;
            }
            if ($arg instanceof QuickBooksProfileInterface) {
                $this->profile = $arg;
            }
            if ($arg instanceof PendingRequest) {
                $this->client = $arg;
            }
            if ($arg instanceof Request) {
                $this->request = $arg;
            }
        }
    }

    public function getClient(): ?PendingRequest
    {
        return $this->client;
    }

    public function getManager(): ?QuickBooksManagerInterface
    {
        return $this->manager;
    }

    public function getProfile(): ?QuickBooksProfileInterface
    {
        return $this->profile;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }
}
