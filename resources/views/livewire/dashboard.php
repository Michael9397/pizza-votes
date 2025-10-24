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

<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Page Header -->
        <div class="mb-12">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        🍕 Dashboard
                    </h1>
                    <p class="text-gray-600 mt-2">Manage and view your pizza restaurant ratings</p>
                </div>
                <a href="{{ route('restaurants.create') }}" class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium">
                    + Add Restaurant
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        @php
            $totalRatings = $restaurants->sum('rating_count');
            $avgScore = $restaurants->count() > 0 ? number_format($restaurants->avg('overall_score'), 1) : 0;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Restaurants</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ count($restaurants) }}</p>
                    </div>
                    <div class="text-4xl">🍕</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Ratings</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalRatings }}</p>
                    </div>
                    <div class="text-4xl">⭐</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Average Score</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ $avgScore }}/5</p>
                    </div>
                    <div class="text-4xl">📊</div>
                </div>
            </div>
        </div>

        <!-- Restaurants Table -->
        @if (count($restaurants) > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ratings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($restaurants as $restaurant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $restaurant['name'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $restaurant['location'] ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold text-orange-600">
                                            {{ number_format($restaurant['overall_score'], 1) }}
                                        </span>
                                        <span class="text-sm text-gray-600 ml-2">/5</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $restaurant['rating_count'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                    <a href="{{ route('restaurants.show', $restaurant['id']) }}" class="text-orange-600 hover:text-orange-900 font-medium">
                                        View
                                    </a>
                                    <a href="{{ route('restaurants.edit', $restaurant['id']) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
