<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Adds localized content while preserving legacy text fields. */
    public function up(): void
    {
        Schema::table('server_categories', function (Blueprint $table): void {
            $table->json('name_translations')->nullable()->after('name');
            $table->json('description_translations')->nullable()->after('description');
        });
        Schema::table('game_servers', function (Blueprint $table): void {
            $table->json('description_translations')->nullable()->after('description');
        });
        Schema::table('news_posts', function (Blueprint $table): void {
            $table->json('title_translations')->nullable()->after('title');
            $table->json('excerpt_translations')->nullable()->after('excerpt');
            $table->json('body_translations')->nullable()->after('body');
        });

        DB::table('server_categories')->orderBy('id')->each(function (object $category): void {
            DB::table('server_categories')->where('id', $category->id)->update([
                'name_translations' => json_encode(['bg' => $category->name, 'en' => $category->name], JSON_UNESCAPED_UNICODE),
                'description_translations' => json_encode(['bg' => $category->description, 'en' => $category->description], JSON_UNESCAPED_UNICODE),
            ]);
        });
        DB::table('game_servers')->orderBy('id')->each(function (object $server): void {
            DB::table('game_servers')->where('id', $server->id)->update([
                'description_translations' => json_encode(['bg' => $server->description, 'en' => $server->description], JSON_UNESCAPED_UNICODE),
            ]);
        });
        DB::table('news_posts')->orderBy('id')->each(function (object $post): void {
            DB::table('news_posts')->where('id', $post->id)->update([
                'title_translations' => json_encode(['bg' => $post->title, 'en' => $post->title], JSON_UNESCAPED_UNICODE),
                'excerpt_translations' => json_encode(['bg' => $post->excerpt, 'en' => $post->excerpt], JSON_UNESCAPED_UNICODE),
                'body_translations' => json_encode(['bg' => $post->body, 'en' => $post->body], JSON_UNESCAPED_UNICODE),
            ]);
        });
    }

    /** Removes localized content fields. */
    public function down(): void
    {
        Schema::table('news_posts', function (Blueprint $table): void {
            $table->dropColumn(['title_translations', 'excerpt_translations', 'body_translations']);
        });
        Schema::table('game_servers', function (Blueprint $table): void {
            $table->dropColumn('description_translations');
        });
        Schema::table('server_categories', function (Blueprint $table): void {
            $table->dropColumn(['name_translations', 'description_translations']);
        });
    }
};
