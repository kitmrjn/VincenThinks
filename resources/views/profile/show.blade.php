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
        tailwind.config = { theme: { extend: { colors: { maroon: { 700: '#800000', 800: '#600000', 900: '#400000' } } } } }
    </script>
    <style>
        .prose p { margin-top: 0; margin-bottom: 0.5em; }
        .prose img { margin-top: 0.5em; margin-bottom: 0.5em; }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal min-h-screen flex flex-col">

    @include('partials.navbar')

    <div class="max-w-6xl mx-auto w-full mt-8 px-4 pb-12 flex-grow">
        
        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 mb-6 shadow-sm flex items-center">
                <i class='bx bx-check-circle text-xl mr-2'></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200 mb-6 shadow-sm flex items-start">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
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
                                        <label for="avatar_upload" class="bg-maroon-700 text-white rounded-full p-2 cursor-pointer hover:bg-maroon-800 shadow-sm border-2 border-white flex items-center justify-center w-8 h-8" title="Change Picture">
                                            <i class='bx bx-camera text-xs'></i>
                                        </label>
                                        <input type="file" name="avatar" id="avatar_upload" class="hidden" 
                                            onchange="this.form.submit()">
                                    </form>
                                @endif
                            @endauth
                        </div>

                        <h1 class="text-2xl font-bold text-gray-900 mt-4">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500 mt-1 flex items-center justify-center">
                            <i class='bx bx-calendar mr-1'></i> Joined {{ $user->created_at->format('M Y') }}
                        </p>
                        
                        {{-- Badges Container --}}
                        <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                            {{-- Admin Badge --}}
                            @if($user->is_admin)
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-maroon-100 text-maroon-800 border border-maroon-200">
                                    <i class='bx bxs-shield-alt-2 mr-1'></i> Administrator
                                </span>
                            @endif
                            
                            {{-- Email Verified Badge --}}
                            @if ($user->hasVerifiedEmail())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class='bx bx-check-circle mr-1'></i> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    <i class='bx bx-time mr-1'></i> Unverified
                                </span>
                            @endif
                        </div>

                        {{-- Academic Details Section --}}
                        <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                            {{-- Member Type --}}
                            @if($user->member_type === 'teacher')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    <i class='bx bxs-briefcase-alt-2 mr-1'></i> Teacher
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    <i class='bx bxs-school mr-1'></i> Student
                                </span>
                            @endif

                            {{-- Course Badge --}}
                            @if($user->member_type === 'student' && $user->course)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border cursor-help
                                    {{ $user->course->type == 'College' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                      ($user->course->type == 'SHS' ? 'bg-orange-50 text-orange-700 border-orange-200' : 
                                      ($user->course->type == 'JHS' ? 'bg-green-50 text-green-700 border-green-200' : 
                                      'bg-purple-50 text-purple-700 border-purple-200')) }}" 
                                      title="{{ $user->course->name }}">
                                    {{ $user->course->acronym }}
                                </span>
                            @endif
                        </div>

                        {{-- Student Number --}}
                        @if($user->member_type === 'student' && $user->student_number)
                            <div class="mt-2 text-xs text-gray-500 font-mono bg-gray-50 inline-block px-2 py-1 rounded border border-gray-200">
                                ID: {{ $user->student_number }}
                            </div>
                        @endif

                    </div>

                    <hr class="border-gray-100 my-6">

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
                        <div class="flex flex-col items-center">
                            <span class="font-bold text-gray-900 text-lg">{{ $user->questions->count() }}</span>
                            <span>Questions</span>
                        </div>
                        <div class="h-10 w-px bg-gray-200"></div>
                        <div class="flex flex-col items-center">
                            <span class="font-bold text-gray-900 text-lg">{{ $user->answers->count() }}</span>
                            <span>Answers</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- RIGHT CONTENT --}}
            <div class="w-full lg:w-2/3" 
                x-data="{ activeTab: '{{ request('answers_page') ? 'answers' : 'questions' }}' }">
                
                <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
                    <button @click="activeTab = 'questions'" 
                            class="px-6 py-3 text-sm font-medium transition whitespace-nowrap border-b-2"
                            :class="activeTab === 'questions' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                        <i class='bx bx-question-mark mr-1 text-lg align-middle'></i> Questions
                    </button>
                    <button @click="activeTab = 'answers'" 
                            class="px-6 py-3 text-sm font-medium transition whitespace-nowrap border-b-2"
                            :class="activeTab === 'answers' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                        <i class='bx bx-message-square-dots mr-1 text-lg align-middle'></i> Answers
                    </button>
                </div>

                {{-- QUESTIONS TAB --}}
                <div x-show="activeTab === 'questions'" class="space-y-4" style="display: none;">
                    @forelse($questions_list as $q)
                        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition duration-200 hover:border-maroon-200 group relative">
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-4">
                                    @if($q->user->avatar)
                                        <img src="{{ asset('storage/' . $q->user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="{{ $q->user->name }}">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 text-sm font-bold">
                                            {{ substr($q->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-grow min-w-0">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center text-xs text-gray-400 mb-1 font-light">
                                            <span class="font-medium text-gray-600 mr-2">{{ $q->user->name }}</span>
                                            <span class="mx-1">•</span>
                                            <span>{{ $q->created_at->diffForHumans() }}</span>
                                            @if($q->category)
                                                <span class="mx-2 text-gray-300">|</span>
                                                <span class="bg-gray-100 text-maroon-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-gray-200">{{ $q->category->name }}</span>
                                            @endif
                                        </div>

                                        @if($q->best_answer_id)
                                            <span class="bg-green-100 text-green-700 border border-green-200 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide flex items-center whitespace-nowrap ml-2">
                                                <i class='bx bx-check mr-1'></i> Solved
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ route('question.show', $q->id) }}" class="block group-hover:text-maroon-700 transition-colors mb-2">
                                        <h3 class="text-lg font-normal text-gray-800 leading-tight">{{ $q->title }}</h3>
                                    </a>
                                    
                                    {{-- IMAGE GRID --}}
                                    @if($q->images->count() > 0)
                                        <div class="mb-3 mt-2 grid grid-cols-2 gap-1 rounded-lg overflow-hidden {{ $q->images->count() == 1 ? 'h-64 w-fit max-w-full border border-gray-200' : 'h-32 w-full' }}">
                                            @if($q->images->count() == 1)
                                                <div class="col-span-2 h-full relative">
                                                    <img src="{{ asset('storage/' . $q->images->first()->image_path) }}" class="h-full w-auto max-w-full object-cover" alt="Question Image">
                                                </div>
                                            @else
                                                <div class="h-full relative w-full bg-gray-100">
                                                    <img src="{{ asset('storage/' . $q->images[0]->image_path) }}" class="absolute inset-0 w-full h-full object-cover" alt="Image 1">
                                                </div>
                                                <div class="h-full relative w-full bg-gray-100">
                                                    <img src="{{ asset('storage/' . $q->images[1]->image_path) }}" class="absolute inset-0 w-full h-full object-cover" alt="Image 2">
                                                    
                                                    @if($q->images->count() > 2)
                                                        <div class="absolute inset-0 bg-gray-900/60 flex items-center justify-center backdrop-blur-[2px] transition hover:bg-gray-900/50 cursor-pointer">
                                                            <span class="text-white font-bold text-xl drop-shadow-md">+{{ $q->images->count() - 2 }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- QUESTION CONTENT --}}
                                    <div class="prose prose-sm prose-stone text-gray-500 line-clamp-3">
                                        {!! $q->content !!}
                                    </div>
                                    
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-400 font-light">
                                            <span class="flex items-center">
                                                <i class='bx bx-message-alt mr-1 text-base'></i> {{ $q->answers->count() }} Answers
                                            </span>
                                            <span class="flex items-center">
                                                <i class='bx bx-show mr-1 text-lg'></i> {{ $q->views ?? 0 }} Views
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                            <i class='bx bx-question-mark text-4xl text-gray-300 mb-2'></i>
                            <p class="text-gray-500 text-sm">No questions asked yet.</p>
                        </div>
                    @endforelse

                    {{-- UPDATED: Uses Custom Pagination --}}
                    <div class="mt-4">
                        {{ $questions_list->appends(request()->except('questions_page'))->links('partials.pagination') }}
                    </div>
                </div>

                {{-- ANSWERS TAB --}}
                <div x-show="activeTab === 'answers'" class="space-y-4" style="display: none;">
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
                                        if ($otherScore > $myScore) {
                                            $isTopRated = false;
                                            break;
                                        }
                                    }
                                }
                            }
                        @endphp

                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover:border-maroon-300 transition {{ $isBest ? 'border-l-4 border-l-green-500' : '' }}">
                            
                            <div class="flex justify-between items-start mb-3">
                                <div class="text-xs text-gray-500 font-light pr-4">
                                    <span>Answered on:</span>
                                    <a href="{{ route('question.show', $answer->question->id) }}" class="text-gray-700 hover:text-maroon-700 hover:underline font-bold block mt-0.5 text-sm leading-tight">
                                        {{ Str::limit($answer->question->title, 70) }}
                                    </a>
                                </div>

                                <div class="flex flex-col items-end flex-shrink-0 gap-1">
                                    <span class="text-[10px] text-gray-400 font-light">{{ $answer->created_at->diffForHumans() }}</span>
                                    
                                    <div class="flex gap-1 justify-end flex-wrap">
                                        @if($isBest)
                                            <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded border border-green-200 flex items-center">
                                                <i class='bx bx-check mr-1'></i> Solution
                                            </span>
                                        @endif

                                        @if($isTopRated)
                                            <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded border border-yellow-200 flex items-center">
                                                <i class='bx bxs-trophy mr-1'></i> Top Rated
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            {{-- ANSWER CONTENT --}}
                            <div class="prose prose-sm prose-stone text-gray-600 mb-4 line-clamp-3">
                                {!! $answer->content !!}
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex items-center text-xs text-yellow-600 font-bold bg-white border border-yellow-200 inline-block px-2 py-1 rounded-full shadow-sm">
                                    <i class='bx bxs-star mr-1 text-yellow-400'></i> {{ number_format($myScore, 1) }} Rating
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                            <i class='bx bx-message-square-dots text-4xl text-gray-300 mb-2'></i>
                            <p class="text-gray-500 text-sm">No answers posted yet.</p>
                        </div>
                    @endforelse

                    {{-- UPDATED: Uses Custom Pagination --}}
                    <div class="mt-4">
                        {{ $answers_list->appends(request()->except('answers_page'))->links('partials.pagination') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>