<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Settings - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script>
        tailwind.config = { theme: { extend: { colors: { maroon: { 700: '#800000', 800: '#600000', 900: '#400000' } } } } }
    </script>
</head>
<body class="bg-gray-100 font-sans text-gray-600 antialiased min-h-screen flex">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-maroon-700 text-white flex flex-col fixed h-full shadow-xl z-10">
        <div class="h-16 flex items-center px-6 border-b border-maroon-800">
            <i class='bx bx-grid-alt mr-3 text-2xl'></i>
            <span class="font-bold text-lg tracking-wide">Administrator</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-lg transition">
                <i class='bx bx-error-circle mr-3 text-xl'></i><span class="font-medium">Reports</span>
            </a>
            <a href="{{ route('admin.categories') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-lg transition">
                <i class='bx bx-category mr-3 text-xl'></i><span class="font-medium">Categories</span>
            </a>
            
            {{-- DROPDOWN MENU --}}
            <div x-data="{ open: true }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-white bg-white/10 rounded-lg transition focus:outline-none">
                    <div class="flex items-center"><i class='bx bx-cog mr-3 text-xl'></i><span class="font-medium">Settings</span></div>
                    <i class='bx bx-chevron-down transition-transform' :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" class="pl-12 space-y-1 mt-1">
                    <a href="{{ route('admin.settings.general') }}" class="block py-2 text-sm text-white font-bold border-l-2 border-white pl-2">General Rules</a>
                    <a href="{{ route('admin.settings.email') }}" class="block py-2 text-sm text-gray-400 hover:text-white pl-2">Email Server</a>
                </div>
            </div>
        </nav>
        <div class="p-4 border-t border-maroon-800">
            <a href="/" class="flex items-center px-4 py-2 text-red-200 hover:text-white transition">
                <i class='bx bx-log-out-circle mr-3 text-xl'></i><span>Back to Site</span>
            </a>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 ml-64 p-8">
        <h1 class="text-3xl font-light text-gray-800 mb-8">General Rules</h1>
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Success</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.settings.general.update') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
            @csrf
            
            {{-- Time Limit --}}
            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-800 mb-1">Content Edit Time Limit</label>
                <p class="text-xs text-gray-500 mb-3">How many seconds a user has to edit their post after publishing.</p>
                <div class="flex items-center">
                    <input type="number" name="edit_time_limit" value="{{ $settings['edit_time_limit'] ?? '150' }}" class="w-32 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5 mr-2">
                    <span class="text-gray-500 text-sm">Seconds</span>
                </div>
            </div>

            <hr class="border-gray-100 mb-8">

            {{-- Verification Toggle --}}
            <div class="flex items-center justify-between mb-2">
                <div>
                    <label class="block text-sm font-bold text-gray-800">Require Email Verification</label>
                    <p class="text-xs text-gray-500 mt-1">If enabled, new users must verify their email before posting.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="verification_required" value="1" class="sr-only peer" {{ ($settings['verification_required'] ?? '1') == '1' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-maroon-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                </label>
            </div>

            <div class="mt-10 flex justify-end">
                <button type="submit" class="bg-maroon-700 hover:bg-maroon-800 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                    Save Changes
                </button>
            </div>
        </form>
    </main>
</body>
</html>