<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- DYNAMIC FIELDS --}}
        
        {{-- FOR STUDENTS --}}
        @if($user->member_type === 'student')
            <div>
                <x-input-label for="student_number" :value="__('Student Number')" />
                <x-text-input id="student_number" name="student_number" type="text" class="mt-1 block w-full" :value="old('student_number', $user->student_number)" placeholder="e.g. AY2023-00123" />
                <x-input-error class="mt-2" :messages="$errors->get('student_number')" />
            </div>

            <div>
                <x-input-label for="course_id" :value="__('Course / Strand')" />
                <select id="course_id" name="course_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="" disabled {{ !$user->course_id ? 'selected' : '' }}>Select your course...</option>
                    @php $courses = \App\Models\Course::all()->groupBy('type'); @endphp
                    @foreach($courses as $type => $group)
                        <optgroup label="{{ $type }}">
                            @foreach($group as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $user->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->acronym }} - {{ $course->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('course_id')" />
            </div>
        @endif

        {{-- FOR TEACHERS (EXACT MATCH TO ADMIN PANEL) --}}
        @if($user->member_type === 'teacher')
            <div>
                <x-input-label for="teacher_number" :value="__('Teacher Number')" />
                <x-text-input id="teacher_number" name="teacher_number" type="text" class="mt-1 block w-full" :value="old('teacher_number', $user->teacher_number)" placeholder="e.g. AY2023-00123" />
                <x-input-error class="mt-2" :messages="$errors->get('teacher_number')" />
            </div>

            <div>
                <x-input-label for="department_id" :value="__('Department / Faculty')" />
                {{-- Dynamic Dropdown from Database --}}
                <select id="department_id" name="department_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="" disabled {{ !$user->department_id ? 'selected' : '' }}>Select Department...</option>
                    
                    @if(isset($departments))
                        @foreach($departments as $dept)
                            {{-- This line makes it identical to Admin Panel: Name (Acronym) --}}
                            <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->acronym }})
                            </option>
                        @endforeach
                    @endif
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('department_id')" />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>