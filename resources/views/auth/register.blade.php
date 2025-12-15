<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('member_type', 'student') }}' }">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="member_type" :value="__('I am a...')" />
            <select id="member_type" name="member_type" x-model="role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
            <x-input-error :messages="$errors->get('member_type')" class="mt-2" />
        </div>

        <div x-show="role === 'student'" class="space-y-4 mt-4 transition-all" style="display: none;">
            
            <div>
                <x-input-label for="student_number" :value="__('Student Number')" />
                {{-- Updated placeholder to match the regex pattern --}}
                <x-text-input id="student_number" class="block mt-1 w-full" type="text" name="student_number" :value="old('student_number')" placeholder="e.g., AY2023-00123" />
                <x-input-error :messages="$errors->get('student_number')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="course_id" :value="__('Course / Strand')" />
                <select id="course_id" name="course_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="" disabled selected>Select your course...</option>
                    
                    @if(isset($courses))
                        @foreach($courses as $type => $group)
                            <optgroup label="{{ $type }}">
                                @foreach($group as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->acronym }} - {{ $course->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @endif
                </select>
                <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>