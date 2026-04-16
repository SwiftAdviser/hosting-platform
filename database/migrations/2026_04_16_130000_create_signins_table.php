<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signins', function (Blueprint $table) {
            $table->id();
            $table->string('google_id');
            $table->string('email')->index();
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signins');
    }
};
