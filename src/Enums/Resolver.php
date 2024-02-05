<?php

namespace Aybarsm\Laravel\QuickBooks\Enums;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksErrorInterface;
use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksResolverInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

enum Resolver: string implements QuickBooksResolverInterface
{
    case Error = 'error';
    case GetTokensCache = 'get_tokens_cache';
    case SetTokensCache = 'set_tokens_cache';
    case ProfileId = 'profile_id';
    case Profile = 'profile';
    case AuthLogin = 'auth_login';
    case AuthCallback = 'auth_callback';
    case AuthLogout = 'auth_logout';

    case Request = 'request';
    case Response = 'response';
    case Webhook = 'webhook';

    public function getDefaults(): array
    {
        return [
            self::Error->value => fn (array $parameters): \Exception => throw App::make(QuickBooksErrorInterface::class, $parameters),
            self::GetTokensCache->value => fn (): array => transform(Cache::get('quickbooks_tokens'), fn ($cache) => json_decode(decrypt($cache), true), fn () => []),
            self::SetTokensCache->value => fn (array $values): bool => Cache::set('quickbooks_tokens', encrypt(json_encode($values, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK))),
            self::ProfileId->value => fn (array $config): string => md5("{$config['clientId']}|{$config['clientSecret']}|{$config['scope']}"),
            self::Profile->value => fn (array $config): QuickBooksProfileInterface => App::make(QuickBooksProfileInterface::class, $config),
            self::AuthLogin->value => \Aybarsm\Laravel\QuickBooks\Services\Resolvers\AuthLogin::class,
            self::AuthCallback->value => \Aybarsm\Laravel\QuickBooks\Services\Resolvers\AuthCallback::class,
            self::AuthLogout->value => \Aybarsm\Laravel\QuickBooks\Services\Resolvers\AuthLogout::class,
            self::Request->value => \Aybarsm\Laravel\QuickBooks\Services\Resolvers\Request::class,
            self::Response->value => \Aybarsm\Laravel\QuickBooks\Services\Resolvers\Response::class,
            self::Webhook->value => \Aybarsm\Laravel\QuickBooks\Services\Resolvers\Webhook::class,
        ];
    }

    public function resolve(array $parameters = []): mixed
    {
        $defaults = $this->getDefaults();
        $resolver = config("quickbooks.resolvers.{$this->value}") ?? $defaults[$this->value];

        return App::call($resolver, $parameters);
    }
}
