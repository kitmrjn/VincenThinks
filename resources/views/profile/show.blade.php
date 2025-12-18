<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} - Profile</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script>
        // Force Light Mode configuration to prevent black inputs
        tailwind.config = { 
            darkMode: 'class', 
            theme: { 
                extend: { 
                    colors: { 
                        maroon: { 50: '#fdf2f2', 100: '#fde8e8', 200: '#fbd5d5', 300: '#f8b4b4', 400: '#f98080', 500: '#f05252', 600: '#e02424', 700: '#800000', 800: '#600000', 900: '#400000' } 
                    } 
                } 
            } 
        }
    </script>
    <style>
        .prose p { margin-top: 0; margin-bottom: 0.5em; }
        .prose img { margin-top: 0.5em; margin-bottom: 0.5em; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal min-h-screen flex flex-col text-gray-900">

    @include('partials.navbar')

    <div class="max-w-6xl mx-auto w-full mt-8 px-4 pb-12 flex-grow">
        
        @if(session('status') === 'profile-updated')
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="fixed bottom-5 right-5 bg-maroon-700 text-white px-6 py-3 rounded-lg shadow-lg flex items-center z-50 transition-opacity duration-500">
                <i class='bx bx-check-circle text-2xl mr-2'></i> Profile Updated Successfully!
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 mb-6 shadow-sm flex items-center">
                <i class='bx bx-check-circle text-xl mr-2'></i> {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">

            {{-- LEFT SIDEBAR --}}
            <div class="w-full lg:w-1/3 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
                    
                    {{-- Avatar Section --}}
                    <div class="text-center mb-6" x-data="{ avatarPreview: '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}' }">
                        <div class="relative inline-block">
                            @if($user->avatar)
                                <img :src="avatarPreview" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md mx-auto" alt="{{ $user->name }}">
                            @else
                                <div x-show="!avatarPreview" class="w-32 h-32 rounded-full bg-gray-100 border-4 border-white flex items-center justify-center text-maroon-700 text-4xl font-bold shadow-md mx-auto">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <img x-show="avatarPreview" :src="avatarPreview" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md mx-auto" style="display:none;">
                            @endif
                            
                            @auth
                                @if(Auth::id() === $user->id)
                                    <form action="{{ route('user.avatar.update') }}" method="POST" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                                        @csrf
                                        <label for="avatar_upload" class="bg-maroon-700 text-white rounded-full p-2 cursor-pointer hover:bg-maroon-800 shadow-sm border-2 border-white flex items-center justify-center w-8 h-8 transition-transform hover:scale-110" title="Change Picture">
                                            <i class='bx bx-camera text-xs'></i>
                                        </label>
                                        <input type="file" name="avatar" id="avatar_upload" class="hidden" onchange="this.form.submit()">
                                    </form>
                                @endif
                            @endauth
                        </div>

                        <h1 class="text-2xl font-bold text-gray-900 mt-4">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500 mt-1 flex items-center justify-center">
                            <i class='bx bx-calendar mr-1'></i> Joined {{ $user->created_at->format('M Y') }}
                        </p>
                        
                        {{-- Badges --}}
                        <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                            @if($user->is_admin)
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-maroon-100 text-maroon-800 border border-maroon-200"><i class='bx bxs-shield-alt-2 mr-1'></i> Administrator</span>
                            @endif
                            
                            @if ($user->hasVerifiedEmail())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200"><i class='bx bx-check-circle mr-1'></i> Verified</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200"><i class='bx bx-time mr-1'></i> Unverified</span>
                            @endif
                        </div>

                        {{-- Academic Badge --}}
                        <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                            @if($user->member_type === 'teacher')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200"><i class='bx bxs-briefcase-alt-2 mr-1'></i> Teacher</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200"><i class='bx bxs-school mr-1'></i> Student</span>
                            @endif

                            @if($user->member_type === 'student' && $user->course)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border cursor-help {{ $user->course->type == 'College' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($user->course->type == 'SHS' ? 'bg-orange-50 text-orange-700 border-orange-200' : ($user->course->type == 'JHS' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-purple-50 text-purple-700 border-purple-200')) }}" title="{{ $user->course->name }}">{{ $user->course->acronym }}</span>
                            @elseif($user->member_type === 'teacher' && $user->departmentInfo)
                                 {{-- FIXED: Use departmentInfo relationship for display --}}
                                 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $user->departmentInfo->name }}
                                 </span>
                            @endif
                        </div>

                        {{-- ID Number --}}
                        @if($user->member_type === 'student' && $user->student_number)
                            <div class="mt-2 text-xs text-gray-500 font-mono bg-gray-50 inline-block px-2 py-1 rounded border border-gray-200">ID: {{ $user->student_number }}</div>
                        @elseif($user->member_type === 'teacher' && $user->teacher_number)
                            <div class="mt-2 text-xs text-gray-500 font-mono bg-gray-50 inline-block px-2 py-1 rounded border border-gray-200">ID: {{ $user->teacher_number }}</div>
                        @endif
                    </div>

                    <hr class="border-gray-100 my-6">

                    {{-- Stats --}}
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                            <span class="block text-2xl font-bold text-green-700">{{ $solvedCount }}</span>
                            <span class="text-xs text-green-600 font-medium uppercase tracking-wide">Solutions</span>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                            <span class="block text-2xl font-bold text-yellow-700">{{ $topRatedCount }}</span>
                            <span class="text-xs text-yellow-600 font-medium uppercase tracking-wide">Top Rated</span>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between text-sm text-gray-600 px-4">
                        <div class="flex flex-col items-center"><span class="font-bold text-gray-900 text-lg">{{ $user->questions->count() }}</span><span>Questions</span></div>
                        <div class="h-10 w-px bg-gray-200"></div>
                        <div class="flex flex-col items-center"><span class="font-bold text-gray-900 text-lg">{{ $user->answers->count() }}</span><span>Answers</span></div>
                    </div>
                </div>
            </div>

            {{-- RIGHT CONTENT --}}
            <div class="w-full lg:w-2/3" 
                x-data="{ activeTab: '{{ request('tab') ? request('tab') : (request('answers_page') ? 'answers' : 'questions') }}' }"
                x-on:switch-tab.window="activeTab = $event.detail">
                
                {{-- Tabs Navigation --}}
                <div class="flex border-b border-gray-200 mb-6 overflow-x-auto bg-white rounded-t-xl px-2">
                    <button @click="activeTab = 'questions'" class="px-6 py-4 text-sm font-bold transition whitespace-nowrap border-b-2" :class="activeTab === 'questions' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                        <i class='bx bx-question-mark mr-1 text-lg align-middle'></i> Questions
                    </button>
                    <button @click="activeTab = 'answers'" class="px-6 py-4 text-sm font-bold transition whitespace-nowrap border-b-2" :class="activeTab === 'answers' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                        <i class='bx bx-message-square-dots mr-1 text-lg align-middle'></i> Answers
                    </button>
                    @auth
                        @if(Auth::id() === $user->id)
                            <button @click="activeTab = 'settings'" class="px-6 py-4 text-sm font-bold transition whitespace-nowrap border-b-2" :class="activeTab === 'settings' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                                <i class='bx bx-cog mr-1 text-lg align-middle'></i> Settings
                            </button>
                        @endif
                    @endauth
                </div>

                {{-- QUESTIONS TAB --}}
                <div x-show="activeTab === 'questions'" class="space-y-4" x-cloak>
                    @forelse($questions_list as $q)
                        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition duration-200 hover:border-maroon-200 group relative">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-4">
                                    @if($q->user->avatar)
                                        <img src="{{ asset('storage/' . $q->user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 text-sm font-bold">{{ substr($q->user->name, 0, 1) }}</div>
                                    @endif
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center text-xs text-gray-400 mb-1 font-light">
                                            <span class="font-medium text-gray-600 mr-2">{{ $q->user->name }}</span>
                                            <span class="mx-1">•</span>
                                            <span>{{ $q->created_at->diffForHumans() }}</span>
                                            @if($q->category) <span class="mx-2 text-gray-300">|</span> <span class="bg-gray-100 text-maroon-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-gray-200">{{ $q->category->name }}</span> @endif
                                        </div>
                                        @if($q->best_answer_id) <span class="bg-green-100 text-green-700 border border-green-200 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide flex items-center whitespace-nowrap ml-2"><i class='bx bx-check mr-1'></i> Solved</span> @endif
                                    </div>
                                    <a href="{{ route('question.show', $q->id) }}" class="block group-hover:text-maroon-700 transition-colors mb-2">
                                        <h3 class="text-lg font-normal text-gray-800 leading-tight">{{ $q->title }}</h3>
                                    </a>
                                    <div class="prose prose-sm prose-stone text-gray-500 line-clamp-3">{!! $q->content !!}</div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-400 font-light">
                                            <span class="flex items-center"><i class='bx bx-message-alt mr-1 text-base'></i> {{ $q->answers->count() }} Answers</span>
                                            <span class="flex items-center"><i class='bx bx-show mr-1 text-lg'></i> {{ $q->views ?? 0 }} Views</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300"><i class='bx bx-question-mark text-4xl text-gray-300 mb-2'></i><p class="text-gray-500 text-sm">No questions asked yet.</p></div>
                    @endforelse
                    <div class="mt-4">{{ $questions_list->appends(request()->except('questions_page'))->links('partials.pagination') }}</div>
                </div>

                {{-- ANSWERS TAB --}}
                <div x-show="activeTab === 'answers'" class="space-y-4" x-cloak>
                    @forelse($answers_list as $answer)
                        @php
                            $myScore = $answer->ratings->avg('score') ?? 0;
                            $isTopRated = false;
                            $isBest = $answer->question && $answer->id === $answer->question->best_answer_id;
                            if ($myScore > 0 && $answer->question) {
                                $isTopRated = true;
                                foreach ($answer->question->answers as $otherAnswer) {
                                    if ($otherAnswer->id !== $answer->id) {
                                        $otherScore = $otherAnswer->ratings->avg('score') ?? 0;
                                        if ($otherScore > $myScore) { $isTopRated = false; break; }
                                    }
                                }
                            }
                        @endphp
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover:border-maroon-300 transition {{ $isBest ? 'border-l-4 border-l-green-500' : '' }}">
                            <div class="flex justify-between items-start mb-3">
                                <div class="text-xs text-gray-500 font-light pr-4">
                                    <span>Answered on:</span>
                                    <a href="{{ route('question.show', $answer->question->id) }}" class="text-gray-700 hover:text-maroon-700 hover:underline font-bold block mt-0.5 text-sm leading-tight">{{ Str::limit($answer->question->title, 70) }}</a>
                                </div>
                                <div class="flex flex-col items-end flex-shrink-0 gap-1">
                                    <span class="text-[10px] text-gray-400 font-light">{{ $answer->created_at->diffForHumans() }}</span>
                                    <div class="flex gap-1 justify-end flex-wrap">
                                        @if($isBest) <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded border border-green-200 flex items-center"><i class='bx bx-check mr-1'></i> Solution</span> @endif
                                        @if($isTopRated) <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded border border-yellow-200 flex items-center"><i class='bx bxs-trophy mr-1'></i> Top Rated</span> @endif
                                    </div>
                                </div>
                            </div>
                            <div class="prose prose-sm prose-stone text-gray-600 mb-4 line-clamp-3">{!! $answer->content !!}</div>
                            <div class="flex items-center">
                                <div class="flex items-center text-xs text-yellow-600 font-bold bg-white border border-yellow-200 inline-block px-2 py-1 rounded-full shadow-sm"><i class='bx bxs-star mr-1 text-yellow-400'></i> {{ number_format($myScore, 1) }} Rating</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300"><i class='bx bx-message-square-dots text-4xl text-gray-300 mb-2'></i><p class="text-gray-500 text-sm">No answers posted yet.</p></div>
                    @endforelse
                    <div class="mt-4">{{ $answers_list->appends(request()->except('answers_page'))->links('partials.pagination') }}</div>
                </div>

                {{-- SETTINGS TAB --}}
                @auth
                    @if(Auth::id() === $user->id)
                        <div x-show="activeTab === 'settings'" class="space-y-6" x-cloak>
                            
                            {{-- 1. Profile Info --}}
                            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border border-gray-100">
                                <div class="max-w-xl">
                                    <section>
                                        <header>
                                            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
                                            <p class="mt-1 text-sm text-gray-600">Update your account's profile information and academic details.</p>
                                        </header>

                                        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                                            @csrf @method('patch')

                                            <div>
                                                <x-input-label for="name" :value="__('Name')" />
                                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-maroon-700 focus:ring-maroon-700 py-3 px-4 bg-white text-gray-900" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                            </div>

                                            <div>
                                                <x-input-label for="email" :value="__('Email')" />
                                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-maroon-700 focus:ring-maroon-700 py-3 px-4 bg-white text-gray-900" :value="old('email', $user->email)" required autocomplete="username" />
                                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                            </div>

                                            @if($user->member_type === 'student')
                                                <div>
                                                    <x-input-label for="student_number" :value="__('Student Number')" />
                                                    <x-text-input id="student_number" name="student_number" type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-maroon-700 focus:ring-maroon-700 py-3 px-4 bg-white text-gray-900" :value="old('student_number', $user->student_number)" placeholder="e.g. AY2023-00123" />
                                                    <x-input-error class="mt-2" :messages="$errors->get('student_number')" />
                                                </div>
                                                <div>
                                                    <x-input-label for="course_id" :value="__('Course / Strand')" />
                                                    <select id="course_id" name="course_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-maroon-700 focus:ring-maroon-700 py-3 px-4 bg-white text-gray-900">
                                                        <option value="" disabled {{ !$user->course_id ? 'selected' : '' }}>Select your course...</option>
                                                        @foreach(\App\Models\Course::all()->groupBy('type') as $type => $group)
                                                            <optgroup label="{{ $type }}">
                                                                @foreach($group as $course)
                                                                    <option value="{{ $course->id }}" {{ old('course_id', $user->course_id) == $course->id ? 'selected' : '' }}>{{ $course->acronym }} - {{ $course->name }}</option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                    <x-input-error class="mt-2" :messages="$errors->get('course_id')" />
                                                </div>
                                            @elseif($user->member_type === 'teacher')
                                                <div>
                                                    <x-input-label for="teacher_number" :value="__('Teacher Number')" />
                                                    <x-text-input id="teacher_number" name="teacher_number" type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-maroon-700 focus:ring-maroon-700 py-3 px-4 bg-white text-gray-900" :value="old('teacher_number', $user->teacher_number)" placeholder="e.g. AY2023-00123" />
                                                    <x-input-error class="mt-2" :messages="$errors->get('teacher_number')" />
                                                </div>
                                                <div>
                                                    <x-input-label for="department_id" :value="__('Department / Faculty')" />
                                                    
                                                    {{-- FIXED: Dynamic Dropdown using 'department_id' --}}
                                                    <select id="department_id" name="department_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-maroon-700 focus:ring-maroon-700 py-3 px-4 bg-white text-gray-900">
                                                        <option value="" disabled {{ !$user->department_id ? 'selected' : '' }}>Select Department...</option>
                                                        
                                                        @if(isset($departments))
                                                            @foreach($departments as $dept)
                                                                <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                                                    {{ $dept->name }} @if($dept->acronym) ({{ $dept->acronym }}) @endif
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <x-input-error class="mt-2" :messages="$errors->get('department_id')" />
                                                </div>
                                            @endif

                                            <div class="flex items-center gap-4">
                                                <x-primary-button class="bg-maroon-700 hover:bg-maroon-800 py-3 px-6">{{ __('Save') }}</x-primary-button>
                                            </div>
                                        </form>
                                    </section>
                                </div>
                            </div>

                            {{-- 2. Update Password --}}
                            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border border-gray-100">
                                <div class="max-w-xl">
                                    <section>
                                        <header>
                                            <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
                                            <p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password to stay secure.</p>
                                        </header>

                                        <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                                            @csrf @method('put')

                                            <div>
                                                <x-input-label for="current_password" :value="__('Current Password')" />
                                                <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-maroon-700 focus:ring-maroon-700 py-3 px-4 bg-white text-gray-900" autocomplete="current-password" />
                                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="password" :value="__('New Password')" />
                                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-maroon-700 focus:ring-maroon-700 py-3 px-4 bg-white text-gray-900" autocomplete="new-password" />
                                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-maroon-700 focus:ring-maroon-700 py-3 px-4 bg-white text-gray-900" autocomplete="new-password" />
                                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <x-primary-button class="bg-gray-800 py-3 px-6">{{ __('Save') }}</x-primary-button>
                                            </div>
                                        </form>
                                    </section>
                                </div>
                            </div>

                            {{-- 3. Delete Account --}}
                            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border border-red-100">
                                <div class="max-w-xl">
                                    <section class="space-y-6">
                                        <header>
                                            <h2 class="text-lg font-medium text-gray-900">Delete Account</h2>
                                            <p class="mt-1 text-sm text-gray-600">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                                        </header>

                                        <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="py-3 px-6">{{ __('Delete Account') }}</x-danger-button>

                                        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                                            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                                                @csrf @method('delete')
                                                <h2 class="text-lg font-medium text-gray-900">Are you sure you want to delete your account?</h2>
                                                <p class="mt-1 text-sm text-gray-600">Please enter your password to confirm you would like to permanently delete your account.</p>
                                                <div class="mt-6">
                                                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4 border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 bg-white text-gray-900" placeholder="{{ __('Password') }}" />
                                                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                                                </div>
                                                <div class="mt-6 flex justify-end">
                                                    <x-secondary-button x-on:click="$dispatch('close')" class="py-3 px-6">{{ __('Cancel') }}</x-secondary-button>
                                                    <x-danger-button class="ms-3 py-3 px-6">{{ __('Delete Account') }}</x-danger-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </section>
                                </div>
                            </div>

                        </div>
                    @endif
                @endauth

            </div>
        </div>
    </div>
</body>
</html>