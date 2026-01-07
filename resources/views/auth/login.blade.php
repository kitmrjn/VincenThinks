<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-left mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
        <p class="text-gray-500 mt-2 text-sm">Please enter your details to sign in.</p>
    </div>

    {{-- x-data handles the toggling label text --}}
    <form method="POST" action="{{ route('login') }}" x-data="{ loginType: 'student' }">
        @csrf

        {{-- Role Selector --}}
        <div class="mb-4">
            <x-input-label for="login_type" :value="__('I am a...')" />
            <select id="login_type" name="login_type" x-model="loginType" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 rounded-lg shadow-sm bg-white text-gray-900">
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>

        <div>
            {{-- Dynamic Label based on selection --}}
            <x-input-label for="login" x-text="loginType === 'teacher' ? 'Email or Teacher Number' : 'Email or Student Number'" />
            <x-text-input id="login" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 rounded-lg" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 rounded-lg"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-maroon-700 shadow-sm focus:ring-maroon-700" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-maroon-700 font-semibold hover:text-maroon-900 hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="mt-8">
            <x-primary-button class="w-full justify-center py-3 bg-maroon-700 hover:bg-maroon-800 text-white font-bold rounded-lg transition-all text-base">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div class="mt-6 text-center text-sm text-gray-500">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-bold text-maroon-700 hover:text-maroon-900 hover:underline">Sign up</a>
        </div>
    </form>
</x-guest-layout>