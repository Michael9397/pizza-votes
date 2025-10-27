<?php

use App\Models\Restaurant;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $restaurants = Restaurant::query()
            ->withCount('ratings')
            ->get()
            ->map(function ($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'location' => $restaurant->location,
                    'overall_score' => $restaurant->overallScore(),
                    'visit_count' => $restaurant->ratings()->count(),
                    'dimension_scores' => $restaurant->averageScorePerDimension(),
                ];
            });

        // Get top pizza place
        $topRestaurant = $restaurants->sortByDesc('overall_score')->first();

        // Calculate aggregate scores across all restaurants
        $allRatings = \App\Models\Rating::all();
        $aggregateScores = [
            'taste' => round($allRatings->where('dimension', 'taste')->avg('score'), 1),
            'service' => round($allRatings->where('dimension', 'service')->avg('score'), 1),
            'atmosphere' => round($allRatings->where('dimension', 'atmosphere')->avg('score'), 1),
            'value' => round($allRatings->where('dimension', 'value')->avg('score'), 1),
        ];

        $totalSubmissions = $allRatings->groupBy(function ($rating) {
            return $rating->voter_name . '|' . $rating->created_at->format('Y-m-d H:i:s');
        })->count();

        return [
            'restaurants' => $restaurants,
            'top_restaurant' => $topRestaurant,
            'aggregate_scores' => $aggregateScores,
            'total_submissions' => $totalSubmissions,
        ];
    }
};
?>

<div class="min-h-screen bg-gradient-to-b from-orange-50 to-white dark:from-zinc-900 dark:to-zinc-800" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val)); $watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark'))" :class="darkMode && 'dark'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Top Navigation Bar -->
        <div class="py-4 flex justify-between items-center">
            <div></div>
            <div class="flex gap-4 items-center">
                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 transition">
                    <span x-show="!darkMode" class="text-lg">🌙</span>
                    <span x-show="darkMode" class="text-lg">☀️</span>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                        Sign up
                    </a>
                @endauth
            </div>
        </div>

        <!-- Header -->
        <div class="py-12 text-center">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                🍕 Pizza Family Ratings
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">
                Rate and track your favorite pizza places together
            </p>

            @auth
                <div class="space-x-4">
                    <a href="{{ route('dashboard') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                        Dashboard
                    </a>
                </div>
            @endauth
        </div>

        <!-- Aggregated Stats Section -->
        @if ($top_restaurant || $total_submissions > 0)
            <div class="mb-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Top Pizza Place Card -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-300 text-sm font-medium mb-2">🏆 Top Pizza Place</p>
                                @if ($top_restaurant)
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $top_restaurant['name'] }}</h3>
                                    <p class="text-lg text-orange-600 dark:text-orange-400 font-semibold mt-2">{{ number_format($top_restaurant['overall_score'], 1) }}/5</p>
                                @else
                                    <p class="text-2xl text-gray-500 dark:text-gray-400">No ratings yet</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Total Submissions Card -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-300 text-sm font-medium mb-2">📝 All Submissions</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $total_submissions }}</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">family votes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Across All Restaurants Scores -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Across All Restaurants</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach (['taste' => '🍕 Taste', 'service' => '👤 Service', 'atmosphere' => '🎭 Atmosphere', 'value' => '💰 Value'] as $dimension => $label)
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    <span class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $aggregate_scores[$dimension] }}/5</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-3">
                                    <div class="bg-orange-600 h-3 rounded-full transition-all" style="width: {{ ($aggregate_scores[$dimension] / 5) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Restaurants Grid -->
        @if ($restaurants->count() > 0)
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Recent Ratings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($restaurants as $restaurant)
                        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow hover:shadow-lg transition p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ $restaurant['name'] }}
                            </h3>
                            @if ($restaurant['location'])
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    📍 {{ $restaurant['location'] }}
                                </p>
                            @endif

                            <!-- Overall Score -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Score</span>
                                    <span class="text-2xl font-bold text-orange-600">
                                        {{ number_format($restaurant['overall_score'], 1) }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                                    <div class="bg-orange-600 h-2 rounded-full" style="width: {{ ($restaurant['overall_score'] / 5) * 100 }}%"></div>
                                </div>
                            </div>

                            <!-- Dimension Scores -->
                            @if (count($restaurant['dimension_scores']) > 0)
                                <div class="space-y-2 mb-4">
                                    @foreach ($restaurant['dimension_scores'] as $dimension => $score)
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400 capitalize">{{ ucfirst($dimension) }}</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">
                                                {{ number_format($score, 1) }}/5
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Visit Count -->
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                {{ $restaurant['visit_count'] }} {{ Str::plural('visit', $restaurant['visit_count']) }}
                            </div>

                            <!-- View Details Button -->
                            @auth
                                <a href="{{ route('restaurants.show', $restaurant['id']) }}" class="w-full block text-center px-4 py-2 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded hover:bg-orange-200 dark:hover:bg-orange-900/50 transition font-medium">
                                    View Details
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="w-full block text-center px-4 py-2 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded hover:bg-orange-200 dark:hover:bg-orange-900/50 transition font-medium">
                                    Sign in to Vote
                                </a>
                            @endauth
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-6">
                    No restaurants yet. Sign in to add your favorite pizza places!
                </p>
                @guest
                    <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                        Get Started
                    </a>
                @endguest
            </div>
        @endif
    </div>
</div>
