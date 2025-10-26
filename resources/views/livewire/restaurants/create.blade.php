<?php

use App\Models\Restaurant;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $location = '';
    public string $notes = '';
    public bool $submitted = false;

    public function submit()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Restaurant::create([
            'name' => $this->name,
            'location' => $this->location ?: null,
            'notes' => $this->notes ?: null,
            'user_id' => auth()->id(),
        ]);

        $this->submitted = true;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->location = '';
        $this->notes = '';
        $this->submitted = false;
    }
};
?>

<div class="min-h-screen bg-gray-50 dark:bg-zinc-900 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('dashboard') }}" class="text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 mb-6 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Dashboard
        </a>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg p-8">
            @if ($submitted)
                <div class="text-center">
                    <div class="text-5xl mb-4">✨</div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Restaurant added!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Your new pizza restaurant is ready for ratings.</p>
                    <div class="space-x-4">
                        <a href="{{ route('dashboard') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                            Go to Dashboard
                        </a>
                        <button
                            wire:click="resetForm"
                            class="inline-block px-6 py-3 bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-600 transition"
                        >
                            Add Another
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        🍕 Add New Restaurant
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">Create a new pizza restaurant to track ratings</p>
                </div>

                <form wire:submit="submit" class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Restaurant Name *
                        </label>
                        <input
                            type="text"
                            wire:model="name"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="e.g., Gino's Pizzeria"
                            required
                        />
                        @error('name') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Location
                        </label>
                        <input
                            type="text"
                            wire:model="location"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="e.g., Downtown, 123 Main St"
                        />
                        @error('location') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea
                            wire:model="notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Add any additional details about this restaurant..."
                        ></textarea>
                        @error('notes') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition"
                        >
                            Create Restaurant
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
