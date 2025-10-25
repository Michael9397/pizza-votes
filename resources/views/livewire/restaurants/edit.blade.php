<?php

use App\Models\Restaurant;
use Livewire\Volt\Component;

new class extends Component {
    public Restaurant $restaurant;
    public string $name = '';
    public string $location = '';
    public string $notes = '';
    public bool $voting_enabled = false;
    public bool $showDeleteConfirm = false;
    public bool $updated = false;

    public function mount()
    {
        $this->name = $this->restaurant->name;
        $this->location = $this->restaurant->location ?? '';
        $this->notes = $this->restaurant->notes ?? '';
        $this->voting_enabled = $this->restaurant->voting_enabled;
    }

    public function submit()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'voting_enabled' => ['boolean'],
        ]);

        $this->restaurant->update([
            'name' => $this->name,
            'location' => $this->location ?: null,
            'notes' => $this->notes ?: null,
            'voting_enabled' => $this->voting_enabled,
        ]);

        $this->updated = true;
    }

    public function delete()
    {
        $this->restaurant->delete();
        redirect()->route('dashboard');
    }

    public function toggleVoting()
    {
        $this->voting_enabled = !$this->voting_enabled;
    }

    public function with(): array
    {
        return [
            'voting_link' => $this->voting_enabled ? route('restaurants.vote-by-slug', $this->restaurant->slug) : null,
        ];
    }
};
?>

<div class="min-h-screen bg-gray-50 dark:bg-zinc-900 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('restaurants.show', $restaurant) }}" class="text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 mb-6 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Restaurant
        </a>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg p-8">
            @if ($updated)
                <div class="text-center">
                    <div class="text-5xl mb-4">✨</div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Updated!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Your restaurant details have been saved.</p>
                    <a href="{{ route('restaurants.show', $restaurant) }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                        Back to Restaurant
                    </a>
                </div>
            @elseif ($showDeleteConfirm)
                <div class="text-center">
                    <div class="text-5xl mb-4">⚠️</div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Delete Restaurant?</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">This action cannot be undone. All ratings will also be deleted.</p>
                    <div class="space-x-4">
                        <button
                            wire:click="delete"
                            class="inline-block px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
                        >
                            Yes, Delete
                        </button>
                        <button
                            wire:click="$set('showDeleteConfirm', false)"
                            class="inline-block px-6 py-3 bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-600 transition font-medium"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        🍕 Edit Restaurant
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">Update the restaurant details</p>
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-orange-500 focus:border-orange-500"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-orange-500 focus:border-orange-500"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-orange-500 focus:border-orange-500"
                        ></textarea>
                        @error('notes') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Voting Section -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Enable Voting Link</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Allow guests to vote using a shareable link</p>
                            </div>
                            <button
                                type="button"
                                wire:click="toggleVoting"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                    @if ($voting_enabled)
                                        bg-orange-600
                                    @else
                                        bg-gray-300 dark:bg-zinc-600
                                    @endif
                                "
                            >
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                        @if ($voting_enabled)
                                            translate-x-6
                                        @else
                                            translate-x-1
                                        @endif
                                    "
                                ></span>
                            </button>
                        </div>

                        @if ($voting_link)
                            <div class="mt-4 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Voting Link</p>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="text"
                                        value="{{ $voting_link }}"
                                        readonly
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-gray-900 dark:text-white text-sm"
                                    />
                                    <button
                                        type="button"
                                        @click="navigator.clipboard.writeText('{{ $voting_link }}'); alert('Link copied to clipboard!')"
                                        class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 transition font-medium text-sm"
                                    >
                                        Copy
                                    </button>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Share this link with people at the restaurant to collect their votes.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Buttons -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6 space-y-4">
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition"
                        >
                            Save Changes
                        </button>

                        <button
                            type="button"
                            wire:click="$set('showDeleteConfirm', true)"
                            class="w-full px-6 py-3 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 font-semibold rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition"
                        >
                            Delete Restaurant
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
