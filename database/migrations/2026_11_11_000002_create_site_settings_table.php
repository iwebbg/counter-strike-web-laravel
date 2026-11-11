<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Creates the singleton branding and SEO settings table. */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('site_name', 80);
            $table->string('site_abbreviation', 12);
            $table->json('network_label');
            $table->json('meta_title');
            $table->json('meta_description');
            $table->json('meta_keywords');
            $table->json('hero_eyebrow');
            $table->json('hero_primary');
            $table->json('hero_accent');
            $table->json('home_description');
            $table->json('footer_text');
            $table->timestamps();
        });

        DB::table('site_settings')->insert([
            'site_name' => 'FragZone',
            'site_abbreviation' => 'FZ',
            'network_label' => json_encode(['bg' => 'ГЕЙМЪРСКА ОБЩНОСТ', 'en' => 'COMMUNITY NETWORK'], JSON_UNESCAPED_UNICODE),
            'meta_title' => json_encode(['bg' => 'Counter-Strike сървъри', 'en' => 'Counter-Strike Servers'], JSON_UNESCAPED_UNICODE),
            'meta_description' => json_encode(['bg' => 'Counter-Strike 1.6 и CS:GO сървъри със статус, карти и играчи на живо.', 'en' => 'Counter-Strike 1.6 and CS:GO servers with live status, maps and players.'], JSON_UNESCAPED_UNICODE),
            'meta_keywords' => json_encode(['bg' => 'Counter-Strike, CS 1.6, CS:GO, сървъри, играчи, карти', 'en' => 'Counter-Strike, CS 1.6, CS:GO, servers, players, maps'], JSON_UNESCAPED_UNICODE),
            'hero_eyebrow' => json_encode(['bg' => 'БАЛКАНСКА COUNTER-STRIKE ОБЩНОСТ', 'en' => 'BALKAN COUNTER-STRIKE COMMUNITY'], JSON_UNESCAPED_UNICODE),
            'hero_primary' => json_encode(['bg' => 'ИГРАЙ.', 'en' => 'PLAY.'], JSON_UNESCAPED_UNICODE),
            'hero_accent' => json_encode(['bg' => 'ПОБЕДИ.', 'en' => 'WIN.'], JSON_UNESCAPED_UNICODE),
            'home_description' => json_encode(['bg' => 'Подбрани CS 1.6 и CS:GO сървъри, статистика на живо и общност, в която всеки рунд има значение.', 'en' => 'Curated CS 1.6 and CS:GO servers, live statistics and a community where every round matters.'], JSON_UNESCAPED_UNICODE),
            'footer_text' => json_encode(['bg' => 'Играй честно. Уважавай отбора. Направи следващия рунд незабравим.', 'en' => 'Play fair. Respect the team. Make the next round unforgettable.'], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** Removes site settings. */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
