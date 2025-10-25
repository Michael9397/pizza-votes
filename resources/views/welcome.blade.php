<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val)); $watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark'))" :class="darkMode && 'dark'">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pizza Votes - Family Pizza Ratings</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script>
            // Check for dark mode preference on page load
            if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>
    </head>
    <body class="bg-white dark:bg-zinc-900 text-gray-900 dark:text-white antialiased">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav class="border-b border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🍕</span>
                        <h1 class="text-xl font-bold">Pizza Votes</h1>
                    </div>
                    <div class="flex gap-4 items-center">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 transition">
                            <span x-show="!darkMode" class="text-lg">🌙</span>
                            <span x-show="darkMode" class="text-lg">☀️</span>
                        </button>

                        @auth
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                                    Log out
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                                    Sign up
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <section class="flex-grow">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <!-- Left Content -->
                        <div class="space-y-6">
                            <h2 class="text-4xl sm:text-5xl font-bold">
                                Rate Pizza with Your Family
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-400">
                                Track your favorite pizza places, share votes with family members, and find the best pizza together. Simple, fun, and completely family-friendly.
                            </p>
                            <div class="flex gap-4 flex-wrap pt-4">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
                                        Go to Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
                                        Get Started
                                    </a>
                                    <a href="{{ route('login') }}" class="px-8 py-3 border-2 border-orange-600 text-orange-600 dark:text-orange-400 dark:border-orange-400 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 transition font-semibold">
                                        Log In
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <!-- Right Emoji Grid -->
                        <div class="grid grid-cols-2 gap-6">
                            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-8 flex items-center justify-center">
                                <span class="text-6xl">🍕</span>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-8 flex items-center justify-center">
                                <span class="text-6xl">⭐</span>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-8 flex items-center justify-center">
                                <span class="text-6xl">👨‍👩‍👧‍👦</span>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-8 flex items-center justify-center">
                                <span class="text-6xl">🎯</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="bg-gray-50 dark:bg-zinc-800 border-y border-gray-200 dark:border-zinc-700">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                        <h3 class="text-3xl font-bold text-center mb-12">Key Features</h3>
                        <div class="grid md:grid-cols-3 gap-8">
                            <!-- Feature 1 -->
                            <div class="bg-white dark:bg-zinc-900 rounded-lg p-8 border border-gray-200 dark:border-zinc-700">
                                <div class="text-4xl mb-4">📍</div>
                                <h4 class="text-xl font-semibold mb-3">Restaurant Tracking</h4>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Build your pizza place collection with location details and notes about what makes each place special.
                                </p>
                            </div>

                            <!-- Feature 2 -->
                            <div class="bg-white dark:bg-zinc-900 rounded-lg p-8 border border-gray-200 dark:border-zinc-700">
                                <div class="text-4xl mb-4">🗳️</div>
                                <h4 class="text-xl font-semibold mb-3">Easy Voting</h4>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Rate restaurants on multiple dimensions: taste, service, atmosphere, and value. Simple 1-5 star ratings.
                                </p>
                            </div>

                            <!-- Feature 3 -->
                            <div class="bg-white dark:bg-zinc-900 rounded-lg p-8 border border-gray-200 dark:border-zinc-700">
                                <div class="text-4xl mb-4">🔗</div>
                                <h4 class="text-xl font-semibold mb-3">Shareable Links</h4>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Generate voting links to share with family at restaurants. Perfect for collecting votes from everyone in real-time.
                                </p>
                            </div>

                            <!-- Feature 4 -->
                            <div class="bg-white dark:bg-zinc-900 rounded-lg p-8 border border-gray-200 dark:border-zinc-700">
                                <div class="text-4xl mb-4">📊</div>
                                <h4 class="text-xl font-semibold mb-3">Analytics</h4>
                                <p class="text-gray-600 dark:text-gray-400">
                                    View detailed ratings and trends for each restaurant. See what your family values most in a pizza place.
                                </p>
                            </div>

                            <!-- Feature 5 -->
                            <div class="bg-white dark:bg-zinc-900 rounded-lg p-8 border border-gray-200 dark:border-zinc-700">
                                <div class="text-4xl mb-4">📱</div>
                                <h4 class="text-xl font-semibold mb-3">Mobile Friendly</h4>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Perfect for use on your phone at the restaurant. Responsive design works great on any device.
                                </p>
                            </div>

                            <!-- Feature 6 -->
                            <div class="bg-white dark:bg-zinc-900 rounded-lg p-8 border border-gray-200 dark:border-zinc-700">
                                <div class="text-4xl mb-4">🌙</div>
                                <h4 class="text-xl font-semibold mb-3">Dark Mode</h4>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Comfortable viewing in any lighting. Built-in dark mode support throughout the app.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-12 text-center border border-orange-200 dark:border-orange-800">
                        <h3 class="text-3xl font-bold mb-4">Ready to Start Rating?</h3>
                        <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto">
                            Join your family in the quest to find the perfect pizza. Start tracking, voting, and discovering together!
                        </p>
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-block px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
                                Create Account Free
                            </a>
                        @endauth
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="border-t border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 mt-12">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex flex-col sm:flex-row justify-between items-center text-gray-600 dark:text-gray-400 text-sm">
                        <div class="flex items-center gap-2 mb-4 sm:mb-0">
                            <span class="text-xl">🍕</span>
                            <span>Pizza Votes © 2025</span>
                        </div>
                        <div class="flex gap-6">
                            <a href="#" class="hover:text-gray-900 dark:hover:text-white transition">About</a>
                            <a href="#" class="hover:text-gray-900 dark:hover:text-white transition">Privacy</a>
                            <a href="#" class="hover:text-gray-900 dark:hover:text-white transition">Terms</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
