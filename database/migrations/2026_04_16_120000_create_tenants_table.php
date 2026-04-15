<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            // telegram_bot_token and llm_api_key are set on the tenant container env at provision time and never persisted here.
            $table->uuid('id')->primary();
            $table->string('user_id')->nullable();
            $table->string('agent_name')->index();
            $table->string('provider')->default('coolify');
            $table->string('provider_ref')->nullable()->index();
            $table->string('fqdn')->nullable();
            $table->string('image_tag')->nullable();
            $table->string('status')->default('provisioning')->index();
            $table->text('personality')->nullable();
            $table->json('allowlist')->nullable();
            $table->string('llm_provider')->nullable()->default('anthropic');
            $table->text('last_error')->nullable();
            $table->timestamp('provisioned_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
