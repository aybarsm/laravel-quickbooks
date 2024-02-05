<?php

namespace Aybarsm\Laravel\QuickBooks\Enums;

enum Response: string
{
    case GrantAuthorizationCode = 'grant_authorization_code';
    case GrantRefreshToken = 'grant_refresh_token';
    case RevokeToken = 'revoke_token';
    case GetAccounting = 'get_accounting';
    case PostAccounting = 'post_accounting';
}
