<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VincenThinks') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600&display=swap" rel="stylesheet" />
        
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Allow specific pages to push custom styles (like Highlight.js) --}}
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900 leading-normal tracking-normal flex flex-col min-h-screen" x-data="{ mobileMenuOpen: false }">
        
        @include('partials.navbar')

        <main class="flex-grow w-full">
            {{ $slot }}
        </main>
        
        {{-- Allow specific pages to push custom scripts --}}
        @stack('scripts')
    </body>
</html>