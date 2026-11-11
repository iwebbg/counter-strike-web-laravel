<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Replaces mixed-language demo content in existing installations. */
    public function up(): void
    {
        $categories = [
            'public' => ['Public', ['bg' => 'Публични', 'en' => 'Public'], ['bg' => 'Класически сървъри с приятелска атмосфера.', 'en' => 'Classic community servers with a friendly atmosphere.']],
            'competitive' => ['Competitive', ['bg' => 'Състезателни', 'en' => 'Competitive'], ['bg' => 'Състезателни правила и подредени отбори.', 'en' => 'Competitive rules and balanced teams.']],
            'zombie' => ['Zombie', ['bg' => 'Зомбита', 'en' => 'Zombie'], ['bg' => 'Режими за бягство и инфекция със зомбита.', 'en' => 'Zombie escape and infection modes.']],
        ];
        foreach ($categories as $slug => [$legacyName, $names, $descriptions]) {
            DB::table('server_categories')->where('slug', $slug)->where('name', $legacyName)->update([
                'name' => $names['bg'],
                'name_translations' => json_encode($names, JSON_UNESCAPED_UNICODE),
                'description' => $descriptions['bg'],
                'description_translations' => json_encode($descriptions, JSON_UNESCAPED_UNICODE),
            ]);
        }

        $servers = [
            'fragzone-classic' => ['Класически 5v5 и public ротации с активна администрация.', ['bg' => 'Класически 5v5 и публични ротации с активна администрация.', 'en' => 'Classic 5v5 and public rotations with active administration.']],
            'fragzone-retakes' => ['Бързи retake рундове за загрявка и тренировка.', ['bg' => 'Бързи рундове за превземане, загрявка и тренировка.', 'en' => 'Fast retake rounds for warmup and practice.']],
            'fragzone-zombie' => ['Custom zombie карти, XP и седмични събития.', ['bg' => 'Персонализирани зомби карти, опит и седмични събития.', 'en' => 'Custom zombie maps, experience and weekly events.']],
        ];
        foreach ($servers as $slug => [$legacyDescription, $descriptions]) {
            DB::table('game_servers')->where('slug', $slug)->where('description', $legacyDescription)->update([
                'description' => $descriptions['bg'],
                'description_translations' => json_encode($descriptions, JSON_UNESCAPED_UNICODE),
            ]);
        }

        $posts = [
            'dob-re-doshli-v-fragzone' => [
                'legacy_title' => 'Добре дошли във FragZone',
                'title' => ['bg' => 'Добре дошли във FragZone', 'en' => 'Welcome to FragZone'],
                'excerpt' => ['bg' => 'Новият дом на нашите Counter-Strike сървъри вече е онлайн.', 'en' => 'The new home of our Counter-Strike servers is now online.'],
                'body' => ['bg' => '<p>Създадохме едно място за статус на живо, карти, играчи и всички важни новини.</p><p>Това е първата версия и ще я развиваме заедно с общността.</p>', 'en' => '<p>We created one place for live status, maps, players and every important update.</p><p>This is the first version, and we will keep improving it with the community.</p>'],
            ],
            'weekend-retakes' => [
                'legacy_title' => 'Retakes уикенд',
                'title' => ['bg' => 'Уикенд за превземане', 'en' => 'Retakes Weekend'],
                'excerpt' => ['bg' => 'Двоен рейтинг и вечерни мини турнири.', 'en' => 'Double ranking points and evening mini tournaments.'],
                'body' => ['bg' => '<p>Влез в сървъра между 19:00 и 23:00 за двойни точки и специална класация.</p>', 'en' => '<p>Join the server between 19:00 and 23:00 for double points and a special leaderboard.</p>'],
            ],
        ];
        foreach ($posts as $slug => $content) {
            DB::table('news_posts')->where('slug', $slug)->where('title', $content['legacy_title'])->update([
                'title' => $content['title']['bg'],
                'title_translations' => json_encode($content['title'], JSON_UNESCAPED_UNICODE),
                'excerpt' => $content['excerpt']['bg'],
                'excerpt_translations' => json_encode($content['excerpt'], JSON_UNESCAPED_UNICODE),
                'body' => $content['body']['bg'],
                'body_translations' => json_encode($content['body'], JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    /** Data normalization is intentionally irreversible. */
    public function down(): void {}
};
