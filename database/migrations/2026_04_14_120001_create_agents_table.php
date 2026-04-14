<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('personality');
            $table->string('icon')->nullable();
            $table->string('status')->default('pending');
            $table->string('kiloclaw_id')->nullable();
            $table->string('wallet_address')->nullable();
            $table->text('bot_token_encrypted')->nullable();
            $table->text('allowlist')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
