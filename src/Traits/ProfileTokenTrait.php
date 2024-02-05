<?php

namespace Aybarsm\Laravel\QuickBooks\Traits;

use Aybarsm\Laravel\QuickBooks\Contracts\ProfileStatusInterface;
use Aybarsm\Laravel\QuickBooks\Enums\ProfileStatus;
use Illuminate\Support\Arr;

trait ProfileTokenTrait
{
    public function getTokens(): array
    {
        return $this->token;
    }

    public function getToken(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->token, $key, $default);
    }

    protected function setTokens(array $tokens): static
    {
        $this->token = $tokens;

        return $this;
    }

    public function setToken(string $key, mixed $value): static
    {
        if (blank($key)) {
            return $this;
        }

        Arr::set($this->token, $key, $value);

        return $this;
    }

    protected function buildTokenConfig(array $cache): array
    {
        $new = [
            'authorisationBasic' => base64_encode("{$this->getClientId()}:{$this->getClientSecret()}"),
            'refreshThreshold' => 60,
            'renewalReminders' => [10800, 21600, 32400],
            'autoRefresh' => true,
        ];

        $config = config("quickbooks.profiles.{$this->getName()}.token", []);

        return array_merge($new, $cache, $config);
    }

    protected function setStatus(ProfileStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ProfileStatusInterface
    {
        $this->updateStatus();

        return $this->status;
    }

    public function updateStatus(): static
    {
        $status = match (true) {
            $this->getToken('pendingCode') && $this->getToken('pendingRealmId') => ProfileStatus::WaitingAccessCodeExchange,
            $this->getAccessTokenExpiresAt() && ($this->getAccessTokenExpiresAt() - time()) < $this->getRefreshThreshold() => ProfileStatus::NeedsRefresh,
            $this->getRefreshTokenExpiresAt() && $this->getRefreshTokenExpiresAt() < time() => ProfileStatus::NeedsRenewal,
            $this->getAccessToken() && $this->getAccessTokenExpiresAt() && ($this->getAccessTokenExpiresAt() - time()) > $this->getRefreshThreshold() => ProfileStatus::Ready,
            default => ProfileStatus::New,
        };

        return $this->setStatus($status);
    }

    public function getAuthorisationBasic(): string
    {
        return $this->getToken('authorisationBasic');
    }

    public function getState(): string
    {
        return $this->getToken('state');
    }

    public function setState(string $state): static
    {
        $this->setToken('state', $state);

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->getToken('code');
    }

    public function setCode(string $code): static
    {
        $this->setToken('code', $code);

        return $this;
    }

    public function getRealmId(): null|int|string
    {
        return $this->getToken('realmId');
    }

    public function setRealmId(int|string $realmId): static
    {
        $this->setToken('realmId', $realmId);

        return $this;
    }

    public function getTokenType(): ?string
    {
        return $this->getToken('tokenType');
    }

    public function setTokenType(string $tokenType): static
    {
        $this->setToken('tokenType', $tokenType);

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->getToken('accessToken');
    }

    public function setAccessToken(string $accessToken): static
    {
        $this->setToken('accessToken', $accessToken);

        return $this;
    }

    public function setAccessTokenExpiresIn(int|string $expiresIn): static
    {
        $this->setToken('accessTokenExpiresIn', $expiresIn);
        $this->setToken('accessTokenExpiresAt', time() + $expiresIn);

        return $this;
    }

    public function getAccessTokenExpiresAt(): null|int|string
    {
        return $this->getToken('accessTokenExpiresAt');
    }

    public function getRefreshToken(): ?string
    {
        return $this->getToken('refreshToken');
    }

    public function setRefreshToken(string $refreshToken): static
    {
        $this->setToken('refreshToken', $refreshToken);

        return $this;
    }

    public function getWebhookVerifierToken(): ?string
    {
        return $this->getToken('webhookVerifierToken');
    }

    public function SetWebhookVerifierToken(string $verifierToken): static
    {
        $this->setToken('webhookVerifierToken', $verifierToken);

        return $this;
    }

    public function getRefreshTokenExpiresAt(): null|int|string
    {
        return $this->getToken('refreshTokenExpiresAt');
    }

    public function setRefreshTokenExpiresIn(int|string $expiresIn): static
    {
        $this->setToken('refreshTokenExpiresIn', $expiresIn);
        $this->setToken('refreshTokenExpiresAt', time() + $expiresIn);

        return $this;
    }

    public function getRefreshThreshold(): int
    {
        return $this->getToken('refreshThreshold');
    }

    public function getRenewalReminders(): array
    {
        return $this->getToken('renewalReminders', []);
    }
}
