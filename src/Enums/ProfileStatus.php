<?php

namespace Aybarsm\Laravel\QuickBooks\Enums;

enum ProfileStatus: string
{
    case New = 'new';
    case WaitingCallback = 'waiting_callback';
    case WaitingAccessCodeExchange = 'waiting_access_code_exchange';
    case NeedsRefresh = 'needs_refresh';
    case NeedsRenewal = 'needs_renewal';
    case Ready = 'ready';
}
