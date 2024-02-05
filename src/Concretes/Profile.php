<?php

namespace Aybarsm\Laravel\QuickBooks\Concretes;

use Aybarsm\Laravel\QuickBooks\Contracts\ProfileStatusInterface;
use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Enums\Resolver;
use Aybarsm\Laravel\QuickBooks\Traits\ProfileTokenTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;

class Profile implements QuickBooksProfileInterface
{
    use Conditionable, Macroable, ProfileTokenTrait;

    protected ProfileStatusInterface $status;

    protected string $id;

    protected string $baseUrl;

    protected string $redirectUrl;

    private array $token = [];

    public function __construct(
        protected string $name,
        protected string $clientId,
        protected string $clientSecret,
        protected string $scope,
        string $baseUrl,
        ?string $redirectUrl,
        ?string $redirectRouteName,
        array $macros = [],
        array $tokenCache = [],
    ) {
        $this->setBaseUrl(Str::isUrl($baseUrl, ['http', 'https']) ? $baseUrl : config("quickbooks.oauth.baseUrl.{$baseUrl}"));
        $this->setRedirectUrl(Str::isUrl($baseUrl, ['http', 'https']) ? $redirectUrl : route($redirectRouteName));

        $this->updateId();

        $this->unless(blank($macros), fn () => Arr::mapWithKeys($macros, fn ($closure, $name) => static::macro($name, $closure)));
        $this->setTokens($this->buildTokenConfig($tokenCache));
    }

    public function getConfig(): array
    {
        return [
            'name' => $this->getName(),
            'clientId' => $this->getClientId(),
            'clientSecret' => $this->getClientSecret(),
            'scope' => $this->getScope(),
            'baseUrl' => $this->getBaseUrl(),
            'redirectUrl' => $this->getRedirectUrl(),
        ];
    }

    protected function updateId(): static
    {
        $this->id = Resolver::ProfileId->resolve([
            'config' => $this->getConfig(),
        ]);

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setClientId(string $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function setClientSecret(string $clientSecret): static
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function setScope(string $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function setRedirectUrl(string $redirectUrl): static
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }
}
