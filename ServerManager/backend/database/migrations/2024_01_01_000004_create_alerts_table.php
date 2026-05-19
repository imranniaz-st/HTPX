<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['disk_full', 'high_cpu', 'high_memory', 'server_offline', 'custom']);
            $table->enum('severity', ['critical', 'warning', 'info'])->default('info');
            $table->string('title');
            $table->text('message');
            $table->string('metric_type')->nullable();
            $table->float('metric_value')->nullable();
            $table->float('threshold')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index('server_id');
            $table->index('is_resolved');
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
