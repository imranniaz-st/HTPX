<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->float('cpu_usage')->nullable();
            $table->float('memory_usage')->nullable();
            $table->bigInteger('memory_total')->nullable();
            $table->bigInteger('disk_usage')->nullable();
            $table->bigInteger('disk_total')->nullable();
            $table->bigInteger('disk_free')->nullable();
            $table->bigInteger('network_in')->nullable();
            $table->bigInteger('network_out')->nullable();
            $table->unsignedSmallInteger('cpu_count')->nullable();
            $table->float('load_average')->nullable();
            $table->unsignedInteger('process_count')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
            $table->index('server_id');
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_metrics');
    }
};
