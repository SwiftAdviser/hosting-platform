<?php

declare(strict_types=1);

namespace App\Services\Runtime;

enum TenantStatus: string
{
    case Provisioning = 'provisioning';
    case Starting = 'starting';
    case Running = 'running';
    case Degraded = 'degraded';
    case Stopped = 'stopped';
    case Failed = 'failed';
}
