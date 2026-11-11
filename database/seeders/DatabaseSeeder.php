<?php

namespace Database\Seeders;

use App\Models\GameServer;
use App\Models\NewsPost;
use App\Models\ServerCategory;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Seeds a complete local preview. */
class DatabaseSeeder extends Seeder
{
    /** Creates example categories, servers, news and an administrator. */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@fragzone.local'], ['name' => 'FragZone Admin', 'password' => 'ChangeMe123!', 'is_admin' => true]);
        SiteSetting::query()->firstOrCreate([], [
            'site_name' => 'FragZone',
            'site_abbreviation' => 'FZ',
            'network_label' => ['bg' => 'ГЕЙМЪРСКА ОБЩНОСТ', 'en' => 'COMMUNITY NETWORK'],
            'meta_title' => ['bg' => 'Counter-Strike сървъри', 'en' => 'Counter-Strike Servers'],
            'meta_description' => ['bg' => 'Counter-Strike 1.6 и CS:GO сървъри със статус, карти и играчи на живо.', 'en' => 'Counter-Strike 1.6 and CS:GO servers with live status, maps and players.'],
            'meta_keywords' => ['bg' => 'Counter-Strike, CS 1.6, CS:GO, сървъри, играчи, карти', 'en' => 'Counter-Strike, CS 1.6, CS:GO, servers, players, maps'],
            'hero_eyebrow' => ['bg' => 'БАЛКАНСКА COUNTER-STRIKE ОБЩНОСТ', 'en' => 'BALKAN COUNTER-STRIKE COMMUNITY'],
            'hero_primary' => ['bg' => 'ИГРАЙ.', 'en' => 'PLAY.'],
            'hero_accent' => ['bg' => 'ПОБЕДИ.', 'en' => 'WIN.'],
            'home_description' => ['bg' => 'Подбрани CS 1.6 и CS:GO сървъри, статистика на живо и общност, в която всеки рунд има значение.', 'en' => 'Curated CS 1.6 and CS:GO servers, live statistics and a community where every round matters.'],
            'footer_text' => ['bg' => 'Играй честно. Уважавай отбора. Направи следващия рунд незабравим.', 'en' => 'Play fair. Respect the team. Make the next round unforgettable.'],
        ]);
        $public = ServerCategory::updateOrCreate(['slug' => 'public'], ['name' => 'Public', 'description' => 'Класически community сървъри с приятелска атмосфера.', 'accent_color' => '#f4b942']);
        $competitive = ServerCategory::updateOrCreate(['slug' => 'competitive'], ['name' => 'Competitive', 'description' => 'Състезателни правила и подредени отбори.', 'accent_color' => '#55d6be']);
        $zombie = ServerCategory::updateOrCreate(['slug' => 'zombie'], ['name' => 'Zombie', 'description' => 'Zombie escape и infection модове.', 'accent_color' => '#ee6c4d']);
        GameServer::updateOrCreate(['slug' => 'fragzone-classic'], ['server_category_id' => $public->id, 'name' => 'FragZone Classic #1', 'game' => 'cs16', 'protocol' => 'goldsrc', 'host' => '127.0.0.1', 'port' => 27015, 'description' => 'Класически 5v5 и public ротации с активна администрация.', 'location' => 'Sofia, BG', 'is_featured' => true, 'is_published' => true, 'is_online' => true, 'current_map' => 'de_dust2', 'current_players' => 18, 'max_players' => 32, 'last_ping_ms' => 14, 'last_queried_at' => now()]);
        GameServer::updateOrCreate(['slug' => 'fragzone-retakes'], ['server_category_id' => $competitive->id, 'name' => 'FragZone Retakes', 'game' => 'csgo', 'protocol' => 'source', 'host' => '127.0.0.2', 'port' => 27016, 'description' => 'Бързи retake рундове за загрявка и тренировка.', 'location' => 'Frankfurt, DE', 'is_featured' => true, 'is_published' => true, 'is_online' => true, 'current_map' => 'de_mirage', 'current_players' => 9, 'max_players' => 12, 'last_ping_ms' => 38, 'last_queried_at' => now()]);
        GameServer::updateOrCreate(['slug' => 'fragzone-zombie'], ['server_category_id' => $zombie->id, 'name' => 'FragZone Zombie Escape', 'game' => 'cs16', 'protocol' => 'goldsrc', 'host' => '127.0.0.3', 'port' => 27017, 'description' => 'Custom zombie карти, XP и седмични събития.', 'location' => 'Sofia, BG', 'is_published' => true, 'is_online' => false, 'current_map' => 'ze_jurassicpark', 'current_players' => 0, 'max_players' => 32, 'last_queried_at' => now()->subMinutes(2)]);
        NewsPost::updateOrCreate(['slug' => 'dob-re-doshli-v-fragzone'], ['title' => 'Добре дошли във FragZone', 'excerpt' => 'Новият дом на нашите Counter-Strike сървъри вече е онлайн.', 'body' => "Създадохме едно място за live статус, карти, играчи и всички важни новини.\n\nТова е първата версия и ще я развиваме заедно с общността.", 'is_published' => true, 'published_at' => now()->subDay()]);
        NewsPost::updateOrCreate(['slug' => 'weekend-retakes'], ['title' => 'Retakes уикенд', 'excerpt' => 'Двоен рейтинг и вечерни mini турнири в Retakes.', 'body' => 'Влез в сървъра между 19:00 и 23:00 за двойни точки и специална класация.', 'is_published' => true, 'published_at' => now()->subHours(6)]);
    }
}
