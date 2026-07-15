<x-guest-layout :title="'Log in — '.config('app.name')">
    <div class="mb-8">
        <h1 class="text-2xl font-bold">Welcome back</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Log in to reconnect with your MBSTU network.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        @if (Route::has('password.request'))
            <div class="flex justify-end mt-4">
                <a class="text-sm text-primary-600 dark:text-primary-400 hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        @endif

        <button type="submit" class="mt-6 w-full rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2.5 transition">
            {{ __('Log in') }}
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-medium text-primary-600 dark:text-primary-400 hover:underline">Sign up</a>
    </p>
</x-guest-layout>
