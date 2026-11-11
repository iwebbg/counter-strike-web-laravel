<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Creates administration, content and telemetry tables. */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
        Schema::create('cache', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });
        Schema::create('cache_locks', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
        Schema::create('jobs', function (Blueprint $table): void {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
        Schema::create('server_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('accent_color', 7)->default('#f4b942');
            $table->timestamps();
        });
        Schema::create('game_servers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('server_category_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('game', 16);
            $table->string('protocol', 16);
            $table->string('host');
            $table->unsignedSmallInteger('port');
            $table->text('description')->nullable();
            $table->string('location', 80)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_online')->default(false);
            $table->string('current_map')->nullable();
            $table->unsignedSmallInteger('current_players')->default(0);
            $table->unsignedSmallInteger('max_players')->default(0);
            $table->unsignedSmallInteger('last_ping_ms')->nullable();
            $table->timestamp('last_queried_at')->nullable();
            $table->text('last_query_error')->nullable();
            $table->timestamps();
            $table->unique(['host', 'port']);
            $table->index(['is_published', 'is_online']);
        });
        Schema::create('server_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_server_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_online');
            $table->string('map')->nullable();
            $table->unsignedSmallInteger('players')->default(0);
            $table->unsignedSmallInteger('max_players')->default(0);
            $table->unsignedSmallInteger('ping_ms')->nullable();
            $table->json('player_data')->nullable();
            $table->timestamp('queried_at');
            $table->index(['game_server_id', 'queried_at']);
        });
        Schema::create('news_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('body');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /** Removes portal tables. */
    public function down(): void
    {
        Schema::dropIfExists('news_posts');
        Schema::dropIfExists('server_snapshots');
        Schema::dropIfExists('game_servers');
        Schema::dropIfExists('server_categories');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
