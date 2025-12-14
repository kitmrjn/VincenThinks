<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Settings - Admin</title>
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
                    <a href="{{ route('admin.settings.general') }}" class="block py-2 text-sm text-gray-400 hover:text-white pl-2">General Rules</a>
                    <a href="{{ route('admin.settings.email') }}" class="block py-2 text-sm text-white font-bold border-l-2 border-white pl-2">Email Server</a>
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
        <h1 class="text-3xl font-light text-gray-800 mb-8">Email Server Configuration</h1>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Success</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.settings.email.update') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-3xl">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Mailer --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Mailer Driver</label>
                    <select name="mail_mailer" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5">
                        <option value="smtp" {{ ($settings['mail_mailer'] ?? '') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="log" {{ ($settings['mail_mailer'] ?? '') == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                    </select>
                </div>

                {{-- Encryption --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Encryption</label>
                    <select name="mail_encryption" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5">
                        <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="null" {{ ($settings['mail_encryption'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                {{-- Host --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">SMTP Host</label>
                    <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.gmail.com" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5">
                </div>

                {{-- Port --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Port</label>
                    <input type="text" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5">
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Username</label>
                    <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5">
                </div>

                {{-- Password --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Password / App Key</label>
                    <input type="password" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}" placeholder="Enter new password to update..." class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5">
                </div>

                {{-- From Address --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">From Address</label>
                    <input type="text" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5">
                </div>

                {{-- From Name --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">From Name</label>
                    <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5">
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-maroon-700 hover:bg-maroon-800 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                    Save Configuration
                </button>
            </div>
        </form>
    </main>
</body>
</html>