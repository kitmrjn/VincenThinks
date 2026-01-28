<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600&display=swap" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        @include('admin.partials.sidebar')

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden transition-all duration-300 md:ml-64">
            
            <header class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200 md:hidden sticky top-0 z-20 shadow-sm">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="text-gray-500 hover:text-maroon-700 focus:outline-none transition p-1 rounded-md hover:bg-gray-100">
                        <i class='bx bx-menu text-3xl'></i>
                    </button>
                    
                    <span class="font-bold text-gray-800 text-lg tracking-tight">Administrator</span>
                </div>

                </header>

            <main class="w-full flex-grow p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>