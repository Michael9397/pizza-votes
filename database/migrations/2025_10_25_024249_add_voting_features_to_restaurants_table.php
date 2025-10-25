<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->boolean('voting_enabled')->default(false)->after('notes');
        });

        // Generate slugs for existing restaurants
        $restaurants = \DB::table('restaurants')->get();
        foreach ($restaurants as $restaurant) {
            \DB::table('restaurants')
                ->where('id', $restaurant->id)
                ->update(['slug' => Str::slug($restaurant->name)]);
        }

        // Make slug non-nullable after data migration
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['slug', 'voting_enabled']);
        });
    }
};
