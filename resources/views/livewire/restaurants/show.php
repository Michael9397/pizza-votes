<?php

use App\Models\Restaurant;
use Livewire\Volt\Component;

class extends Component
{
    public Restaurant $restaurant;

    public function with(): array
    {
        $ratings = $this->restaurant->ratings()
            ->orderBy('visited_at', 'desc')
            ->get()
            ->groupBy('dimension')
            ->map(function ($dimensionRatings) {
                return [
                    'ratings' => $dimensionRatings,
                    'average' => round($dimensionRatings->avg('score'), 1),
                    'count' => $dimensionRatings->count(),
                ];
            });

        $visits = $this->restaurant->ratings()
            ->selectRaw('visited_at, COUNT(DISTINCT CONCAT(COALESCE(user_id, voter_name))) as visitor_count')
            ->groupBy('visited_at')
            ->orderBy('visited_at', 'desc')
            ->get();

        return [
            'dimension_ratings' => $ratings,
            'overall_score' => $this->restaurant->overallScore(),
            'average_scores' => $this->restaurant->averageScorePerDimension(),
            'visit_count' => $this->restaurant->ratings()->count(),
            'visits' => $visits,
        ];
    }
}
?>

<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('dashboard') }}" class="text-orange-600 hover:text-orange-700 mb-6 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        🍕 {{ $restaurant->name }}
                    </h1>
                    @if ($restaurant->location)
                        <p class="text-lg text-gray-600">📍 {{ $restaurant->location }}</p>
                    @endif
                    @if ($restaurant->notes)
                        <p class="text-gray-600 mt-4 max-w-2xl">{{ $restaurant->notes }}</p>
                    @endif
                </div>
                <a href="{{ route('restaurants.edit', $restaurant) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Edit
                </a>
            </div>
        </div>

        <!-- Overall Score Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium mb-2">Overall Score</p>
                <p class="text-4xl font-bold text-orange-600">{{ number_format($overall_score, 1) }}</p>
                <p class="text-gray-600 text-sm mt-2">/5 stars</p>
                <div class="mt-4 w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-orange-600 h-3 rounded-full" style="width: {{ ($overall_score / 5) * 100 }}%"></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium mb-2">Total Ratings</p>
                <p class="text-4xl font-bold text-blue-600">{{ $visit_count }}</p>
                <p class="text-gray-600 text-sm mt-2">rating submissions</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium mb-2">Dimension Scores</p>
                <div class="space-y-2 mt-3">
                    @foreach ($average_scores as $dimension => $score)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 capitalize font-medium">{{ ucfirst($dimension) }}</span>
                            <span class="font-bold text-gray-900">{{ number_format($score, 1) }}/5</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Dimension Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach ($dimension_ratings as $dimension => $data)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 capitalize">{{ ucfirst($dimension) }}</h3>

                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Average</span>
                            <span class="text-2xl font-bold text-orange-600">{{ $data['average'] }}/5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: {{ ($data['average'] / 5) * 100 }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @foreach ([5, 4, 3, 2, 1] as $score)
                            @php
                                $count = $data['ratings']->where('score', $score)->count();
                                $percentage = $data['count'] > 0 ? ($count / $data['count']) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ $score }} star{{ $score !== 1 ? 's' : '' }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-gray-500 text-xs w-8 text-right">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Vote Button -->
        <div class="bg-white rounded-lg shadow p-6 mb-8 text-center">
            <p class="text-gray-600 mb-4">Have you visited this restaurant?</p>
            <a href="{{ route('restaurants.vote', $restaurant) }}" class="inline-block px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold text-lg">
                + Add Your Rating
            </a>
        </div>

        <!-- Recent Ratings -->
        @if ($visit_count > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Recent Ratings</h3>

                <div class="space-y-4">
                    @php
                        $recentRatings = $restaurant->ratings()
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get()
                            ->groupBy(function ($rating) {
                                return $rating->visited_at->format('Y-m-d');
                            });
                    @endphp

                    @foreach ($recentRatings as $date => $ratings)
                        <div class="border-l-4 border-orange-600 pl-4 py-2">
                            <p class="text-sm font-semibold text-gray-900 mb-3">
                                {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                            </p>

                            @php
                                $dimensionScores = $ratings->groupBy('dimension')->map(function ($items) {
                                    return $items->first()->score;
                                });
                            @endphp

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach ($dimensionScores as $dimension => $score)
                                    <div class="bg-orange-50 rounded p-3">
                                        <p class="text-xs text-gray-600 capitalize mb-1">{{ ucfirst($dimension) }}</p>
                                        <p class="text-lg font-bold text-orange-600">{{ $score }}/5</p>
                                    </div>
                                @endforeach
                            </div>

                            @php
                                $firstRating = $ratings->first();
                                $voterNames = $ratings->pluck('voter_name')->filter()->unique();
                            @endphp

                            @if ($firstRating->notes)
                                <p class="text-sm text-gray-600 mt-3 italic">{{ $firstRating->notes }}</p>
                            @endif

                            @if ($voterNames->count() > 0)
                                <p class="text-xs text-gray-500 mt-2">
                                    Rated by: {{ $voterNames->implode(', ') }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
