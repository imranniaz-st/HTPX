<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('metric_type');
            $table->enum('operator', ['>', '<', '=', '!=']);
            $table->float('threshold');
            $table->unsignedInteger('duration_minutes')->default(5);
            $table->enum('severity', ['critical', 'warning', 'info']);
            $table->boolean('is_enabled')->default(true);
            $table->string('notify_email')->nullable();
            $table->string('notify_webhook_url')->nullable();
            $table->timestamps();
            $table->index('server_id');
            $table->index('is_enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_rules');
    }
};
