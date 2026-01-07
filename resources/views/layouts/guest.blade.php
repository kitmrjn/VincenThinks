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
        
        {{-- WRAPPER: Flex Column on Mobile, Row on Desktop --}}
        <div class="min-h-screen flex flex-col lg:flex-row">
            
            {{-- DESKTOP SIDEBAR (Left - Hidden on Mobile) --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-maroon-700 to-maroon-900 text-white flex-col justify-between p-12 relative overflow-hidden">
                
                {{-- Background Pattern (Subtle SVG) --}}
                <div class="absolute inset-0 opacity-10">
                    <svg class="h-full w-full" width="100%" height="100%" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                        <defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M0 40L40 0H20L0 20M40 40V20L20 40" stroke="currentColor" stroke-width="2" fill="none"/></pattern></defs>
                        <rect width="100%" height="100%" fill="url(#grid)" />
                    </svg>
                </div>

                {{-- Content --}}
                <div class="relative z-10">
                    {{-- FIXED: Use route('home') instead of "/" --}}
                    <a href="{{ route('home') }}" class="block">
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

            {{-- MOBILE HEADER (Top - Visible only on Mobile) --}}
            <div class="lg:hidden w-full bg-gradient-to-br from-maroon-700 to-maroon-900 p-6 text-center relative overflow-hidden shrink-0 shadow-md">
                <div class="absolute inset-0 opacity-10">
                    <svg class="h-full w-full" width="100%" height="100%" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                        <rect width="100%" height="100%" fill="url(#grid)" />
                    </svg>
                </div>
                
                <div class="relative z-10 flex flex-col items-center justify-center">
                    {{-- FIXED: Use route('home') instead of "/" --}}
                    <a href="{{ route('home') }}">
                        <x-application-logo class="w-16 h-16 fill-current text-white" />
                    </a>
                    <h1 class="text-white text-lg font-bold mt-2 tracking-wide">Think. Ask. Learn.</h1>
                </div>
            </div>

            {{-- RIGHT SIDE: FORM PANEL --}}
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 bg-white flex-grow">
                <div class="w-full max-w-md space-y-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>