<x-guest-layout :title="'Create your account — '.config('app.name')">
    <div class="mb-8">
        <h1 class="text-2xl font-bold">Create your account</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Join the MBSTU alumni network in a couple of minutes.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Role -->
        <div>
            <x-input-label :value="__('I am a...')" />
            <div class="mt-2 grid grid-cols-2 gap-3">
                <label class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2 text-sm cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-500/10">
                    <input type="radio" name="role" value="alumni" class="text-primary-600 focus:ring-primary-500" @checked(old('role', 'alumni') === 'alumni') required>
                    Alumni
                </label>
                <label class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2 text-sm cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-500/10">
                    <input type="radio" name="role" value="student" class="text-primary-600 focus:ring-primary-500" @checked(old('role') === 'student') required>
                    Student
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="mt-6 w-full rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2.5 transition">
            {{ __('Create account') }}
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        Already registered?
        <a href="{{ route('login') }}" class="font-medium text-primary-600 dark:text-primary-400 hover:underline">Log in</a>
    </p>
</x-guest-layout>
