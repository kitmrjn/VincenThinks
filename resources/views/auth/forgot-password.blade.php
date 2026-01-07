<x-guest-layout>
    <div class="mb-4">
        <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-maroon-700 flex items-center mb-6 transition">
            <i class='bx bx-arrow-back mr-1'></i> Back to Login
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h2>
        <div class="text-sm text-gray-600 leading-relaxed">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 rounded-lg" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 bg-maroon-700 hover:bg-maroon-800 text-white font-bold rounded-lg transition-all">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>