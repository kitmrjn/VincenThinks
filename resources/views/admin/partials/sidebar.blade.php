<aside class="w-64 bg-maroon-700 text-white flex flex-col fixed h-full shadow-xl z-10">
    <div class="h-16 flex items-center px-6 border-b border-maroon-800">
        <i class='bx bxs-dashboard mr-3 text-2xl'></i>
        <span class="font-bold text-lg tracking-wide">Administrator</span>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2">
        {{-- 1. Dashboard (Home) Link --}}
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bx-grid-alt mr-3 text-xl'></i><span class="font-medium">Dashboard</span>
        </a>

        {{-- 2. Analytics Link --}}
        <a href="{{ route('admin.analytics') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.analytics') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bx-bar-chart-alt-2 mr-3 text-xl'></i><span class="font-medium">Analytics</span>
        </a>

        {{-- 3. Reports (Moderation) Link --}}
        <a href="{{ route('admin.reports') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.reports') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bx-error-circle mr-3 text-xl'></i><span class="font-medium">Reports</span>
        </a>

        {{-- 4. Moderation Queue Link --}}
        <a href="{{ route('admin.moderation') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.moderation') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bx-shield-quarter mr-3 text-xl'></i><span class="font-medium">Moderation</span>
        </a>

        {{-- 5. Banned Words Link --}}
        <a href="{{ route('admin.banned_words') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.banned_words') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bx-block mr-3 text-xl'></i><span class="font-medium">Banned Words</span>
        </a>

        {{-- Users Link --}}
        <a href="{{ route('admin.users') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.users') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bxs-user-account mr-3 text-xl'></i><span class="font-medium">Users</span>
        </a>

        {{-- Audit Logs Link --}}
        <a href="{{ route('admin.audit_logs') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.audit_logs') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bx-list-ul mr-3 text-xl'></i><span class="font-medium">Audit Logs</span>
        </a>

        {{-- Categories Link --}}
        <a href="{{ route('admin.categories') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.categories') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bx-category mr-3 text-xl'></i><span class="font-medium">Categories</span>
        </a>

        {{-- Courses Link --}}
        <a href="{{ route('admin.courses') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.courses') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bxs-graduation mr-3 text-xl'></i><span class="font-medium">Courses</span>
        </a>

        {{-- Departments Link --}}
        <a href="{{ route('admin.departments') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.departments') ? 'bg-white/10 text-white font-bold' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class='bx bx-buildings mr-3 text-xl'></i><span class="font-medium">Departments</span>
        </a>

        {{-- Settings Dropdown --}}
        <div x-data="{ open: {{ request()->is('admin/settings*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition focus:outline-none {{ request()->is('admin/settings*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <div class="flex items-center"><i class='bx bx-cog mr-3 text-xl'></i><span class="font-medium">Settings</span></div>
                <i class='bx bx-chevron-down transition-transform' :class="open ? 'rotate-180' : ''"></i>
            </button>
            
            <div x-show="open" class="pl-12 space-y-1 mt-1">
                <a href="{{ route('admin.settings.general') }}" 
                   class="block py-2 text-sm pl-2 transition {{ request()->routeIs('admin.settings.general') ? 'text-white font-bold border-l-2 border-white' : 'text-gray-400 hover:text-white' }}">
                    General Rules
                </a>
                <a href="{{ route('admin.settings.email') }}" 
                   class="block py-2 text-sm pl-2 transition {{ request()->routeIs('admin.settings.email') ? 'text-white font-bold border-l-2 border-white' : 'text-gray-400 hover:text-white' }}">
                    Email Server
                </a>
            </div>
        </div>
    </nav>

    <div class="p-4 border-t border-maroon-800">
        <a href="/" class="flex items-center px-4 py-2 text-red-200 hover:text-white transition">
            <i class='bx bx-log-out-circle mr-3 text-xl'></i><span>Back to Site</span>
        </a>
    </div>
</aside>