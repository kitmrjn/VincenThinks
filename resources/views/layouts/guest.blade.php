<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VincenThinks') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:300,400,600,700&display=swap" rel="stylesheet" />
        
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-white">
        
        <div class="min-h-screen flex">
            
            {{-- LEFT SIDE: BRANDING PANEL (Hidden on Mobile, Visible on Desktop) --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-maroon-700 to-maroon-900 text-white flex-col justify-between p-12 relative overflow-hidden">
                
                {{-- Background Pattern (Subtle) --}}
                <div class="absolute inset-0 opacity-10">
                    <svg class="h-full w-full" width="100%" height="100%" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                        <defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M0 40L40 0H20L0 20M40 40V20L20 40" stroke="currentColor" stroke-width="2" fill="none"/></pattern></defs>
                        <rect width="100%" height="100%" fill="url(#grid)" />
                    </svg>
                </div>

                {{-- Content --}}
                <div class="relative z-10">
                    <a href="/" class="block">
                         {{-- Logo: We force white fill via text-white class on the component --}}
                        <x-application-logo class="w-20 h-20 fill-current text-white mb-6" />
                    </a>
                    <h1 class="text-5xl font-bold tracking-tight mb-4">Think. Ask. Learn.</h1>
                    <p class="text-maroon-100 text-lg font-light max-w-md leading-relaxed">
                        Join the academic community where questions find answers and knowledge grows through collaboration.
                    </p>
                </div>

                <div class="relative z-10 text-sm text-maroon-200 font-light">
                    &copy; {{ date('Y') }} VincenThinks. All rights reserved.
                </div>
            </div>

            {{-- RIGHT SIDE: FORM PANEL --}}
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 bg-white overflow-y-auto relative">
                
                {{-- Mobile Logo (Visible only on small screens) --}}
                <div class="lg:hidden mb-8">
                    <a href="/">
                        <x-application-logo class="w-16 h-16 fill-current text-maroon-700" />
                    </a>
                </div>

                {{-- Form Container --}}
                <div class="w-full max-w-md space-y-6">
                    {{ $slot }}
                </div>
                
            </div>
        </div>
    </body>
</html>