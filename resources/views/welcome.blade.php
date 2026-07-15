<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }} — Reconnect. Grow. Give Back.</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Dark mode: set before paint to avoid a flash of the wrong theme -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100" x-data x-cloak>

        <!-- Nav -->
        <header class="sticky top-0 z-30 bg-white/80 dark:bg-gray-950/80 backdrop-blur border-b border-gray-100 dark:border-gray-900">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-primary-600 flex items-center justify-center text-white font-bold text-sm">A</div>
                    <span class="font-semibold text-sm sm:text-base">{{ config('app.name') }}</span>
                </a>

                <div class="flex items-center gap-2 sm:gap-4">
                    <x-dark-mode-toggle />

                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2">
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 transition">
                            Join the network
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-primary-50 dark:from-primary-950/40 to-white dark:to-gray-950"></div>
            <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-primary-300/30 dark:bg-primary-600/10 blur-3xl -z-10"></div>
            <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-indigo-300/30 dark:bg-indigo-600/10 blur-3xl -z-10"></div>

            <div class="max-w-5xl mx-auto px-6 pt-20 pb-24 text-center">
                <span class="inline-flex items-center rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-xs font-semibold px-3 py-1">
                    MBSTU Alumni Association
                </span>

                <h1 class="mt-6 text-4xl sm:text-6xl font-extrabold tracking-tight leading-tight">
                    Your journey with MBSTU<br class="hidden sm:block"> doesn't end at graduation.
                </h1>

                <p class="mt-6 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Reconnect with classmates, find mentors who've walked your path, discover career opportunities, and give back to the community that shaped you — all in one place.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="w-full sm:w-auto rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-base font-semibold px-8 py-3 transition shadow-lg shadow-primary-600/20">
                            Create your account
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto rounded-lg bg-white dark:bg-gray-900 ring-1 ring-gray-900/10 dark:ring-white/10 text-gray-900 dark:text-white text-base font-semibold px-8 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            I already have an account
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-base font-semibold px-8 py-3 transition shadow-lg shadow-primary-600/20">
                            Go to your dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="max-w-5xl mx-auto px-6 -mt-6 pb-20">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <x-stat-card label="Verified Alumni" :value="number_format($stats['alumni'])" />
                <x-stat-card label="Events Hosted" :value="number_format($stats['events'])" />
                <x-stat-card label="Job Opportunities" :value="number_format($stats['jobs'])" />
                <x-stat-card label="Donations Raised" :value="'৳'.number_format((float) $stats['donations'])" />
            </div>
        </section>

        <!-- Features -->
        <section class="max-w-7xl mx-auto px-6 py-20">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="text-3xl font-bold">Everything your alumni community needs</h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400">One portal for staying in touch, growing your career, and supporting the next generation of MBSTU graduates.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ([
                    ['icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z', 'title' => 'Alumni Directory', 'body' => 'Search and reconnect with verified graduates by batch, department, or location.'],
                    ['icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5', 'title' => 'Events', 'body' => 'Reunions, webinars, and workshops — register in a click and never miss a gathering.'],
                    ['icon' => 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.653v-2.674a3.75 3.75 0 00-.615-2.052L18.75 4.5a2.25 2.25 0 00-1.87-1H7.12a2.25 2.25 0 00-1.87 1L2.865 7.771A3.75 3.75 0 002.25 9.823v2.674c0 .646.28 1.245.75 1.653m16.5 0a2.181 2.181 0 01-1.5.657H5.25a2.18 2.18 0 01-1.5-.657', 'title' => 'Job Portal', 'body' => 'Verified alumni post openings; students and graduates browse and bookmark them.'],
                    ['icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z', 'title' => 'Mentorship', 'body' => 'Students request guidance from verified alumni who\'ve been where they\'re headed.'],
                    ['icon' => 'M16.5 8.25V6a3.75 3.75 0 10-7.5 0v2.25m8.25 0h-9a1.5 1.5 0 00-1.5 1.5v9a1.5 1.5 0 001.5 1.5h9a1.5 1.5 0 001.5-1.5v-9a1.5 1.5 0 00-1.5-1.5z', 'title' => 'Notice Board', 'body' => 'Official announcements, scholarships, and circulars — bookmark what matters.'],
                    ['icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 21.03a.562.562 0 01-.84-.61l1.285-5.385a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z', 'title' => 'Success Stories', 'body' => 'Read how fellow graduates built their careers — and share your own story.'],
                    ['icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Donations', 'body' => 'Support campaigns that give back — every contribution earns a real receipt.'],
                    ['icon' => 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 22.5H6a2.25 2.25 0 01-2.25-2.25V3.75A2.25 2.25 0 016 1.5h8.379a2.25 2.25 0 011.591.659l4.121 4.121a2.25 2.25 0 01.659 1.591V20.25A2.25 2.25 0 0118 22.5z', 'title' => 'Gallery', 'body' => 'Photo albums from reunions, ceremonies, and campus life through the years.'],
                    ['icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-1.519-3.844L12 15.375l-1.481-1.969m0 0L9 15.375M3.75 19.5h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H10.5l-3-3H3.75A1.5 1.5 0 002.25 3v15a1.5 1.5 0 001.5 1.5z', 'title' => 'Documents', 'body' => 'Newsletters, annual reports, and forms in one categorized, secure archive.'],
                ] as $feature)
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-6 hover:ring-primary-500/50 transition">
                        <div class="h-11 w-11 rounded-xl bg-primary-100 dark:bg-primary-900/40 flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}" /></svg>
                        </div>
                        <h3 class="mt-4 font-semibold">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $feature['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- CTA banner -->
        @guest
            <section class="max-w-5xl mx-auto px-6 pb-24">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-indigo-900 text-white px-8 py-16 text-center">
                    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 15% 30%, white 0, transparent 30%), radial-gradient(circle at 85% 70%, white 0, transparent 35%);"></div>
                    <h2 class="relative text-3xl font-bold">Ready to reconnect?</h2>
                    <p class="relative mt-3 text-primary-100 max-w-xl mx-auto">Join thousands of MBSTU alumni, students, and faculty already using the portal.</p>
                    <a href="{{ route('register') }}" class="relative mt-8 inline-flex items-center rounded-lg bg-white text-primary-700 text-base font-semibold px-8 py-3 hover:bg-primary-50 transition">
                        Get started — it's free
                    </a>
                </div>
            </section>
        @endguest

        <!-- Footer -->
        <footer class="border-t border-gray-100 dark:border-gray-900">
            <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="h-7 w-7 rounded-lg bg-primary-600 flex items-center justify-center text-white font-bold text-xs">A</div>
                    <span class="text-sm font-medium">{{ config('app.name') }}</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>
