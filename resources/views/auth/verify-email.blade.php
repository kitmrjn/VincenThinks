<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Identity verified! Thanks for signing up. Before getting started, could you verify your email address by entering the 6-digit code we just emailed to you?') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification code has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.verify') }}">
        @csrf
        
        <div>
            <x-input-label for="otp" :value="__('6-Digit Verification Code')" />
            <x-text-input 
                id="otp" 
                class="block mt-1 w-full text-center tracking-[0.5em] text-2xl font-bold py-3" 
                type="text" 
                name="otp" 
                required 
                autofocus 
                maxlength="6" 
                placeholder="000000" 
            />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 bg-maroon-700 hover:bg-maroon-800 text-white font-bold rounded-lg transition-all">
                {{ __('Verify Email') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500">
                {{ __('Resend Code') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>