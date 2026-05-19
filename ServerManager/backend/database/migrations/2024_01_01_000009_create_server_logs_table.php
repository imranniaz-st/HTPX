<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', [
                'system', 'ssh', 'firewall', 'alert', 
                'password_change', 'reboot', 'package', 'custom'
            ])->default('system');
            $table->enum('level', ['debug', 'info', 'warning', 'error'])->default('info');
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->text('command')->nullable();
            $table->text('output')->nullable();
            $table->text('error')->nullable();
            $table->unsignedInteger('status_code')->nullable();
            $table->enum('source', ['system', 'agent', 'user', 'remote'])->default('system');
            $table->timestamp('timestamp')->nullable();
            $table->timestamps();
            $table->index('server_id');
            $table->index('type');
            $table->index('level');
            $table->index('timestamp');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_logs');
    }
};
