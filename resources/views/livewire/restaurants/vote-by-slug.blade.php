<?php

use App\Models\Rating;
use App\Models\Restaurant;
use Livewire\Volt\Component;

new class extends Component {
    public Restaurant $restaurant;
    public string $voter_name = '';
    public array $scores = ['taste' => 3, 'service' => 3, 'atmosphere' => 3, 'value' => 3];
    public string $notes = '';
    public ?string $visited_at = null;
    public bool $submitted = false;

    public function mount(string $slug)
    {
        $this->restaurant = Restaurant::where('slug', $slug)
            ->firstOr(function () {
                abort(404, 'Restaurant not found');
            });

        // Check if voting is enabled
        if (!$this->restaurant->voting_enabled) {
            abort(403, 'Voting is not currently enabled for this restaurant');
        }

        $this->visited_at = now()->format('Y-m-d');
    }

    public function submit()
    {
        $this->validate([
            'voter_name' => ['required', 'string', 'max:255'],
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
                'voter_name' => $this->voter_name,
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
        ];
    }
};
?>

<div class="min-h-screen bg-gradient-to-b from-orange-50 dark:from-zinc-900 to-white dark:to-zinc-800 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('home') }}" class="text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 mb-6 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Results
        </a>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg p-8">
            @if ($submitted)
                <div class="text-center">
                    <div class="text-5xl mb-4">✨</div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Thanks for voting!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Your ratings have been saved.</p>
                    <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                        Back to Results
                    </a>
                </div>
            @else
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        🍕 {{ $restaurant->name }}
                    </h1>
                    @if ($restaurant->location)
                        <p class="text-gray-600 dark:text-gray-300">📍 {{ $restaurant->location }}</p>
                    @endif
                </div>

                <form wire:submit="submit" class="space-y-8">
                    <!-- Name (required for voting via slug) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Your Name *
                        </label>
                        <input
                            type="text"
                            wire:model="voter_name"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Enter your name"
                            required
                        />
                        @error('voter_name') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Visit Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Visit Date *
                        </label>
                        <input
                            type="date"
                            wire:model="visited_at"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-orange-500 focus:border-orange-500"
                            required
                        />
                        @error('visited_at') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ratings -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Rate This Restaurant</h3>

                        @foreach ($dimensions as $dimension => $label)
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
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
                                                    bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-600
                                                @endif
                                            "
                                        >
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                @error('scores.' . $dimension) <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endforeach
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Additional Notes
                        </label>
                        <textarea
                            wire:model="notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Share your thoughts about this restaurant..."
                        ></textarea>
                        @error('notes') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
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
