<?php

namespace Aybarsm\Laravel\QuickBooks\Services\Resolvers;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Traits\ResolverHelperTrait;

class Webhook
{
    use ResolverHelperTrait;

    public function __invoke(QuickBooksProfileInterface $profile)
    {
        $this->profile = $profile;

    }
}
