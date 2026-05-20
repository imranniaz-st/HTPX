<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->ipAddress('ip_address')->unique();
            $table->string('hostname')->nullable();
            $table->unsignedSmallInteger('ssh_port')->default(22);
            $table->string('ssh_username')->nullable();
            $table->enum('ssh_auth_type', ['key', 'password'])->default('key');
            $table->string('ssh_key_path')->nullable();
            $table->enum('os_type', ['linux', 'windows', 'macos'])->default('linux');
            $table->enum('status', ['online', 'offline', 'unknown'])->default('unknown');
            $table->text('description')->nullable();
            $table->timestamp('last_heartbeat')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('last_heartbeat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
