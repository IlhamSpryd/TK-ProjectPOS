<x-layouts.auth :title="__('Log in')">
    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
        @csrf

        <!-- Email Address -->
        <x-ui.input
            name="email"
            :label="__('Email address')"
            :value="old('email')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
            <x-ui.input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            />

            @if (Route::has('password.request'))
                <a class="absolute top-0 right-0 text-body-sm text-primary-600 hover:text-primary-700 font-medium" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mt-1">
            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded-sm border-neutral-300 text-primary-600 focus:ring-primary-500" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember" class="ml-2 block text-body-sm text-neutral-700">
                {{ __('Remember me') }}
            </label>
        </div>

        <div class="flex items-center justify-end mt-2">
            <x-ui.button variant="primary" type="submit" class="w-full" data-test="login-button" target="login.store">
                {{ __('Log in') }}
            </x-ui.button>
        </div>
    </form>
</x-layouts.auth>
