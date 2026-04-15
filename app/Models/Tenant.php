<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class Tenant extends Model
{
    use HasUuids;

    protected $table = 'tenants';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'allowlist' => 'array',
        'provisioned_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];
}
