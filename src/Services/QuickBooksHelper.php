<?php

namespace Aybarsm\Laravel\QuickBooks\Services;

use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksHelperInterface;
use Aybarsm\Laravel\QuickBooks\Enums\Request as RequestType;
use Aybarsm\Laravel\QuickBooks\Enums\Response as ResponseType;
use Illuminate\Support\Str;

class QuickBooksHelper implements QuickBooksHelperInterface
{
    public static function getExecMethod(RequestType|ResponseType $type): string
    {
        return Str::of($type->value)->studly()->start('exec')->value();
    }

    public static function isValueTrue(mixed $value): bool
    {
        $value = is_string($value) ? strtolower($value) : $value;

        return in_array($value, [true, 'true', 1, '1', 'y', 'yes', 'on'], true);
    }

    public static function isValueFalse(mixed $value): bool
    {
        $value = is_string($value) ? strtolower($value) : $value;

        return in_array($value, [false, 'false', 0, '0', 'n', 'no', 'off', null, ''], true);
    }
}
