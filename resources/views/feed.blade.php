<x-public-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
        <style>
            .line-clamp-3 pre { overflow: hidden; }
            .prose img { display: block; max-width: 100%; max-height: 300px; width: auto; height: auto; margin: 10px 0; border-radius: 6px; object-fit: contain; }
            [x-cloak] { display: none !important; }
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
        <script>hljs.highlightAll();</script>
    @endpush

    {{-- Main Container with x-data for Modal State --}}
    <div x-data="{ mobileMenuOpen: false, createModalOpen: false }" class="max-w-7xl mx-auto w-full mt-6 px-4 flex flex-col lg:flex-row gap-8 relative">

        {{-- MODAL COMPONENT --}}
        <x-create-question-modal :categories="$categories" />

        {{-- MOBILE FAB --}}
        @auth
            <button 
                @click="createModalOpen = true"
                class="lg:hidden fixed bottom-6 right-6 z-40 bg-maroon-700 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:bg-maroon-800 hover:scale-105 transition transform focus:outline-none focus:ring-4 focus:ring-maroon-300"
            >
                <i class='bx bx-plus text-3xl'></i>
            </button>
        @endauth

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
                    <div class="hidden lg:block bg-white rounded-xl shadow-sm p-4 mb-8 border border-gray-200">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 font-bold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <button 
                                @click="createModalOpen = true"
                                class="flex-grow text-left bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-full py-2.5 px-5 text-sm text-gray-500 font-light transition duration-200 focus:outline-none focus:ring-2 focus:ring-maroon-100"
                            >
                                What do you want to ask, {{ Auth::user()->name }}?
                            </button>
                            <div class="flex items-center gap-2 text-gray-400">
                                <button @click="createModalOpen = true" class="p-2 hover:bg-gray-50 rounded-full hover:text-maroon-700 transition" title="Upload Image"><i class='bx bx-image-add text-xl'></i></button>
                                <button @click="createModalOpen = true" class="p-2 hover:bg-gray-50 rounded-full hover:text-maroon-700 transition" title="Select Category"><i class='bx bx-category-alt text-xl'></i></button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-6 mb-8 rounded-lg shadow-sm flex items-center justify-between">
                        <div class="flex items-center">
                            <i class='bx bxs-lock text-3xl mr-4 font-thin'></i>
                            <div><p class="font-normal text-lg text-gray-800">Action Blocked: Email Verification Required</p><p class="text-sm font-light mt-1 text-gray-600">You must verify your email address before you can post.</p></div>
                        </div>
                        <form method="POST" action="{{ route('verification.send') }}">@csrf<button type="submit" class="bg-red-700 text-white px-4 py-2 rounded-lg font-normal hover:bg-red-800 transition shadow-sm flex items-center tracking-wide"><i class='bx bx-mail-send mr-2'></i> Resend Link</button></form>
                    </div>
                @endif
            @else
                <div class="bg-white border-l-4 border-blue-400 text-gray-600 p-6 mb-8 rounded-lg shadow-sm flex items-center">
                    <i class='bx bx-info-circle text-3xl text-blue-400 mr-4 font-thin'></i>
                    <div><p class="font-normal text-lg text-gray-800">Join the conversation!</p><p class="text-sm font-light mt-1">Please <a href="{{ route('login') }}" class="underline hover:text-blue-600">login</a> to ask questions.</p></div>
                </div>
            @endauth

            {{-- Feed Header & Filters --}}
            <div class="flex flex-col sm:flex-row items-center justify-between mb-4 space-y-4 sm:space-y-0">
                <h2 class="text-2xl font-light text-gray-800 flex items-center">
                    <i class='bx bx-message-square-dots text-maroon-700 mr-2 font-thin'></i> 
                    @if(request('category'))
                        {{ optional($categories->find(request('category')))->name ?? 'Unknown' }} Discussions
                    @else
                        Recent Discussions
                    @endif
                </h2>
                <button @click="mobileMenuOpen = true" class="lg:hidden w-full flex items-center justify-center space-x-2 bg-white border border-gray-200 py-2 rounded-lg text-sm font-medium text-gray-600 shadow-sm">
                    <i class='bx bx-filter'></i> <span>Filter Topics</span>
                </button>
                <form action="/" method="GET" class="hidden sm:block relative w-72">
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg'></i>
                    <input type="text" name="search" placeholder="Search questions..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-full focus:outline-none focus:border-maroon-700 focus:ring-1 focus:ring-maroon-700 text-sm font-light bg-white shadow-sm transition">
                </form>
            </div>
            
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
            {{-- Increased space-y-4 to space-y-6 to accommodate the floating 'Legend' badge --}}
            <div id="feed-container" class="space-y-6 pb-12">
                @forelse($questions as $q)
                    {{-- 
                        CARD LOGIC: 
                        If solved: border-green-500 + green ring. 
                        Else: default styling.
                    --}}
                    <div class="bg-white rounded-xl shadow-sm p-5 border transition duration-200 group relative 
                        {{ $q->best_answer_id 
                            ? 'border-green-500 ring-1 ring-green-500' 
                            : 'border-gray-100 hover:border-maroon-200 hover:shadow-md' 
                        }}">
                        
                        {{-- NEW: Solved "Legend" Badge on Top Border --}}
                        @if($q->best_answer_id)
                            <div class="absolute -top-3 left-4 bg-green-100 text-green-700 border border-green-400 px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-sm flex items-center gap-1 z-10">
                                <i class='bx bx-check text-base'></i> Solved
                            </div>
                        @endif

                        <div class="flex items-start {{ $q->best_answer_id ? 'mt-2' : '' }}"> {{-- Slight margin top if solved to breathe --}}
                            <div class="flex-shrink-0 mr-4">
                                @if($q->user->avatar)
                                    <img src="{{ asset('storage/' . $q->user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="{{ $q->user->name }}">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 text-sm font-bold">{{ substr($q->user->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="flex justify-between items-start mb-3">
                                    
                                    {{-- Metadata Container --}}
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-y-1 sm:gap-x-2 text-xs text-gray-400 font-light flex-1 min-w-0">
                                        
                                        {{-- Row 1: Name + Role --}}
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ route('user.profile', $q->user->id) }}" class="font-medium text-gray-600 hover:underline hover:text-maroon-700">{{ $q->user->name }}</a>
                                            @if($q->user->member_type === 'student' && $q->user->course)
                                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 whitespace-nowrap" title="{{ $q->user->course->name }}">{{ $q->user->course->acronym }}</span>
                                            @elseif($q->user->member_type === 'teacher' && $q->user->departmentInfo)
                                                <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100 whitespace-nowrap" title="{{ $q->user->departmentInfo->name }}">{{ $q->user->departmentInfo->acronym ?? $q->user->departmentInfo->name }}</span>
                                            @endif
                                        </div>

                                        {{-- Desktop Separator --}}
                                        <span class="hidden sm:inline text-gray-300">•</span>

                                        {{-- Row 2: Time + Category (Solved Badge Removed from here) --}}
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="whitespace-nowrap">{{ $q->created_at->diffForHumans() }}</span>
                                            @if($q->category)
                                                <span class="text-gray-300">|</span>
                                                <span class="bg-gray-100 text-maroon-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-gray-200 whitespace-nowrap" 
                                                      title="{{ $q->category->name }}">
                                                    {{ $q->category->acronym ?? $q->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Actions: Only Delete now --}}
                                    <div class="flex items-center space-x-2 pl-2 flex-shrink-0">
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
</x-public-layout>