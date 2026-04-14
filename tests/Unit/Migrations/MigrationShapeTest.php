<?php

declare(strict_types=1);

namespace Tests\Unit\Migrations;

use PHPUnit\Framework\TestCase;

final class MigrationShapeTest extends TestCase
{
    private const MIGRATIONS_DIR = __DIR__ . '/../../../database/migrations';

    private function loadMigration(string $filename): string
    {
        $path = self::MIGRATIONS_DIR . '/' . $filename;
        self::assertFileExists($path, "Migration file not found: {$filename}");

        $contents = file_get_contents($path);
        self::assertIsString($contents, "Failed to read migration file: {$filename}");

        return $contents;
    }

    public function test_users_migration_has_expected_shape(): void
    {
        $src = $this->loadMigration('2026_04_14_120000_create_users_table.php');

        self::assertStringContainsString('return new class extends Migration', $src);
        self::assertStringContainsString('use Illuminate\\Database\\Migrations\\Migration;', $src);
        self::assertStringContainsString('use Illuminate\\Database\\Schema\\Blueprint;', $src);
        self::assertStringContainsString('use Illuminate\\Support\\Facades\\Schema;', $src);

        self::assertStringContainsString("Schema::create('users', function (Blueprint \$table)", $src);
        self::assertStringContainsString("Schema::dropIfExists('users')", $src);

        self::assertStringContainsString('$table->id();', $src);
        self::assertStringContainsString("\$table->string('email')->unique();", $src);
        self::assertStringContainsString("\$table->string('name')->nullable();", $src);
        self::assertStringContainsString("\$table->string('google_id')->unique()->nullable();", $src);
        self::assertStringContainsString("\$table->timestamp('email_verified_at')->nullable();", $src);
        self::assertStringContainsString("\$table->string('remember_token', 100)->nullable();", $src);
        self::assertStringContainsString('$table->timestamps();', $src);
    }

    public function test_agents_migration_has_expected_shape(): void
    {
        $src = $this->loadMigration('2026_04_14_120001_create_agents_table.php');

        self::assertStringContainsString('return new class extends Migration', $src);
        self::assertStringContainsString('use Illuminate\\Database\\Migrations\\Migration;', $src);
        self::assertStringContainsString('use Illuminate\\Database\\Schema\\Blueprint;', $src);
        self::assertStringContainsString('use Illuminate\\Support\\Facades\\Schema;', $src);

        self::assertStringContainsString("Schema::create('agents', function (Blueprint \$table)", $src);
        self::assertStringContainsString("Schema::dropIfExists('agents')", $src);

        self::assertStringContainsString('$table->id();', $src);
        self::assertStringContainsString("\$table->foreignId('user_id')->constrained()->cascadeOnDelete();", $src);
        self::assertStringContainsString("\$table->string('name');", $src);
        self::assertStringContainsString("\$table->text('personality');", $src);
        self::assertStringContainsString("\$table->string('icon')->nullable();", $src);
        self::assertStringContainsString("\$table->string('status')->default('pending');", $src);
        self::assertStringContainsString("\$table->string('kiloclaw_id')->nullable();", $src);
        self::assertStringContainsString("\$table->string('wallet_address')->nullable();", $src);
        self::assertStringContainsString("\$table->text('bot_token_encrypted')->nullable();", $src);
        self::assertStringContainsString("\$table->text('allowlist')->nullable();", $src);
        self::assertStringContainsString('$table->timestamps();', $src);

        self::assertStringContainsString("\$table->index(['user_id', 'status']);", $src);
    }

    public function test_deploys_migration_has_expected_shape(): void
    {
        $src = $this->loadMigration('2026_04_14_120002_create_deploys_table.php');

        self::assertStringContainsString('return new class extends Migration', $src);
        self::assertStringContainsString('use Illuminate\\Database\\Migrations\\Migration;', $src);
        self::assertStringContainsString('use Illuminate\\Database\\Schema\\Blueprint;', $src);
        self::assertStringContainsString('use Illuminate\\Support\\Facades\\Schema;', $src);

        self::assertStringContainsString("Schema::create('deploys', function (Blueprint \$table)", $src);
        self::assertStringContainsString("Schema::dropIfExists('deploys')", $src);

        self::assertStringContainsString('$table->id();', $src);
        self::assertStringContainsString("\$table->foreignId('agent_id')->constrained()->cascadeOnDelete();", $src);
        self::assertStringContainsString("\$table->integer('amount_usd');", $src);
        self::assertStringContainsString("\$table->string('onchainos_session_id')->nullable()->unique();", $src);
        self::assertStringContainsString("\$table->string('status')->default('pending');", $src);
        self::assertStringContainsString("\$table->timestamp('paid_at')->nullable();", $src);
        self::assertStringContainsString('$table->timestamps();', $src);

        self::assertStringContainsString("\$table->index(['agent_id', 'status']);", $src);
    }
}
