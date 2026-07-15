<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Dark mode: set before paint to avoid a flash of the wrong theme -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100" x-data x-cloak>
        <div class="min-h-screen lg:flex">

            <!-- Motivating banner panel -->
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-600 to-indigo-900 text-white flex-col justify-between p-12">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, white 0, transparent 35%), radial-gradient(circle at 85% 75%, white 0, transparent 40%);"></div>

                <a href="{{ route('home') }}" class="relative flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg bg-white/15 backdrop-blur flex items-center justify-center font-bold">A</div>
                    <span class="font-semibold text-lg">{{ config('app.name') }}</span>
                </a>

                <div class="relative space-y-6 max-w-md">
                    <h1 class="text-4xl font-bold leading-tight">Stay connected to the network that shaped you.</h1>
                    <p class="text-primary-100 text-lg">Thousands of MBSTU graduates use this portal to find mentors, discover opportunities, and give back to the community that started it all.</p>

                    <ul class="space-y-3 pt-2">
                        <li class="flex items-center gap-3 text-primary-50">
                            <svg class="h-5 w-5 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            Reconnect with classmates and faculty
                        </li>
                        <li class="flex items-center gap-3 text-primary-50">
                            <svg class="h-5 w-5 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            Find mentors and job opportunities
                        </li>
                        <li class="flex items-center gap-3 text-primary-50">
                            <svg class="h-5 w-5 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            Give back through mentorship and donations
                        </li>
                    </ul>
                </div>

                <p class="relative text-sm text-primary-200">© {{ date('Y') }} {{ config('app.name') }}</p>
            </div>

            <!-- Form panel -->
            <div class="flex-1 flex flex-col justify-center px-6 py-10 sm:px-10 lg:px-16">
                <div class="w-full max-w-sm mx-auto">
                    <div class="flex items-center justify-between mb-8 lg:hidden">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-lg bg-primary-600 flex items-center justify-center text-white font-bold text-sm">A</div>
                            <span class="font-semibold text-sm">{{ config('app.name') }}</span>
                        </a>
                        <x-dark-mode-toggle />
                    </div>

                    <div class="hidden lg:flex justify-end mb-6">
                        <x-dark-mode-toggle />
                    </div>

                    {{ $slot }}

                    <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400 lg:hidden">
                        © {{ date('Y') }} {{ config('app.name') }}
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
