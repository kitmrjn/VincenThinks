<nav class="bg-maroon-700 shadow-lg sticky top-0 z-50" x-data="{ mobileNavOpen: false }">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            
            {{-- LEFT SIDE: Logo & Mobile Topic Context --}}
            <div class="flex items-center overflow-hidden">
                <a href="/" class="flex-shrink-0 flex items-center gap-3 text-white font-bold tracking-wider hover:text-gray-200 transition">
                    
                    {{-- NEW LOGO (Clean, no background) --}}
                    <img src="{{ asset('images/logo-no-text.svg') }}" class="h-10 w-10" alt="VT Logo">

                    {{-- Desktop Text (Hidden on mobile, visible on desktop) --}}
                    <span class="hidden lg:block text-xl">VincenThinks</span>
                </a>

                {{-- Mobile Topic Indicator (Shows what category is active) --}}
                <div class="lg:hidden flex flex-col ml-3 border-l border-white/20 pl-3 justify-center h-10">
                    <span class="text-[10px] text-white/60 uppercase font-bold leading-none tracking-wider">Browsing</span>
                    <span class="text-sm font-bold text-white leading-tight truncate max-w-[150px]">
                        @if(request('category'))
                            @php 
                                $currentCat = \App\Models\Category::find(request('category'));
                            @endphp
                            {{ $currentCat->acronym ?? $currentCat->name }}
                        @else
                            All Topics
                        @endif
                    </span>
                </div>
            </div>

            {{-- RIGHT SIDE: Icons & Profile --}}
            <div class="flex items-center space-x-1 sm:space-x-4">
                
                {{-- REMOVED: Mobile Filter Button (Redundant) --}}

                @auth
                    {{-- 2. Notifications --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-white hover:text-gray-200 relative p-2 rounded-full hover:bg-white/10 transition">
                            <i class='bx bx-bell text-xl align-middle'></i>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="absolute top-1 right-1 inline-flex items-center justify-center h-4 w-4 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">
                                    {{ Auth::user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- Notification Dropdown --}}
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-50 border border-gray-200 origin-top-right" style="display: none;">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-600 uppercase">Notifications</span>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[10px] text-maroon-700 hover:underline">Mark all read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                @forelse(Auth::user()->notifications as $notification)
                                    <a href="{{ route('notifications.read_one', $notification->id) }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition {{ $notification->read_at ? 'opacity-60' : 'bg-blue-50' }}">
                                        <p class="text-sm text-gray-800">{{ $notification->data['message'] }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </a>
                                @empty
                                    <div class="px-4 py-6 text-center text-gray-500 text-sm">No notifications yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- 3. Desktop Profile & Links --}}
                    <div class="hidden lg:flex items-center space-x-4 ml-2">
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center bg-white/10 border border-white/20 text-white px-3 py-1 rounded-full text-xs uppercase tracking-wider hover:bg-white hover:text-maroon-700 transition">
                                Admin
                            </a>
                        @endif
                        
                        <div class="text-white text-sm flex items-center">
                            <a href="{{ route('user.profile', Auth::id()) }}" class="opacity-90 hover:opacity-100 hover:underline font-light mr-4">
                                {{ Auth::user()->name }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-white hover:text-red-200 transition" title="Log Out"><i class='bx bx-log-out-circle text-xl align-middle'></i></button>
                            </form>
                        </div>
                    </div>

                    {{-- 4. Mobile Menu Button (Hamburger) --}}
                    <button @click="mobileNavOpen = !mobileNavOpen" class="lg:hidden text-white p-2 ml-1 hover:bg-white/10 rounded-full transition">
                        <i class='bx bx-menu text-2xl align-middle'></i>
                    </button>

                @else
                    {{-- Guest Links --}}
                    <div class="flex items-center">
                        <a href="{{ route('login') }}" class="text-white hover:text-gray-200 px-3 py-1 text-sm font-light">Log in</a>
                        <a href="{{ route('register') }}" class="bg-white text-maroon-700 px-3 py-1.5 rounded text-sm font-bold hover:bg-gray-100 transition shadow-sm ml-2">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- MOBILE NAVIGATION MENU (Standard Links) --}}
    <div x-show="mobileNavOpen" x-cloak class="lg:hidden bg-maroon-800 border-t border-maroon-900">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-maroon-700">Home</a>
            @auth
                <a href="{{ route('user.profile', Auth::id()) }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-maroon-700">My Profile</a>
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-maroon-700">Admin Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-red-200 hover:bg-maroon-700 hover:text-white">Log Out</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

{{-- EMAIL VERIFICATION BANNER --}}
@auth
    @if (!Auth::user()->hasVerifiedEmail())
        <div class="bg-red-600 text-white text-sm py-2 shadow-inner">
            <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
                <p class="flex items-center text-xs sm:text-sm">
                    <i class='bx bxs-error-circle text-xl mr-2'></i>
                    <span>Please verify your email address.</span>
                </p>
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-xs font-bold transition whitespace-nowrap ml-2">
                        Resend
                    </button>
                </form>
            </div>
        </div>
    @endif
@endauth