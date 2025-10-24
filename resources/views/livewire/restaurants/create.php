<?php

use App\Models\Restaurant;
use Livewire\Volt\Component;

class extends Component
{
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
        ]);

        $this->submitted = true;
    }
}
?>

<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('dashboard') }}" class="text-orange-600 hover:text-orange-700 mb-6 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Dashboard
        </a>

        <div class="bg-white rounded-lg shadow-lg p-8">
            @if ($submitted)
                <div class="text-center">
                    <div class="text-5xl mb-4">✨</div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Restaurant added!</h2>
                    <p class="text-gray-600 mb-6">Your new pizza restaurant is ready for ratings.</p>
                    <div class="space-x-4">
                        <a href="{{ route('dashboard') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                            Go to Dashboard
                        </a>
                        <button
                            wire:click="$reset"
                            class="inline-block px-6 py-3 bg-gray-100 text-gray-900 rounded-lg hover:bg-gray-200 transition"
                        >
                            Add Another
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        🍕 Add New Restaurant
                    </h1>
                    <p class="text-gray-600">Create a new pizza restaurant to track ratings</p>
                </div>

                <form wire:submit="submit" class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Restaurant Name *
                        </label>
                        <input
                            type="text"
                            wire:model="name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                            placeholder="e.g., Gino's Pizzeria"
                            required
                        />
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Location
                        </label>
                        <input
                            type="text"
                            wire:model="location"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                            placeholder="e.g., Downtown, 123 Main St"
                        />
                        @error('location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea
                            wire:model="notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Add any additional details about this restaurant..."
                        ></textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t pt-6">
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
