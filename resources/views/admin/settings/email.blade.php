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

    @include('admin.partials.sidebar')

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