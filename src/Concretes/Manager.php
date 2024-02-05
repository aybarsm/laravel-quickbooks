<?php

namespace Aybarsm\Laravel\QuickBooks\Concretes;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksManagerInterface;
use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Enums\Resolver;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;

class Manager implements QuickBooksManagerInterface
{
    use Conditionable, Macroable;

    protected static Collection $profiles;

    protected static array $tokensCache;

    public function __construct(
    ) {
        $this->setTokensCache();
        static::$profiles = new Collection();
        $this->registerConfigProfiles();
        App::terminating(fn () => $this->updateTokensCache());
    }

    public function registerConfigProfiles(bool $replace = false): static
    {
        foreach (config('quickbooks.profiles', []) as $profileName => $profileConfig) {
            $this->newProfile($profileName, $profileConfig, $replace);
        }

        return $this;
    }

    public function getProfileTokenCache(string $profileId, mixed $default = null): mixed
    {
        return Arr::get(static::$tokensCache, $profileId, $default);
    }

    public function updateProfileTokenCache(string $profileId, array $tokens): static
    {
        $profileCache = array_merge($this->getProfileTokenCache($profileId, []), $tokens);

        Arr::set(static::$tokensCache, $profileId, $profileCache);

        return $this;
    }

    public function updateTokensCache(): static
    {
        foreach ($this->getProfiles() as $profile) {
            $this->updateProfileTokenCache($profile->getId(), $profile->getTokens());
        }

        Resolver::SetTokensCache->resolve([
            'values' => static::$tokensCache,
        ]);

        return $this;
    }

    protected function setTokensCache(): void
    {
        static::$tokensCache = Resolver::GetTokensCache->resolve();
    }

    public function hasProfile(string $profileName = 'default'): bool
    {
        return $this->getProfiles()->contains(fn ($profile) => $profile->getName(), $profileName);
    }

    public function getProfiles(): Collection
    {
        return static::$profiles;
    }

    public function profile(string $profileName = 'default'): ?QuickBooksProfileInterface
    {
        return $this->getProfile($profileName);
    }

    public function getProfile(string $profileName = 'default'): ?QuickBooksProfileInterface
    {
        return $this->getProfiles()->firstWhere(fn ($profile) => $profile->getName(), $profileName);
    }

    public function newProfile(string $name, array $config, bool $replace = false): static
    {
        $config['name'] = $name;

        if (($exists = $this->hasProfile($name)) && ! $replace) {
            Resolver::Error->resolve([
                'message' => "The profile [{$config['name']}] already exists.",
            ]);
        }

        $profileId = Resolver::ProfileId->resolve([
            'config' => $config,
        ]);
        $config['tokenCache'] = $this->getProfileTokenCache($profileId, []);
        $profile = Resolver::Profile->resolve([
            'config' => $config,
        ]);

        if ($exists) {
            $profileKey = $this->getProfiles()->search(fn ($profile) => $profile->getName() === $name);
            static::$profiles->replace([$profileKey => $profile]);
        } else {
            static::$profiles->push($profile);
        }

        return $this;
    }
}
