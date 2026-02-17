<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-left mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
        <p class="text-gray-500 mt-2 text-sm">Please enter your details to sign in.</p>
    </div>

    {{-- 
        x-data: Combined your 'loginType' logic with the Validation logic.
        - validateLogin(): Checks if the input is a valid Email OR a valid ID (AY format).
    --}}
    <form method="POST" action="{{ route('login') }}" 
          x-data="{ 
              loginType: 'student',
              login: '{{ old('login') }}',
              password: '',
              isLoginValid: null,
              isPasswordFilled: false,

              validateLogin() {
                  if (!this.login) { this.isLoginValid = null; return; }

                  // Regex 1: Standard Email
                  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                  
                  // Regex 2: ID Format (AYxxxx-xxxxx) - Case Insensitive
                  const idRegex = /^AY\d{4}-\d{5}$/i;

                  // Valid if it matches EITHER Email OR ID
                  this.isLoginValid = emailRegex.test(this.login) || idRegex.test(this.login);
              },

              checkPassword() {
                  this.isPasswordFilled = this.password.length > 0;
              },

              get isFormValid() {
                  return this.isLoginValid === true && this.isPasswordFilled === true;
              }
          }"
          x-init="validateLogin(); $watch('password', () => checkPassword())"
    >
        @csrf

        {{-- Role Selector (KEPT EXACTLY AS REQUIRED) --}}
        <div class="mb-4">
            <x-input-label for="login_type" :value="__('I am a...')" />
            <select id="login_type" name="login_type" x-model="loginType" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 rounded-lg shadow-sm bg-white text-gray-900">
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>

        {{-- Login Field (Email or ID) --}}
        <div>
            {{-- Dynamic Label --}}
            <x-input-label for="login" x-text="loginType === 'teacher' ? 'Email or Teacher Number' : 'Email or Student Number'" />
            
            <div class="relative mt-1">
                <x-text-input 
                    id="login" 
                    class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2 transition-colors duration-200 outline-none"
                    x-bind:class="{
                        'border-gray-300 focus:border-maroon-700 focus:ring-maroon-700': isLoginValid === null,
                        'border-green-500 text-green-700 focus:border-green-500 focus:ring-green-500': isLoginValid === true,
                        'border-red-500 text-red-700 focus:border-red-500 focus:ring-red-500': isLoginValid === false
                    }"
                    type="text" 
                    name="login" 
                    x-model="login" 
                    @input="validateLogin()" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />

                {{-- Status Icons --}}
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i x-show="isLoginValid === null" class='bx bx-user text-gray-400 text-xl'></i>
                    <i x-show="isLoginValid === true" class='bx bx-check text-green-600 text-2xl animate-bounce' style="animation-iteration-count: 1;"></i>
                    <i x-show="isLoginValid === false" class='bx bx-x text-red-600 text-2xl animate-pulse'></i>
                </div>
            </div>

            {{-- Validation Error Message --}}
            <p x-show="isLoginValid === false" class="text-red-500 text-xs mt-1" style="display: none;">
                Must be a valid Email or ID (AYxxxx-xxxxx).
            </p>
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        {{-- Password Field --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <x-text-input 
                    id="password" 
                    class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2 border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 transition-colors duration-200 outline-none"
                    type="password" 
                    name="password" 
                    x-model="password" 
                    @input="checkPassword()" 
                    required 
                    autocomplete="current-password" 
                />
                {{-- Lock Icon --}}
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class='bx bx-lock-alt text-gray-400 text-xl'></i>
                </div>
            </div>
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

        {{-- Submit Button (Disabled until valid) --}}
        <div class="mt-8">
            <button 
                type="submit"
                x-bind:disabled="!isFormValid"
                class="w-full justify-center py-3 text-white font-bold rounded-lg transition-all text-base"
                :class="{
                    'bg-maroon-700 hover:bg-maroon-800 cursor-pointer': isFormValid,
                    'bg-gray-400 cursor-not-allowed opacity-50': !isFormValid
                }"
            >
                {{ __('Log in') }}
            </button>
        </div>

        <div class="mt-6 text-center text-sm text-gray-500">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-bold text-maroon-700 hover:text-maroon-900 hover:underline">Sign up</a>
        </div>
    </form>
</x-guest-layout>