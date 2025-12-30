<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VincenThinks - Home</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <script>
        tailwind.config = { theme: { extend: { colors: { maroon: { 700: '#800000', 800: '#600000', 900: '#400000' } } } } }
    </script>
    <style>
        .line-clamp-3 pre { overflow: hidden; }
        .prose img { display: block; max-width: 100%; max-height: 300px; width: auto; height: auto; margin: 10px 0; border-radius: 6px; object-fit: contain; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    </style>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal flex flex-col min-h-screen" x-data="{ mobileMenuOpen: false }">

    @include('partials.navbar')

    <div class="max-w-7xl mx-auto w-full mt-6 px-4 flex flex-col lg:flex-row gap-8">

        {{-- MOBILE FILTER DRAWER --}}
        <div x-cloak x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true">
            <div x-show="mobileMenuOpen" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
            <div x-show="mobileMenuOpen" class="relative mr-auto flex h-full w-4/5 max-w-xs flex-col overflow-y-auto bg-white shadow-2xl">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center"><i class='bx bx-category text-maroon-700 mr-2'></i> Categories</h2>
                    <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-gray-600 transition"><i class='bx bx-x text-3xl'></i></button>
                </div>
                <nav class="p-4 space-y-1">
                    <a href="{{ route('home') }}" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ !request('category') ? 'bg-maroon-50 text-maroon-700' : 'text-gray-600 hover:bg-gray-50' }}"><i class='bx bx-grid-alt mr-3 text-lg'></i> All Topics</a>
                    @foreach($categories as $cat)
                        <a href="{{ route('home', array_merge(request()->query(), ['category' => $cat->id])) }}" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request('category') == $cat->id ? 'bg-maroon-50 text-maroon-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span class="w-2 h-2 rounded-full bg-gray-300 mr-3 {{ request('category') == $cat->id ? 'bg-maroon-700' : '' }}"></span>
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        {{-- DESKTOP SIDEBAR --}}
        <aside class="hidden lg:block w-64 flex-shrink-0">
            <div class="sticky top-24 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{ search: '' }">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Filter by Topic</h3>
                    <div class="relative mb-4">
                        <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400'></i>
                        <input type="text" x-model="search" placeholder="Find..." class="w-full pl-9 pr-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-maroon-700 bg-gray-50 focus:bg-white transition">
                    </div>
                    <nav class="space-y-1 max-h-[400px] overflow-y-auto pr-1 custom-scrollbar">
                        <a href="{{ route('home') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition {{ !request('category') ? 'bg-maroon-50 text-maroon-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"><span>All Discussions</span></a>
                        @foreach($categories as $cat)
                            <a href="{{ route('home', array_merge(request()->query(), ['category' => $cat->id])) }}" x-show="$el.innerText.toLowerCase().includes(search.toLowerCase())" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition {{ request('category') == $cat->id ? 'bg-maroon-50 text-maroon-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"><span>{{ $cat->name }}</span></a>
                        @endforeach
                    </nav>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Quick Links</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-maroon-700 flex items-center transition"><i class='bx bx-help-circle mr-2 text-lg'></i> Help Center</a></li>
                        <li><a href="#" class="hover:text-maroon-700 flex items-center transition"><i class='bx bx-info-circle mr-2 text-lg'></i> Guidelines</a></li>
                    </ul>
                </div>
            </div>
        </aside>

        {{-- MAIN FEED AREA --}}
        <main class="flex-1 min-w-0">
            @if(session('success') || session('status'))
                <div class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 mb-6 shadow-sm flex items-center">
                    <i class='bx bx-check-circle text-xl mr-2'></i> {{ session('success') ?? session('status') }}
                </div>
            @endif

            @auth
                @if (!$verification_required || Auth::user()->hasVerifiedEmail())
                    {{-- Ask Question Box --}}
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-200 relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-1 h-full bg-maroon-700"></div>
                        <h3 class="text-xl font-light text-gray-800 mb-4 flex items-center">
                            <i class='bx bx-edit text-2xl text-maroon-700 mr-2 font-thin'></i> Ask the Community
                        </h3>
                        <form action="{{ route('question.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <select name="category_id" required class="w-full border-b border-gray-200 bg-transparent p-2 mb-4 focus:border-maroon-700 focus:outline-none text-sm font-light text-gray-600 cursor-pointer">
                                <option value="" disabled selected>Select a Category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="title" placeholder="What's your question?" required class="w-full border-b border-gray-200 bg-transparent p-2 mb-4 focus:border-maroon-700 focus:outline-none text-lg font-normal transition-colors placeholder-gray-400">
                            <div class="mb-4">
                                <x-trix-editor name="content" placeholder="Type your question here..." />
                                @error('content') <p class="text-red-500 text-xs mt-1 font-bold flex items-center"><i class='bx bx-error-circle mr-1'></i> {{ $message }}</p> @enderror
                            </div>
                            <div class="mb-4 flex items-center">
                                <label class="cursor-pointer flex items-center text-xs text-gray-500 hover:text-maroon-700 transition">
                                    <i class='bx bx-images text-lg mr-1'></i> Add Images (Optional)
                                    <input type="file" name="images[]" multiple class="hidden" onchange="document.getElementById('img-preview-count').innerText = this.files.length + ' files selected'">
                                </label>
                                <span id="img-preview-count" class="ml-3 text-xs text-maroon-700 font-bold"></span>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="bg-maroon-700 text-white px-6 py-2 rounded-lg font-normal hover:bg-maroon-800 transition shadow-sm flex items-center ml-auto tracking-wide"><i class='bx bx-send mr-2'></i> Post Question</button>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- Unverified Email Warning --}}
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-6 mb-8 rounded-lg shadow-sm flex items-center justify-between">
                        <div class="flex items-center">
                            <i class='bx bxs-lock text-3xl mr-4 font-thin'></i>
                            <div><p class="font-normal text-lg text-gray-800">Action Blocked: Email Verification Required</p><p class="text-sm font-light mt-1 text-gray-600">You must verify your email address before you can post.</p></div>
                        </div>
                        <form method="POST" action="{{ route('verification.send') }}">@csrf<button type="submit" class="bg-red-700 text-white px-4 py-2 rounded-lg font-normal hover:bg-red-800 transition shadow-sm flex items-center tracking-wide"><i class='bx bx-mail-send mr-2'></i> Resend Link</button></form>
                    </div>
                @endif
            @else
                {{-- Guest Banner --}}
                <div class="bg-white border-l-4 border-blue-400 text-gray-600 p-6 mb-8 rounded-lg shadow-sm flex items-center">
                    <i class='bx bx-info-circle text-3xl text-blue-400 mr-4 font-thin'></i>
                    <div><p class="font-normal text-lg text-gray-800">Join the conversation!</p><p class="text-sm font-light mt-1">Please <a href="{{ route('login') }}" class="underline hover:text-blue-600">login</a> to ask questions.</p></div>
                </div>
            @endauth

            {{-- Feed Header --}}
            <div class="flex flex-col sm:flex-row items-center justify-between mb-4 space-y-4 sm:space-y-0">
                <h2 class="text-2xl font-light text-gray-800 flex items-center">
                    <i class='bx bx-message-square-dots text-maroon-700 mr-2 font-thin'></i> 
                    @if(request('category'))
                        {{ optional($categories->find(request('category')))->name ?? 'Unknown' }} Discussions
                    @else
                        Recent Discussions
                    @endif
                </h2>
                <form action="/" method="GET" class="relative w-full sm:w-72">
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg'></i>
                    <input type="text" name="search" placeholder="Search questions..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-full focus:outline-none focus:border-maroon-700 focus:ring-1 focus:ring-maroon-700 text-sm font-light bg-white shadow-sm transition">
                </form>
            </div>
            
            {{-- Filter Tabs --}}
            <div class="flex items-center space-x-2 mb-6 overflow-x-auto custom-scrollbar pb-2">
                @php
                    $currentFilter = request('filter');
                    $baseClasses = "px-4 py-1.5 rounded-full text-xs font-bold border transition whitespace-nowrap";
                    $activeClasses = "bg-maroon-700 text-white border-maroon-700 shadow-sm";
                    $inactiveClasses = "bg-white text-gray-600 border-gray-200 hover:border-maroon-700 hover:text-maroon-700";
                @endphp

                <a href="{{ route('home', array_merge(request()->except('filter'), ['page' => 1])) }}" class="{{ $baseClasses }} {{ !$currentFilter ? $activeClasses : $inactiveClasses }}">All</a>
                <a href="{{ route('home', array_merge(request()->query(), ['filter' => 'solved', 'page' => 1])) }}" class="{{ $baseClasses }} {{ $currentFilter === 'solved' ? $activeClasses : $inactiveClasses }}"><i class='bx bx-check-circle mr-1'></i> Solved</a>
                <a href="{{ route('home', array_merge(request()->query(), ['filter' => 'unsolved', 'page' => 1])) }}" class="{{ $baseClasses }} {{ $currentFilter === 'unsolved' ? $activeClasses : $inactiveClasses }}"><i class='bx bx-question-mark mr-1'></i> Unsolved</a>
                 <a href="{{ route('home', array_merge(request()->query(), ['filter' => 'no_answers', 'page' => 1])) }}" class="{{ $baseClasses }} {{ $currentFilter === 'no_answers' ? $activeClasses : $inactiveClasses }}"><i class='bx bx-message-square-x mr-1'></i> No Answers</a>
            </div>

            @if(request('search'))
                <div class="mb-4 text-sm text-gray-500 font-light flex items-center">
                    <span>Showing results for: <strong>"{{ request('search') }}"</strong></span>
                    <a href="/" class="ml-2 text-maroon-700 hover:underline flex items-center"><i class='bx bx-x'></i> Clear</a>
                </div>
            @endif

            {{-- Questions Feed --}}
            <div class="space-y-4 pb-12">
                @forelse($questions as $q)
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition duration-200 hover:border-maroon-200 group relative">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                @if($q->user->avatar)
                                    <img src="{{ asset('storage/' . $q->user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="{{ $q->user->name }}">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 text-sm font-bold">{{ substr($q->user->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="flex justify-between items-start mb-3">
                                    {{-- ADDED: flex-wrap so it doesn't break on small phones --}}
                                        <div class="flex items-center flex-wrap gap-y-1 text-xs text-gray-400 font-light">
                                        <a href="{{ route('user.profile', $q->user->id) }}" class="font-medium text-gray-600 hover:underline hover:text-maroon-700">{{ $q->user->name }}</a>
                                        @if($q->user->member_type === 'student' && $q->user->course)
                                            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 ml-1" title="{{ $q->user->course->name }}">{{ $q->user->course->acronym }}</span>
                                        @elseif($q->user->member_type === 'teacher' && $q->user->departmentInfo)
                                            <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100 ml-1" title="{{ $q->user->departmentInfo->name }}">{{ $q->user->departmentInfo->acronym ?? $q->user->departmentInfo->name }}</span>
                                        @endif
                                        <span class="mx-1">•</span>
                                        <span>{{ $q->created_at->diffForHumans() }}</span>
                                        @if($q->category)
                                            <span class="mx-2 text-gray-300">|</span>
                                            {{-- UPDATED: Shows Acronym if available, otherwise falls back to Name --}}
                                            <span class="bg-gray-100 text-maroon-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-gray-200" 
                                                  title="{{ $q->category->name }}">
                                                {{ $q->category->acronym ?? $q->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($q->best_answer_id)
                                            <span class="flex-shrink-0 bg-green-100 text-green-700 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wide flex items-center border border-green-200 shadow-sm"><i class='bx bx-check mr-1 text-base'></i> Solved</span>
                                        @endif
                                        @auth
                                            @if(Auth::id() === $q->user_id || Auth::user()->is_admin)
                                                <form action="{{ route('question.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition" title="Delete Question"><i class='bx bx-trash text-lg'></i></button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                <a href="{{ route('question.show', $q->id) }}" class="block group-hover:text-maroon-700 transition-colors">
                                    <h3 class="text-lg font-bold text-gray-900 leading-tight mb-2 group-hover:text-maroon-700 transition-colors">{{ $q->title }}</h3>
                                    <div class="prose prose-sm prose-stone text-gray-600 line-clamp-3 mb-4">{!! Str::markdown($q->content) !!}</div>
                                    @if($q->images->count() > 0)
                                        <div class="mt-2 rounded-lg overflow-hidden h-72 w-full border border-gray-200 relative bg-gray-100 flex justify-center items-center">
                                            <img src="{{ asset('storage/' . $q->images->first()->image_path) }}" class="w-full h-full object-contain">
                                            @if($q->images->count() > 1)
                                                <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center shadow-sm"><i class='bx bx-images mr-1.5 text-sm'></i> +{{ $q->images->count() - 1 }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </a>
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-4 text-sm text-gray-400 font-light">
                                        <span class="flex items-center"><i class='bx bx-message-alt mr-1 text-base'></i> {{ $q->answers_count }} Answers</span>
                                        <span class="flex items-center"><i class='bx bx-show mr-1 text-lg'></i> {{ $q->views ?? 0 }} Views</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                        <i class='bx bx-search-alt text-4xl text-gray-300 mb-2'></i>
                        <p class="text-gray-500 font-light">No questions found.</p>
                    </div>
                @endforelse
                <div class="mt-8">
                    {{ $questions->withQueryString()->links('partials.pagination') }}
                </div>
            </div>
        </main>
    </div>
</body>
</html>