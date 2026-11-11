<?php

namespace Tests\Unit;

use App\Themes\ThemeManager;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

/** Verifies directory-based Blade theme discovery and safe fallback. */
class ThemeManagerTest extends TestCase
{
    private string $themesPath;

    /** Creates a disposable theme directory. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->themesPath = sys_get_temp_dir().'/fragzone-themes-'.bin2hex(random_bytes(6));
        mkdir($this->themesPath.'/fragzone-dark/layouts', 0777, true);
        file_put_contents($this->themesPath.'/fragzone-dark/home.blade.php', '<h1>FragZone Dark</h1>');
    }

    /** Removes the disposable theme directory. */
    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->themesPath);
        parent::tearDown();
    }

    /** Discovers folders and falls back when a configured theme disappears. */
    public function test_it_discovers_blade_theme_directories_and_falls_back(): void
    {
        $manager = new ThemeManager($this->themesPath, 'fragzone', ['fragzone' => 'FragZone Core']);

        $this->assertSame(['fragzone', 'fragzone-dark'], array_keys($manager->available()));
        $this->assertSame('Fragzone Dark', $manager->available()['fragzone-dark']['name']);
        $this->assertSame($this->themesPath.'/fragzone-dark', $manager->activate('fragzone-dark')['views_path']);
        $this->assertSame('fragzone', $manager->activate('removed-theme')['slug']);
    }
}
