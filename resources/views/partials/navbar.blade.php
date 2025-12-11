<nav class="bg-maroon-700 shadow-lg sticky top-0 z-50">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="flex-shrink-0 flex items-center text-white text-2xl font-bold tracking-wider hover:text-gray-200 transition">
                    VincenThinks
                </a>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button onclick="toggleNotifications()" class="text-white hover:text-gray-200 relative p-1">
                            <i class='bx bx-bell text-xl'></i>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-red-100 bg-red-600 rounded-full">
                                    {{ Auth::user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-50 border border-gray-200">
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
                                    <div class="px-4 py-6 text-center text-gray-500 text-sm">
                                        No notifications yet.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center bg-white/10 border border-white/20 text-white px-4 py-1.5 rounded-full text-xs uppercase tracking-wider hover:bg-white hover:text-maroon-700 transition backdrop-blur-sm">
                            <i class='bx bx-grid-alt text-lg mr-1'></i> Admin
                        </a>
                    @endif

                    <div class="h-6 w-px bg-maroon-800 mx-2"></div>

                    <div class="text-white text-sm flex items-center">
                        <a href="{{ route('user.profile', Auth::id()) }}" class="hidden sm:block opacity-90 hover:opacity-100 mr-4 font-light hover:underline">
                            {{ Auth::user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-white hover:text-red-200 transition flex items-center opacity-80 hover:opacity-100" title="Log Out"><i class='bx bx-log-out-circle text-xl'></i></button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-gray-200 px-3 py-1 transition flex items-center font-light"><i class='bx bx-log-in-circle mr-1'></i> Log in</a>
                    <a href="{{ route('register') }}" class="bg-white text-maroon-700 px-3 py-1 rounded font-bold hover:bg-gray-200 transition shadow-sm ml-2 flex items-center"><i class='bx bx-user-plus mr-1'></i> Register</a>
                @endauth
            </div>
        </div>
    </div>
    
    <script>
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('hidden');
        }
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notificationDropdown');
            const button = document.querySelector('button[onclick="toggleNotifications()"]');
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</nav>