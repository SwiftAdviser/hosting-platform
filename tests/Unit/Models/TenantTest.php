<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class TenantTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = ':memory:';
        $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_DATABASE'] = ':memory:';

        parent::setUp();

        $migration = require __DIR__ . '/../../../database/migrations/2026_04_16_120000_create_tenants_table.php';
        if (Schema::hasTable('tenants')) {
            Schema::drop('tenants');
        }
        $migration->up();
    }

    public function test_tenant_can_be_created_with_required_fields(): void
    {
        $tenant = Tenant::create([
            'agent_name' => 'spawn-bot',
        ]);

        self::assertNotEmpty($tenant->id);
        self::assertIsString($tenant->id);

        $reloaded = Tenant::find($tenant->id);

        self::assertSame('provisioning', $reloaded->status);
        self::assertSame('coolify', $reloaded->provider);
    }

    public function test_tenant_allowlist_casts_to_array(): void
    {
        $tenant = Tenant::create([
            'agent_name' => 'spawn-bot',
            'allowlist' => ['user-1', 'user-2'],
        ]);

        $reloaded = Tenant::find($tenant->id);

        self::assertIsArray($reloaded->allowlist);
        self::assertSame(['user-1', 'user-2'], $reloaded->allowlist);
    }

    public function test_tenant_omits_secret_columns(): void
    {
        $tenant = new Tenant();
        $columns = $tenant->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($tenant->getTable());

        self::assertNotContains('telegram_bot_token', $columns);
        self::assertNotContains('llm_api_key', $columns);
    }

    public function test_tenant_can_transition_status(): void
    {
        $tenant = Tenant::create([
            'agent_name' => 'spawn-bot',
        ]);

        $tenant->status = 'running';
        $tenant->save();

        $reloaded = Tenant::find($tenant->id);

        self::assertSame('running', $reloaded->status);
    }

    public function test_provisioned_at_casts_to_datetime(): void
    {
        $tenant = Tenant::create([
            'agent_name' => 'spawn-bot',
            'provisioned_at' => '2026-04-16 12:00:00',
        ]);

        $reloaded = Tenant::find($tenant->id);

        self::assertInstanceOf(Carbon::class, $reloaded->provisioned_at);
    }
}
