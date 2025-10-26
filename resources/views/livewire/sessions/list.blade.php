<?php

use App\Models\VotingSession;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $sessions = auth()->user()?->votingSessions()
            ->with('restaurant')
            ->orderBy('created_at', 'desc')
            ->get() ?? collect();

        return [
            'sessions' => $sessions,
        ];
    }

    public function toggleSession(VotingSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $session->is_active = !$session->is_active;
        $session->save();
    }
}; ?>

<div class="min-h-screen bg-gray-50 dark:bg-zinc-900 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('dashboard') }}" class="text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 mb-6 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        🎯 Voting Sessions
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">Manage your family voting sessions</p>
                </div>
                <a href="{{ route('sessions.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    + New Session
                </a>
            </div>
        </div>

        <!-- Sessions List -->
        @if ($sessions->count() > 0)
            <div class="space-y-4">
                @foreach ($sessions as $session)
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $session->name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    🍕 {{ $session->restaurant->name }}
                                </p>
                                @if ($session->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $session->description }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($session->is_active)
                                    <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-semibold rounded">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-400 text-xs font-semibold rounded">
                                        Closed
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between mb-4 text-sm text-gray-600 dark:text-gray-400">
                            <span>{{ intval($session->ratings->count() / 4) }} {{ Str::plural('submission', intval($session->ratings->count() / 4)) }}</span>
                            <span>{{ $session->created_at->format('M d, Y') }}</span>
                        </div>

                        <div class="flex gap-2 flex-wrap">
                            @if ($session->is_active)
                                <button
                                    type="button"
                                    x-data="{ copied: false }"
                                    @click="copied = true; navigator.clipboard.writeText('{{ route('restaurants.vote-by-session', [$session->restaurant->slug, $session->slug]) }}'); setTimeout(() => copied = false, 2000)"
                                    class="px-4 py-2 text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 rounded hover:bg-blue-200 dark:hover:bg-blue-800 transition"
                                >
                                    <span x-show="!copied">📋 Copy Link</span>
                                    <span x-show="copied">✓ Copied!</span>
                                </button>
                            @endif

                            <a
                                href="{{ route('restaurants.show', $session->restaurant) }}"
                                class="px-4 py-2 text-sm bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300 rounded hover:bg-orange-200 dark:hover:bg-orange-800 transition"
                            >
                                📊 View Results
                            </a>

                            <button
                                wire:click="toggleSession({{ $session->id }})"
                                class="px-4 py-2 text-sm @if ($session->is_active) bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800 @else bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 @endif rounded transition"
                            >
                                @if ($session->is_active)
                                    🔒 Close Session
                                @else
                                    🔓 Reopen Session
                                @endif
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-12 text-center">
                <div class="text-5xl mb-4">📝</div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No voting sessions yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Create your first voting session to start collecting family ratings</p>
                <a href="{{ route('sessions.create') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Create Voting Session
                </a>
            </div>
        @endif
    </div>
</div>
