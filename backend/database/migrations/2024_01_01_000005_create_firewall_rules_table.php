<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firewall_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('action', ['allow', 'deny']);
            $table->enum('protocol', ['tcp', 'udp', 'icmp', 'all']);
            $table->unsignedInteger('port')->nullable();
            $table->ipAddress('source_ip')->nullable();
            $table->ipAddress('destination_ip')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->index('server_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firewall_rules');
    }
};
