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
            .hide-scrollbar::-webkit-scrollbar { display: none; }
            .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        </style>
    @endpush

    {{-- Main Container --}}
    {{-- ADDED: leaderboardOpen: false --}}
    <div x-data="{ mobileMenuOpen: false, createModalOpen: false, searchOpen: false, sortOpen: false, filterOpen: false, leaderboardOpen: false }" class="max-w-7xl mx-auto w-full mt-6 px-4 flex flex-col lg:flex-row gap-8 relative pb-24 lg:pb-0">

        <x-create-question-modal :categories="$categories" />

        {{-- ================================================= --}}
        {{--  MOBILE BOTTOM SHEETS                             --}}
        {{-- ================================================= --}}
        
        {{-- Shared Backdrop --}}
        <div 
            x-cloak 
            x-show="searchOpen || mobileMenuOpen || sortOpen || filterOpen || leaderboardOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[80] bg-gray-900/40 backdrop-blur-[2px] lg:hidden"
            @click="searchOpen = false; mobileMenuOpen = false; sortOpen = false; filterOpen = false; leaderboardOpen = false"
        ></div>

        {{-- 1. SEARCH SHEET (Unchanged) --}}
        <div x-cloak x-show="searchOpen" x-transition:enter="transition cubic-bezier(0.32, 0.72, 0, 1) duration-500" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="fixed bottom-0 left-0 w-full z-[90] bg-white rounded-t-2xl shadow-xl lg:hidden pb-safe">
            <div class="w-full flex justify-center pt-3 pb-1" @click="searchOpen = false"><div class="w-12 h-1.5 bg-gray-300 rounded-full"></div></div>
            <div class="p-4 pt-2">
                <form action="{{ route('feed') }}" method="GET" class="relative">
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    <div class="flex items-center gap-3">
                        <div class="relative flex-grow">
                            <i class='bx bx-search absolute left-4 top-1/2 transform -translate-y-1/2 text-maroon-700 text-xl'></i>
                            <input x-ref="mobileSearchInput" type="text" name="search" placeholder="Search questions..." value="{{ request('search') }}" class="search-input-js w-full pl-11 pr-4 py-3.5 border-0 bg-gray-100 rounded-xl focus:ring-2 focus:ring-maroon-700 text-base font-normal placeholder-gray-500 transition-all">
                        </div>
                        <button type="button" @click="searchOpen = false" class="p-2 text-gray-500 font-medium hover:text-gray-800">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. CATEGORIES SHEET (Unchanged) --}}
        <div x-cloak x-show="mobileMenuOpen" x-transition:enter="transition cubic-bezier(0.32, 0.72, 0, 1) duration-500" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="fixed bottom-0 left-0 w-full z-[90] bg-white rounded-t-2xl shadow-xl lg:hidden flex flex-col max-h-[85vh] pb-safe">
            <div class="w-full flex flex-col items-center pt-3 pb-4 border-b border-gray-100" @click="mobileMenuOpen = false">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mb-4"></div>
                <h3 class="text-lg font-bold text-gray-800">Select a Topic</h3>
            </div>
            <div class="overflow-y-auto p-4 custom-scrollbar">
                <a href="{{ route('feed') }}" class="flex items-center justify-between p-4 mb-3 rounded-xl border transition-all {{ !request('category') ? 'bg-maroon-50 border-maroon-200 text-maroon-700 shadow-sm' : 'bg-white border-gray-100 text-gray-700 hover:border-maroon-200' }}">
                    <span class="font-bold flex items-center"><i class='bx bx-grid-alt mr-3 text-xl'></i> All Topics</span>
                    @if(!request('category')) <i class='bx bx-check text-xl'></i> @endif
                </a>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($categories as $cat)
                        <a href="{{ route('feed', array_merge(request()->query(), ['category' => $cat->id])) }}" class="flex flex-col p-3 rounded-xl border transition-all h-full justify-center {{ request('category') == $cat->id ? 'bg-maroon-700 text-white border-maroon-700 shadow-md transform scale-[1.02]' : 'bg-gray-50 border-transparent text-gray-600 hover:bg-white hover:border-gray-200 hover:shadow-sm' }}">
                            <span class="font-bold text-sm truncate">{{ $cat->acronym ?? $cat->name }}</span>
                            <span class="text-[10px] opacity-80 font-light mt-0.5">{{ $cat->questions_count ?? 0 }} questions</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 3. SORT SHEET (Unchanged) --}}
        <div x-cloak x-show="sortOpen" x-transition:enter="transition cubic-bezier(0.32, 0.72, 0, 1) duration-500" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="fixed bottom-0 left-0 w-full z-[90] bg-white rounded-t-2xl shadow-xl lg:hidden flex flex-col pb-safe">
            <div class="w-full flex flex-col items-center pt-3 pb-4 border-b border-gray-100" @click="sortOpen = false">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mb-4"></div>
                <h3 class="text-lg font-bold text-gray-800">Sort By</h3>
            </div>
            <div class="p-4 space-y-2">
                @php $sort = request('sort'); @endphp
                <a href="{{ route('feed', array_merge(request()->except('sort'), ['sort' => null])) }}" class="flex items-center justify-between p-4 rounded-xl {{ !$sort ? 'bg-maroon-50 text-maroon-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}"><span><i class='bx bx-time-five mr-2'></i> Newest</span>@if(!$sort) <i class='bx bx-check text-xl'></i> @endif</a>
                <a href="{{ route('feed', array_merge(request()->query(), ['sort' => 'popular'])) }}" class="flex items-center justify-between p-4 rounded-xl {{ $sort === 'popular' ? 'bg-maroon-50 text-maroon-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}"><span><i class='bx bx-trending-up mr-2'></i> Popular</span>@if($sort === 'popular') <i class='bx bx-check text-xl'></i> @endif</a>
                <a href="{{ route('feed', array_merge(request()->query(), ['sort' => 'discussed'])) }}" class="flex items-center justify-between p-4 rounded-xl {{ $sort === 'discussed' ? 'bg-maroon-50 text-maroon-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}"><span><i class='bx bx-comment-detail mr-2'></i> Hot (Most Discussed)</span>@if($sort === 'discussed') <i class='bx bx-check text-xl'></i> @endif</a>
            </div>
        </div>

        {{-- 4. FILTER SHEET (Unchanged) --}}
        <div x-cloak x-show="filterOpen" x-transition:enter="transition cubic-bezier(0.32, 0.72, 0, 1) duration-500" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="fixed bottom-0 left-0 w-full z-[90] bg-white rounded-t-2xl shadow-xl lg:hidden flex flex-col pb-safe">
            <div class="w-full flex flex-col items-center pt-3 pb-4 border-b border-gray-100" @click="filterOpen = false">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mb-4"></div>
                <h3 class="text-lg font-bold text-gray-800">Filter Questions</h3>
            </div>
            <div class="p-4 space-y-2">
                @php $filter = request('filter'); @endphp
                <a href="{{ route('feed', array_merge(request()->except('filter'), ['filter' => null])) }}" class="flex items-center justify-between p-4 rounded-xl {{ !$filter ? 'bg-maroon-50 text-maroon-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}"><span>All Questions</span>@if(!$filter) <i class='bx bx-check text-xl'></i> @endif</a>
                <a href="{{ route('feed', array_merge(request()->query(), ['filter' => 'solved'])) }}" class="flex items-center justify-between p-4 rounded-xl {{ $filter === 'solved' ? 'bg-maroon-50 text-maroon-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}"><span><i class='bx bx-check-circle mr-2 text-green-600'></i> Solved</span>@if($filter === 'solved') <i class='bx bx-check text-xl'></i> @endif</a>
                <a href="{{ route('feed', array_merge(request()->query(), ['filter' => 'unsolved'])) }}" class="flex items-center justify-between p-4 rounded-xl {{ $filter === 'unsolved' ? 'bg-maroon-50 text-maroon-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}"><span><i class='bx bx-question-mark mr-2 text-orange-400'></i> Unsolved</span>@if($filter === 'unsolved') <i class='bx bx-check text-xl'></i> @endif</a>
                <a href="{{ route('feed', array_merge(request()->query(), ['filter' => 'no_answers'])) }}" class="flex items-center justify-between p-4 rounded-xl {{ $filter === 'no_answers' ? 'bg-maroon-50 text-maroon-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}"><span><i class='bx bx-message-square-x mr-2 text-gray-400'></i> No Answers</span>@if($filter === 'no_answers') <i class='bx bx-check text-xl'></i> @endif</a>
            </div>
        </div>

        {{-- 5. LEADERBOARD SHEET (NEW) --}}
        <div 
            x-cloak x-show="leaderboardOpen" 
            x-transition:enter="transition cubic-bezier(0.32, 0.72, 0, 1) duration-500"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed bottom-0 left-0 w-full z-[90] bg-white rounded-t-2xl shadow-xl lg:hidden flex flex-col max-h-[85vh] pb-safe"
        >
            <div class="w-full flex flex-col items-center pt-3 pb-4 border-b border-gray-100" @click="leaderboardOpen = false">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mb-4"></div>
                <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class='bx bx-trophy text-yellow-500 mr-2'></i> Top Contributors</h3>
            </div>
            <div class="overflow-y-auto p-4 custom-scrollbar">
                @if(isset($topContributors) && $topContributors->count() > 0)
                    <div class="space-y-4">
                        @foreach($topContributors as $index => $user)
                            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 bg-gray-50">
                                <div class="flex items-center">
                                    <div class="mr-3 font-bold text-gray-400 text-sm w-4">{{ $index + 1 }}</div>
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 mr-3">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-maroon-700 font-bold mr-3 shadow-sm">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <a href="{{ route('user.profile', $user->id) }}" class="text-sm font-bold text-gray-800 hover:text-maroon-700 truncate max-w-[120px]">
                                            {{ $user->name }}
                                        </a>
                                        <span class="text-[10px] text-gray-500 uppercase tracking-wider">{{ $user->member_type === 'teacher' ? 'Teacher' : 'Student' }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="bg-maroon-100 text-maroon-800 text-xs font-bold px-2 py-1 rounded-full">
                                        {{ $user->answers_count }} 
                                    </span>
                                    <span class="text-[10px] text-gray-400 mt-1">answers</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>No contributors yet this week.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- DESKTOP SIDEBAR (Unchanged) --}}
        <aside class="hidden lg:block w-64 flex-shrink-0">
            <div class="sticky top-24 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{ search: '' }">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Filter by Category</h3>
                    <div class="relative mb-4">
                        <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400'></i>
                        <input type="text" x-model="search" placeholder="Find..." class="w-full pl-9 pr-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-maroon-700 bg-gray-50 focus:bg-white transition">
                    </div>
                    <nav class="space-y-1 max-h-[400px] overflow-y-auto pr-1 custom-scrollbar">
                        <a href="{{ route('feed') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition {{ !request('category') ? 'bg-maroon-50 text-maroon-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"><span>All Discussions</span></a>
                        @foreach($categories as $cat)
                            <a href="{{ route('feed', array_merge(request()->query(), ['category' => $cat->id])) }}" x-show="$el.innerText.toLowerCase().includes(search.toLowerCase())" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition {{ request('category') == $cat->id ? 'bg-maroon-50 text-maroon-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"><span>{{ $cat->name }}</span></a>
                        @endforeach
                    </nav>
                </div>

                @if(isset($topContributors) && $topContributors->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Top Contributors</h3>
                        <ul class="space-y-4">
                            @foreach($topContributors as $user)
                                <li class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-8 h-8 rounded-full object-cover border border-gray-100 mr-3">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-maroon-700 mr-3">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <a href="{{ route('user.profile', $user->id) }}" class="text-sm font-medium text-gray-700 hover:text-maroon-700 line-clamp-1">
                                                {{ $user->name }}
                                            </a>
                                            <span class="text-[10px] text-gray-400">{{ $user->member_type === 'teacher' ? 'Teacher' : 'Student' }}</span>
                                        </div>
                                    </div>
                                    <span class="bg-maroon-50 text-maroon-700 text-xs font-bold px-2 py-0.5 rounded-full" title="{{ $user->answers_count }} Answers">
                                        {{ $user->answers_count }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 mb-6 shadow-sm flex items-center"><i class='bx bx-check-circle text-xl mr-2'></i> {{ session('success') }}</div>
            @endif

            @auth
                @if (!$verification_required || Auth::user()->hasVerifiedEmail())
                    <div class="hidden lg:block bg-white rounded-xl shadow-sm p-4 mb-8 border border-gray-200">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 font-bold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <button @click="createModalOpen = true" class="flex-grow text-left bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-full py-2.5 px-5 text-sm text-gray-500 font-light transition duration-200 focus:outline-none focus:ring-2 focus:ring-maroon-100">
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

            {{-- ACTION BAR --}}
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl sm:text-2xl font-light text-gray-800 flex items-center">
                    @if(request('category'))
                        <span class="font-semibold text-maroon-700 mr-2">{{ optional($categories->find(request('category')))->acronym ?? 'Topic' }}</span>
                    @else
                        Recent
                    @endif
                    Discussions
                </h2>

                {{-- Mobile Action Buttons (Sort, Filter, Leaders) --}}
                <div class="flex items-center gap-2 lg:hidden">
                    <button @click="sortOpen = true" class="flex items-center px-3 py-1.5 bg-white border border-gray-200 rounded-lg shadow-sm text-xs font-medium text-gray-700 active:bg-gray-50">
                        <i class='bx bx-sort-alt-2 mr-1 text-base'></i>
                        {{ request('sort') ? ucfirst(request('sort')) : 'Sort' }}
                    </button>
                    <button @click="filterOpen = true" class="flex items-center px-3 py-1.5 bg-white border border-gray-200 rounded-lg shadow-sm text-xs font-medium text-gray-700 active:bg-gray-50">
                        <i class='bx bx-filter-alt mr-1 text-base'></i>
                        {{ request('filter') ? ucfirst(str_replace('_', ' ', request('filter'))) : 'Filter' }}
                    </button>
                    {{-- ADDED: Leaders Button --}}
                    <button @click="leaderboardOpen = true" class="flex items-center justify-center w-8 h-8 bg-white border border-gray-200 rounded-lg shadow-sm text-yellow-500 active:bg-gray-50">
                        <i class='bx bx-trophy text-lg'></i>
                    </button>
                </div>

                {{-- Desktop Filter Bar --}}
                <div class="hidden lg:flex items-center space-x-2 bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
                    <a href="{{ route('feed') }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition {{ !request('filter') && !request('sort') ? 'bg-maroon-50 text-maroon-700' : 'text-gray-500 hover:bg-gray-50' }}">All</a>
                    <div class="w-px h-4 bg-gray-200"></div>
                    <a href="{{ route('feed', ['filter' => 'solved']) }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition {{ request('filter') == 'solved' ? 'bg-maroon-50 text-maroon-700' : 'text-gray-500 hover:bg-gray-50' }}">Solved</a>
                    <a href="{{ route('feed', ['filter' => 'unsolved']) }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition {{ request('filter') == 'unsolved' ? 'bg-maroon-50 text-maroon-700' : 'text-gray-500 hover:bg-gray-50' }}">Unsolved</a>
                </div>
            </div>

            <div id="feed-container" class="space-y-6 pb-12">
                @include('partials.question-list')
            </div>
        </main>

        {{-- MOBILE NAV (Unchanged) --}}
        <nav class="lg:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-[80] pb-safe">
            <div class="flex justify-between items-center h-16 px-6">
                <a href="{{ route('feed') }}" class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('feed') && !request('search') && !request('category') ? 'text-maroon-700' : 'text-gray-400 hover:text-gray-600' }}"><i class='bx {{ request()->routeIs('feed') && !request('search') && !request('category') ? 'bxs-home' : 'bx-home-alt' }} text-2xl'></i></a>
                <button @click="mobileMenuOpen = !mobileMenuOpen; searchOpen = false; sortOpen = false; filterOpen = false; leaderboardOpen = false" class="flex flex-col items-center justify-center space-y-1 {{ $mobileMenuOpen ?? false ? 'text-maroon-700' : 'text-gray-400 hover:text-gray-600' }}"><i class='bx {{ request('category') ? 'bxs-grid-alt text-maroon-700' : 'bx-category' }} text-2xl'></i></button>
                <div class="relative -top-5"><button @click="createModalOpen = true" class="w-14 h-14 bg-maroon-700 rounded-full flex items-center justify-center text-white shadow-lg shadow-maroon-700/40 border-4 border-gray-50 hover:scale-105 active:scale-95 transition-all duration-200"><i class='bx bx-plus text-3xl'></i></button></div>
                <button @click="searchOpen = !searchOpen; mobileMenuOpen = false; sortOpen = false; filterOpen = false; leaderboardOpen = false" class="flex flex-col items-center justify-center space-y-1 {{ $searchOpen ?? false ? 'text-maroon-700' : 'text-gray-400 hover:text-gray-600' }}"><i class='bx bx-search text-2xl'></i></button>
                <a href="{{ route('user.profile', Auth::id() ?? 0) }}" class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('user.profile') ? 'text-maroon-700' : 'text-gray-400 hover:text-gray-600' }}"><i class='bx {{ request()->routeIs('user.profile') ? 'bxs-user' : 'bx-user' }} text-2xl'></i></a>
            </div>
        </nav>
    </div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
        <script>hljs.highlightAll();</script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInputs = document.querySelectorAll('.search-input-js');
                const feedContainer = document.getElementById('feed-container');
                let timeout = null;

                searchInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        clearTimeout(timeout);
                        const query = this.value;
                        
                        searchInputs.forEach(otherInput => {
                            if (otherInput !== this) otherInput.value = query;
                        });

                        const url = new URL(window.location.href);
                        if (query.length > 0) {
                            url.searchParams.set('search', query);
                        } else {
                            url.searchParams.delete('search');
                        }
                        
                        window.history.pushState({}, '', url);

                        timeout = setTimeout(() => {
                            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(response => response.text())
                            .then(html => { feedContainer.innerHTML = html; })
                            .catch(error => console.error('Error:', error));
                        }, 300);
                    });
                });
            });
        </script>
    @endpush
</x-public-layout>