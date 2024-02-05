<?php

namespace Aybarsm\Laravel\QuickBooks\Facades;

use Illuminate\Support\Facades\Facade;

class QuickBooks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'quickbooks';
    }
}
