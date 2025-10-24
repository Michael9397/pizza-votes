<?php

use App\Models\Rating;
use App\Models\Restaurant;
use Livewire\Volt\Component;

class extends Component
{
    public Restaurant $restaurant;
    public string $voter_name = '';
    public array $scores = ['taste' => 3, 'service' => 3, 'atmosphere' => 3, 'value' => 3];
    public string $notes = '';
    public ?string $visited_at = null;
    public bool $submitted = false;

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->visited_at = now()->format('Y-m-d');
    }

    public function submit()
    {
        $this->validate([
            'voter_name' => ['required_if:' . auth()->guest(), 'nullable', 'string', 'max:255'],
            'scores.taste' => ['required', 'integer', 'min:1', 'max:5'],
            'scores.service' => ['required', 'integer', 'min:1', 'max:5'],
            'scores.atmosphere' => ['required', 'integer', 'min:1', 'max:5'],
            'scores.value' => ['required', 'integer', 'min:1', 'max:5'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'visited_at' => ['required', 'date'],
        ]);

        foreach ($this->scores as $dimension => $score) {
            Rating::create([
                'restaurant_id' => $this->restaurant->id,
                'user_id' => auth()->id(),
                'voter_name' => $this->voter_name ?: null,
                'dimension' => $dimension,
                'score' => (int) $score,
                'notes' => $this->notes ?: null,
                'visited_at' => $this->visited_at,
            ]);
        }

        $this->submitted = true;
    }

    public function with(): array
    {
        $dimensions = [
            'taste' => 'How did the pizza taste?',
            'service' => 'How was the service?',
            'atmosphere' => 'How was the atmosphere?',
            'value' => 'Was it good value?',
        ];

        return [
            'dimensions' => $dimensions,
            'is_authenticated' => auth()->check(),
        ];
    }
}
?>

<div class="min-h-screen bg-gradient-to-b from-orange-50 to-white py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('home') }}" class="text-orange-600 hover:text-orange-700 mb-6 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Results
        </a>

        <div class="bg-white rounded-lg shadow-lg p-8">
            @if ($submitted)
                <div class="text-center">
                    <div class="text-5xl mb-4">✨</div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Thanks for voting!</h2>
                    <p class="text-gray-600 mb-6">Your ratings have been saved.</p>
                    <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                        Back to Results
                    </a>
                </div>
            @else
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        🍕 {{ $restaurant->name }}
                    </h1>
                    @if ($restaurant->location)
                        <p class="text-gray-600">📍 {{ $restaurant->location }}</p>
                    @endif
                </div>

                <form wire:submit="submit" class="space-y-8">
                    <!-- Name (only for guests) -->
                    @unless ($is_authenticated)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Your Name *
                            </label>
                            <input
                                type="text"
                                wire:model="voter_name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                placeholder="Enter your name"
                                required
                            />
                            @error('voter_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    @endunless

                    <!-- Visit Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Visit Date *
                        </label>
                        <input
                            type="date"
                            wire:model="visited_at"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                            required
                        />
                        @error('visited_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ratings -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Rate This Restaurant</h3>

                        @foreach ($dimensions as $dimension => $label)
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-4">
                                    {{ $label }}
                                </label>
                                <div class="flex gap-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button
                                            type="button"
                                            wire:click="$set('scores.{{ $dimension }}', {{ $i }})"
                                            class="w-12 h-12 rounded-lg font-semibold transition
                                                @if ($scores[$dimension] == $i)
                                                    bg-orange-600 text-white
                                                @else
                                                    bg-gray-100 text-gray-700 hover:bg-gray-200
                                                @endif
                                            "
                                        >
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                @error('scores.' . $dimension) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endforeach
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes
                        </label>
                        <textarea
                            wire:model="notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Share your thoughts about this restaurant..."
                        ></textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t pt-6">
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition"
                        >
                            Submit Rating
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
