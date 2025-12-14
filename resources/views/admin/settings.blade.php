<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - VincenThinks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script>
        tailwind.config = { theme: { extend: { colors: { maroon: { 700: '#800000', 800: '#600000', 900: '#400000' } } } } }
    </script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

    @include('partials.navbar')

    <div class="max-w-4xl mx-auto px-4 py-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class='bx bx-cog mr-2 text-maroon-700'></i> System Settings
            </h1>
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-maroon-700 text-sm font-bold flex items-center">
                <i class='bx bx-arrow-back mr-1'></i> Back to Dashboard
            </a>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
                <i class='bx bx-check-circle text-xl mr-2'></i>
                <div>
                    <p class="font-bold">Success</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- MAIN FORM START --}}
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            {{-- 1. Include Email Settings --}}
            @include('admin.settings.email')

            {{-- 2. Include Forum Settings --}}
            @include('admin.settings.forum')

            {{-- 3. Future Settings (Easy to add here) --}}
            {{-- @include('admin.settings.security') --}}

            {{-- Save Button --}}
            <div class="flex justify-end pb-12">
                <button type="submit" class="bg-maroon-700 hover:bg-maroon-800 text-white font-bold py-3 px-8 rounded-lg shadow-lg flex items-center transform transition hover:-translate-y-0.5">
                    <i class='bx bx-save mr-2 text-xl'></i> Save Configuration
                </button>
            </div>

        </form>
    </div>

</body>
</html>