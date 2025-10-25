<?php

use App\Models\Restaurant;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $restaurants = Restaurant::query()
            ->withCount('ratings')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'location' => $restaurant->location,
                    'overall_score' => $restaurant->overallScore(),
                    'visit_count' => $restaurant->ratings()->count(),
                    'rating_count' => $restaurant->ratings()->count(),
                    'created_at' => $restaurant->created_at,
                ];
            });

        // Calculate total submissions (unique combinations of voter_name + created_at timestamp)
        $allRatings = \App\Models\Rating::all();
        $totalSubmissions = $allRatings->groupBy(function ($rating) {
            return $rating->voter_name . '|' . $rating->created_at->format('Y-m-d H:i:s');
        })->count();

        // Calculate average score per dimension across all restaurants
        $dimensionAverages = [
            'taste' => round(\App\Models\Rating::where('dimension', 'taste')->avg('score'), 1),
            'service' => round(\App\Models\Rating::where('dimension', 'service')->avg('score'), 1),
            'atmosphere' => round(\App\Models\Rating::where('dimension', 'atmosphere')->avg('score'), 1),
            'value' => round(\App\Models\Rating::where('dimension', 'value')->avg('score'), 1),
        ];

        // Get top restaurant by overall score
        $topRestaurant = $restaurants->sortByDesc('overall_score')->first();

        return [
            'restaurants' => $restaurants,
            'total_submissions' => $totalSubmissions,
            'dimension_averages' => $dimensionAverages,
            'top_restaurant' => $topRestaurant,
        ];
    }
};
?>

<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Page Header -->
        <div class="mb-12">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        🍕 Dashboard
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">Manage and view your pizza restaurant ratings</p>
                </div>
                <a href="{{ route('restaurants.create') }}" class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium">
                    + Add Restaurant
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm font-medium">Top Pizza Place</p>
                        @if ($top_restaurant)
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $top_restaurant['name'] }}</p>
                        @else
                            <p class="text-3xl font-bold text-gray-500 dark:text-gray-400 mt-2">—</p>
                        @endif
                    </div>
                    <div class="text-4xl">🏆</div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm font-medium">Total Submissions</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $total_submissions }}</p>
                    </div>
                    <div class="text-4xl">📝</div>
                </div>
            </div>
        </div>

        <!-- Dimension Averages Bar Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 mb-12">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Average Score by Dimension</h3>

            <div class="space-y-6">
                @foreach (['taste' => '🍕 Taste', 'service' => '👤 Service', 'atmosphere' => '🎭 Atmosphere', 'value' => '💰 Value'] as $dimension => $label)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</p>
                            <span class="text-sm font-bold text-orange-600 dark:text-orange-400">{{ $dimension_averages[$dimension] }}/5</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-3">
                            <div class="bg-orange-600 h-3 rounded-full transition-all" style="width: {{ ($dimension_averages[$dimension] / 5) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Restaurants Table -->
        @if (count($restaurants) > 0)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Restaurant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ratings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach ($restaurants as $restaurant)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $restaurant['name'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $restaurant['location'] ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold text-orange-600">
                                            {{ number_format($restaurant['overall_score'], 1) }}
                                        </span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300 ml-2">/5</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $restaurant['rating_count'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                    <a href="{{ route('restaurants.show', $restaurant['id']) }}" class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300 font-medium">
                                        View
                                    </a>
                                    <a href="{{ route('restaurants.edit', $restaurant['id']) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-12 text-center">
                <div class="text-5xl mb-4">🏜️</div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No restaurants yet</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Create your first restaurant to start tracking ratings</p>
                <a href="{{ route('restaurants.create') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium">
                    Add Your First Restaurant
                </a>
            </div>
        @endif
    </div>
</div>
