<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('#3b82f6');
        });

        Schema::create('server_tag', function (Blueprint $table) {
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->primary(['server_id', 'tag_id']);
        });

        Schema::create('server_user', function (Blueprint $table) {
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('username')->nullable();
            $table->enum('role', ['admin', 'manager', 'viewer'])->default('viewer');
            $table->primary(['server_id', 'user_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_user');
        Schema::dropIfExists('server_tag');
        Schema::dropIfExists('tags');
    }
};
