<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Stores the selected installed theme. */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->string('theme_slug', 80)->default('fragzone')->after('site_abbreviation');
        });
    }

    /** Removes the theme selection. */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn('theme_slug');
        });
    }
};
