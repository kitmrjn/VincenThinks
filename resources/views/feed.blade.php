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
            
            /* Hide scrollbar for horizontal scrolling areas but allow functionality */
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }
            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
    @endpush

    {{-- Main Container with x-data for Modal State --}}
    <div x-data="{ mobileMenuOpen: false, createModalOpen: false, searchOpen: false }" class="max-w-7xl mx-auto w-full mt-6 px-4 flex flex-col lg:flex-row gap-8 relative pb-24 lg:pb-0">

        {{-- MODAL COMPONENT --}}
        <x-create-question-modal :categories="$categories" />

        {{-- MOBILE FILTER DRAWER --}}
        <div x-cloak x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true">
            <div x-show="mobileMenuOpen" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
            <div x-show="mobileMenuOpen" class="relative mr-auto flex h-full w-4/5 max-w-xs flex-col overflow-y-auto bg-white shadow-2xl">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center"><i class='bx bx-category text-maroon-700 mr-2'></i> Categories</h2>
                    <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-gray-600 transition"><i class='bx bx-x text-3xl'></i></button>
                </div>
                <nav class="p-4 space-y-1">
                    <a href="{{ route('feed') }}" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ !request('category') ? 'bg-maroon-50 text-maroon-700' : 'text-gray-600 hover:bg-gray-50' }}"><i class='bx bx-grid-alt mr-3 text-lg'></i> All Topics</a>
                    @foreach($categories as $cat)
                        <a href="{{ route('feed', array_merge(request()->query(), ['category' => $cat->id])) }}" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request('category') == $cat->id ? 'bg-maroon-50 text-maroon-700' : 'text-gray-600 hover:bg-gray-50' }}">
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

                {{-- TOP CONTRIBUTORS WIDGET --}}
                @if(isset($topContributors) && $topContributors->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Top Contributors (Week)</h3>
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
            
            <div id="js-flash-message" style="display: none;" class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 mb-6 shadow-sm flex items-center">
                <i class='bx bx-check-circle text-xl mr-2'></i>
                <span id="js-flash-text"></span>
            </div>

            @if(session('success') || session('status'))
                <div class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 mb-6 shadow-sm flex items-center">
                    <i class='bx bx-check-circle text-xl mr-2'></i> {{ session('success') ?? session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200 mb-6 shadow-sm flex items-center">
                    <i class='bx bx-error-circle text-xl mr-2'></i> {{ session('error') }}
                </div>
            @endif

            {{-- MOBILE SEARCH BAR (Toggled via Bottom Nav) --}}
            <div x-cloak x-show="searchOpen" class="lg:hidden mb-4 transition-all duration-300 ease-in-out">
                <form action="{{ route('feed') }}" method="GET" class="relative">
                     @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg'></i>
                    <input type="text" name="search" placeholder="Search questions..." value="{{ request('search') }}" class="search-input-js w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-maroon-700 focus:ring-1 focus:ring-maroon-700 text-sm font-light bg-white shadow-sm transition">
                </form>
            </div>

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
                
                {{-- Desktop Search --}}
                <form action="{{ route('feed') }}" method="GET" class="hidden lg:block relative w-72" id="search-form">
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg'></i>
                    <input type="text" name="search" placeholder="Search questions..." value="{{ request('search') }}" class="search-input-js w-full pl-10 pr-4 py-2 border border-gray-200 rounded-full focus:outline-none focus:border-maroon-700 focus:ring-1 focus:ring-maroon-700 text-sm font-light bg-white shadow-sm transition">
                </form>
            </div>
            
            <div class="flex items-center space-x-2 mb-6 overflow-x-auto custom-scrollbar pb-2">
                @php
                    $currentFilter = request('filter');
                    $currentSort = request('sort');
                    $baseClasses = "px-4 py-1.5 rounded-full text-xs font-bold border transition whitespace-nowrap";
                    $activeClasses = "bg-maroon-700 text-white border-maroon-700 shadow-sm";
                    $inactiveClasses = "bg-white text-gray-600 border-gray-200 hover:border-maroon-700 hover:text-maroon-700";
                @endphp

                <a href="{{ route('feed', array_merge(request()->except(['filter', 'sort']), ['page' => 1])) }}" class="{{ $baseClasses }} {{ (!$currentFilter && !$currentSort) ? $activeClasses : $inactiveClasses }}">All</a>
                
                <a href="{{ route('feed', array_merge(request()->query(), ['filter' => 'solved', 'page' => 1])) }}" class="{{ $baseClasses }} {{ $currentFilter === 'solved' ? $activeClasses : $inactiveClasses }}"><i class='bx bx-check-circle mr-1'></i> Solved</a>
                <a href="{{ route('feed', array_merge(request()->query(), ['filter' => 'unsolved', 'page' => 1])) }}" class="{{ $baseClasses }} {{ $currentFilter === 'unsolved' ? $activeClasses : $inactiveClasses }}"><i class='bx bx-question-mark mr-1'></i> Unsolved</a>
                <a href="{{ route('feed', array_merge(request()->query(), ['filter' => 'no_answers', 'page' => 1])) }}" class="{{ $baseClasses }} {{ $currentFilter === 'no_answers' ? $activeClasses : $inactiveClasses }}"><i class='bx bx-message-square-x mr-1'></i> No Answers</a>
            
                {{-- SORTING DIVIDER --}}
                <div class="h-6 w-px bg-gray-300 mx-2"></div>

                <a href="{{ route('feed', array_merge(request()->query(), ['sort' => 'popular', 'page' => 1])) }}" class="{{ $baseClasses }} {{ $currentSort === 'popular' ? $activeClasses : $inactiveClasses }}"><i class='bx bx-trending-up mr-1'></i> Popular</a>
                <a href="{{ route('feed', array_merge(request()->query(), ['sort' => 'discussed', 'page' => 1])) }}" class="{{ $baseClasses }} {{ $currentSort === 'discussed' ? $activeClasses : $inactiveClasses }}"><i class='bx bx-comment-detail mr-1'></i> Hot</a>
            </div>

            @if(request('search'))
                <div class="mb-4 text-sm text-gray-500 font-light flex items-center">
                    <span>Showing results for: <strong>"{{ request('search') }}"</strong></span>
                    <a href="{{ route('feed') }}" class="ml-2 text-maroon-700 hover:underline flex items-center"><i class='bx bx-x'></i> Clear</a>
                </div>
            @endif

            <div id="feed-container" class="space-y-6 pb-12">
                @include('partials.question-list')
            </div>
        </main>

        {{-- PROFESSIONAL STICKY BOTTOM NAV (Mobile) --}}
        <nav class="lg:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-50 pb-safe">
            <div class="flex justify-between items-center h-16 px-6">
                {{-- 1. Home --}}
                <a href="{{ route('feed') }}" class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('feed') && !request('search') ? 'text-maroon-700' : 'text-gray-400 hover:text-gray-600' }}">
                    <i class='bx {{ request()->routeIs('feed') && !request('search') ? 'bxs-home' : 'bx-home-alt' }} text-2xl'></i>
                </a>

                {{-- 2. Topics (Drawer) --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="flex flex-col items-center justify-center space-y-1 {{ $mobileMenuOpen ?? false ? 'text-maroon-700' : 'text-gray-400 hover:text-gray-600' }}">
                    <i class='bx bx-category text-2xl'></i>
                </button>

                {{-- 3. Ask (Prominent Center) --}}
                <div class="relative -top-5">
                    <button 
                        @click="createModalOpen = true" 
                        class="w-14 h-14 bg-maroon-700 rounded-full flex items-center justify-center text-white shadow-lg shadow-maroon-700/40 border-4 border-gray-50 hover:scale-105 active:scale-95 transition-all duration-200"
                    >
                        <i class='bx bx-plus text-3xl'></i>
                    </button>
                </div>

                {{-- 4. Search (Toggle) --}}
                <button @click="searchOpen = !searchOpen" class="flex flex-col items-center justify-center space-y-1 {{ $searchOpen ?? false ? 'text-maroon-700' : 'text-gray-400 hover:text-gray-600' }}">
                    <i class='bx bx-search text-2xl'></i>
                </button>

                {{-- 5. Profile (Corrected: Links to Public Profile) --}}
                <a href="{{ route('user.profile', Auth::id()) }}" class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('user.profile') ? 'text-maroon-700' : 'text-gray-400 hover:text-gray-600' }}">
                    <i class='bx {{ request()->routeIs('user.profile') ? 'bxs-user' : 'bx-user' }} text-2xl'></i>
                </a>
            </div>
        </nav>

    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
        <script>hljs.highlightAll();</script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Select both desktop and mobile search inputs
                const searchInputs = document.querySelectorAll('.search-input-js');
                const feedContainer = document.getElementById('feed-container');
                let timeout = null;

                searchInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        clearTimeout(timeout);
                        const query = this.value;
                        
                        // Sync values between inputs
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
                            fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                feedContainer.innerHTML = html;
                            })
                            .catch(error => console.error('Error:', error));
                        }, 300);
                    });
                });
            });
        </script>
    @endpush
</x-public-layout>