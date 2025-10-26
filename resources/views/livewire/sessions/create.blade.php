<?php

use App\Models\Restaurant;
use App\Models\VotingSession;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component {
    public string $session_name = '';
    public string $session_description = '';
    public ?int $restaurant_id = null;
    public bool $submitted = false;
    public ?string $shareable_link = null;

    public function with(): array
    {
        $restaurants = Restaurant::all();

        return [
            'restaurants' => $restaurants,
        ];
    }

    public function submit()
    {
        $this->validate([
            'session_name' => ['required', 'string', 'max:255'],
            'session_description' => ['nullable', 'string', 'max:1000'],
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
        ]);

        $session = VotingSession::create([
            'user_id' => auth()->id(),
            'restaurant_id' => $this->restaurant_id,
            'name' => $this->session_name,
            'slug' => Str::slug($this->session_name) . '-' . Str::random(8),
            'description' => $this->session_description ?: null,
            'is_active' => true,
        ]);

        $this->shareable_link = route('restaurants.vote-by-session', [
            Restaurant::find($this->restaurant_id)->slug,
            $session->slug,
        ]);

        $this->submitted = true;
    }

    public function resetForm()
    {
        $this->session_name = '';
        $this->session_description = '';
        $this->restaurant_id = null;
        $this->submitted = false;
        $this->shareable_link = null;
    }
}; ?>

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
            @if ($submitted && $shareable_link)
                <div class="text-center">
                    <div class="text-5xl mb-4">🎉</div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Voting Session Created!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Share this link with your family to start voting.</p>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Shareable Link:</p>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                readonly
                                value="{{ $shareable_link }}"
                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white"
                            />
                            <button
                                type="button"
                                x-data="{ copied: false }"
                                @click="copied = true; navigator.clipboard.writeText('{{ $shareable_link }}'); setTimeout(() => copied = false, 2000)"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                            >
                                <span x-show="!copied">Copy</span>
                                <span x-show="copied">✓ Copied!</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-x-4">
                        <a href="{{ route('dashboard') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                            Back to Dashboard
                        </a>
                        <button
                            wire:click="resetForm"
                            class="inline-block px-6 py-3 bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-600 transition"
                        >
                            Create Another
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        🎯 New Voting Session
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">Create a private voting session to share with your family</p>
                </div>

                <form wire:submit="submit" class="space-y-6">
                    <!-- Session Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Session Name *
                        </label>
                        <input
                            type="text"
                            wire:model="session_name"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="e.g., Family Pizza Night Jan 25"
                            required
                        />
                        @error('session_name') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Restaurant Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Restaurant *
                        </label>
                        <select
                            wire:model="restaurant_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-orange-500 focus:border-orange-500"
                            required
                        >
                            <option value="">Select a restaurant...</option>
                            @foreach ($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                            @endforeach
                        </select>
                        @error('restaurant_id') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description (Optional)
                        </label>
                        <textarea
                            wire:model="session_description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Add any details about this voting session..."
                        ></textarea>
                        @error('session_description') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition"
                        >
                            Create Voting Session
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
