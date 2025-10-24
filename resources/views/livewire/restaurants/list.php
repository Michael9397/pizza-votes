<?php

use App\Models\Restaurant;
use Livewire\Volt\Component;

class extends Component
{
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

        return [
            'restaurants' => $restaurants,
        ];
    }
}
?>

<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-12">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        🍕 Restaurants
                    </h1>
                    <p class="text-gray-600 mt-2">All tracked pizza restaurants</p>
                </div>
                <a href="{{ route('restaurants.create') }}" class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium">
                    + Add Restaurant
                </a>
            </div>
        </div>

        <!-- Restaurants Grid -->
        @if (count($restaurants) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($restaurants as $restaurant)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $restaurant['name'] }}
                        </h3>
                        @if ($restaurant['location'])
                            <p class="text-sm text-gray-600 mb-4">
                                📍 {{ $restaurant['location'] }}
                            </p>
                        @endif

                        <!-- Overall Score -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Score</span>
                                <span class="text-2xl font-bold text-orange-600">
                                    {{ number_format($restaurant['overall_score'], 1) }}/5
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: {{ ($restaurant['overall_score'] / 5) * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Visit Count -->
                        <div class="text-sm text-gray-500 mb-4">
                            {{ $restaurant['visit_count'] }} {{ Str::plural('visit', $restaurant['visit_count']) }}
                        </div>

                        <!-- Actions -->
                        <div class="space-x-2">
                            <a href="{{ route('restaurants.show', $restaurant['id']) }}" class="inline-block px-4 py-2 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition font-medium text-sm">
                                View
                            </a>
                            <a href="{{ route('restaurants.edit', $restaurant['id']) }}" class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition font-medium text-sm">
                                Edit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="text-5xl mb-4">🏜️</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No restaurants yet</h3>
                <p class="text-gray-600 mb-6">Create your first restaurant to start tracking ratings</p>
                <a href="{{ route('restaurants.create') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium">
                    Add Your First Restaurant
                </a>
            </div>
        @endif
    </div>
</div>
