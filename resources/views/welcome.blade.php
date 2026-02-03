<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'VincenThinks') }} - The Knowledge Hub</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .hero-pattern {
            background-image: radial-gradient(#c81e1e 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.05;
        }
    </style>
</head>
<body class="font-sans text-gray-700 antialiased bg-white selection:bg-maroon-700 selection:text-white">

    {{-- NAVIGATION --}}
    <nav x-data="{ mobileMenuOpen: false, scrolled: false }" 
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="{ 'bg-white/90 backdrop-blur-md shadow-sm': scrolled, 'bg-transparent': !scrolled }"
         class="fixed top-0 w-full z-50 transition-all duration-300 border-b border-transparent"
         :class="{ 'border-gray-100': scrolled }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ route('landing') }}" class="flex-shrink-0 flex items-center gap-3">
                    <img src="{{ asset('images/logo-no-text.svg') }}" alt="Logo" class="h-10 w-auto">
                    <span class="font-bold text-2xl tracking-tight text-maroon-700">VincenThinks</span>
                </a>

                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#features" class="text-sm font-medium text-gray-500 hover:text-maroon-700 transition">Features</a>
                    <a href="#stats" class="text-sm font-medium text-gray-500 hover:text-maroon-700 transition">Impact</a>
                    <a href="#departments" class="text-sm font-medium text-gray-500 hover:text-maroon-700 transition">Departments</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">Log in</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-maroon-700 text-white text-sm font-semibold rounded-xl hover:bg-maroon-800 transition shadow-lg shadow-maroon-700/20 transform hover:-translate-y-0.5">
                        Get Started
                    </a>
                </div>

                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 hover:text-maroon-700 focus:outline-none">
                        <i class='bx bx-menu text-3xl'></i>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             class="md:hidden absolute top-20 left-0 w-full bg-white shadow-lg border-b border-gray-100 p-4 flex flex-col space-y-4">
            <a href="#features" @click="mobileMenuOpen = false" class="block text-base font-medium text-gray-600 hover:text-maroon-700">Features</a>
            <a href="#stats" @click="mobileMenuOpen = false" class="block text-base font-medium text-gray-600 hover:text-maroon-700">Impact</a>
            <hr class="border-gray-100">
            <a href="{{ route('login') }}" class="block text-center w-full py-3 text-gray-600 font-medium border border-gray-200 rounded-xl hover:bg-gray-50">Log in</a>
            <a href="{{ route('register') }}" class="block text-center w-full py-3 bg-maroon-700 text-white font-bold rounded-xl shadow-lg shadow-maroon-700/20">Sign Up Now</a>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 hero-pattern z-0 pointer-events-none"></div>
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-maroon-100 rounded-full blur-3xl opacity-50 z-0"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-orange-100 rounded-full blur-3xl opacity-50 z-0"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center lg:text-left flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-maroon-50 border border-maroon-100 text-maroon-700 text-xs font-bold uppercase tracking-wider mb-6">
                    <span class="w-2 h-2 rounded-full bg-maroon-700 animate-pulse"></span>
                    AI-Enhanced • Real-Time • Secure
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight mb-6">
                    The AI-Powered Knowledge Hub for <br>
                    <span class="text-maroon-700 bg-clip-text text-transparent bg-gradient-to-r from-maroon-700 to-red-500">
                        St. Vincent College of Cabuyao.
                    </span>
                </h1>
                <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    Bridge the gap between JHS, SHS, and College. Connect with Faculty and Students in a secure, moderated environment when face-to-face consultation isn't possible.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('feed') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white bg-maroon-700 rounded-2xl hover:bg-maroon-800 transition shadow-xl shadow-maroon-700/20 transform hover:-translate-y-1">
                        Start Asking
                        <i class='bx bx-right-arrow-alt ml-2 text-xl'></i>
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-gray-700 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 hover:border-gray-300 transition">
                        Learn More
                    </a>
                </div>
                <div class="mt-8 flex items-center justify-center lg:justify-start gap-4 text-sm text-gray-500">
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs font-bold text-gray-600">J</div>
                        <div class="w-8 h-8 rounded-full bg-gray-300 border-2 border-white flex items-center justify-center text-xs font-bold text-gray-600">S</div>
                        <div class="w-8 h-8 rounded-full bg-gray-400 border-2 border-white flex items-center justify-center text-xs font-bold text-gray-600">C</div>
                    </div>
                    <p>Connecting <strong>JHS, SHS, & College</strong> students.</p>
                </div>
            </div>

            <div class="lg:w-1/2 relative">
                <div class="relative rounded-2xl bg-white shadow-2xl border border-gray-200 p-2 transform rotate-2 hover:rotate-0 transition duration-500 ease-out">
                    <div class="rounded-xl overflow-hidden bg-gray-50 aspect-[4/3] flex flex-col relative group">
                        {{-- Top Bar of the Card --}}
                        <div class="h-10 bg-white border-b border-gray-100 flex items-center px-4 gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            {{-- Dynamic Content: Latest Solved Question --}}
                            @if($latestSolved)
                                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                    <div class="flex gap-3 mb-2">
                                        @if($latestSolved->user->avatar)
                                            <img src="{{ asset('storage/' . $latestSolved->user->avatar) }}" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-maroon-100 flex items-center justify-center text-maroon-700 text-xs font-bold">
                                                {{ substr($latestSolved->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="space-y-1">
                                            <p class="text-sm font-bold text-gray-800">{{ $latestSolved->user->name }}</p>
                                            <div class="w-16 h-2 bg-gray-100 rounded"></div>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2 font-medium truncate">{{ $latestSolved->title }}</p>
                                    <div class="w-3/4 h-2 bg-gray-100 rounded"></div>
                                </div>
                            @else
                                {{-- Fallback if no questions exist yet --}}
                                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                    <div class="flex gap-3 mb-2">
                                        <div class="w-8 h-8 rounded-full bg-maroon-100"></div>
                                        <div class="space-y-1">
                                            <div class="w-24 h-3 bg-gray-200 rounded"></div>
                                            <div class="w-16 h-2 bg-gray-100 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="w-full h-4 bg-gray-100 rounded mb-2"></div>
                                    <div class="w-3/4 h-4 bg-gray-100 rounded"></div>
                                </div>
                            @endif

                            {{-- Visual "Reply" representation --}}
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 opacity-60">
                                <div class="flex gap-3 mb-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100"></div>
                                    <div class="space-y-1">
                                        <div class="w-20 h-3 bg-gray-200 rounded"></div>
                                        <div class="w-12 h-2 bg-gray-100 rounded"></div>
                                    </div>
                                </div>
                                <div class="w-full h-4 bg-gray-100 rounded mb-2"></div>
                                <div class="w-1/2 h-4 bg-gray-100 rounded"></div>
                            </div>
                        </div>
                        
                        {{-- Solved Badge --}}
                        <div class="absolute bottom-6 right-6 bg-white px-4 py-2 rounded-lg shadow-lg border border-gray-100 flex items-center gap-3 animate-bounce">
                            <div class="bg-green-100 p-1.5 rounded-full text-green-600"><i class='bx bx-check'></i></div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase">Status</p>
                                <p class="text-sm font-bold text-gray-800">Solved</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SOCIAL PROOF STATS --}}
    <section id="stats" class="bg-maroon-700 py-12 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-maroon-600/50">
                <div class="space-y-1">
                    <p class="text-4xl font-extrabold">Live</p>
                    <p class="text-maroon-200 text-sm font-medium">Real-Time Analytics</p>
                </div>
                <div class="space-y-1">
                    <p class="text-4xl font-extrabold">24/7</p>
                    <p class="text-maroon-200 text-sm font-medium">Automated Moderation</p>
                </div>
                <div class="space-y-1">
                    <p class="text-4xl font-extrabold">{{ $stats['courses_count'] }}+</p>
                    <p class="text-maroon-200 text-sm font-medium">Active Courses & Strands</p>
                </div>
                <div class="space-y-1">
                    <p class="text-4xl font-extrabold">{{ $stats['education_levels'] }}</p>
                    <p class="text-maroon-200 text-sm font-medium">Education Levels (JHS-Col)</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES GRID --}}
    <section id="features" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-base font-bold text-maroon-700 tracking-wide uppercase mb-2">Why VincenThinks?</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Everything you need to ace your semester.</h3>
                <p class="text-gray-500 text-lg">We've built a platform that encourages collaboration, ensures accuracy, and rewards participation.</p>
            </div>

            {{-- UPDATED: Grid is now 3 columns on large screens, and we have 6 items to fill it perfectly --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                {{-- Feature 1: Category Focused --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 text-3xl mb-6">
                        <i class='bx bx-category'></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Category Focused</h4>
                    <p class="text-gray-500 leading-relaxed">
                        Tailored for {{ $departments ?: 'various academic strands' }}. Find materials specific to your strand.
                    </p>
                </div>

                {{-- Feature 2: AI Auto-Moderation --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 bg-green-50 rounded-xl flex items-center justify-center text-green-600 text-3xl mb-6">
                        <i class='bx bx-shield-quarter'></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">AI Auto-Moderation</h4>
                    <p class="text-gray-500 leading-relaxed">Our AI automatically filters inappropriate content instantly, ensuring a safe space for academic growth.</p>
                </div>

                {{-- Feature 3: Smart Search --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 bg-pink-50 rounded-xl flex items-center justify-center text-pink-600 text-3xl mb-6">
                        <i class='bx bx-search-alt'></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Smart Search</h4>
                    <p class="text-gray-500 leading-relaxed">Don't ask the same question twice. Our powerful search helps you find existing solutions instantly.</p>
                </div>

                {{-- Feature 4: Mobile Ready --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 bg-maroon-50 rounded-xl flex items-center justify-center text-maroon-700 text-3xl mb-6">
                        <i class='bx bx-mobile'></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Mobile Ready</h4>
                    <p class="text-gray-500 leading-relaxed">Study on the go. VincenThinks is fully responsive and optimized for your phone or tablet.</p>
                </div>

                {{-- [NEW] Feature 5: Reputation System --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-600 text-3xl mb-6">
                        <i class='bx bx-trophy'></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Community Reputation</h4>
                    <p class="text-gray-500 leading-relaxed">Earn recognition for helpful answers. Stand out as a top contributor in your department.</p>
                </div>

                {{-- [NEW] Feature 6: Instant Notifications --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 text-3xl mb-6">
                        <i class='bx bx-bell'></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Instant Alerts</h4>
                    <p class="text-gray-500 leading-relaxed">Never miss a reply. Get real-time alerts when someone answers your question or marks it as solved.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="py-20 relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="bg-maroon-700 rounded-3xl p-10 md:p-16 text-center shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-64 h-64 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-64 h-64 bg-white opacity-10 rounded-full blur-2xl"></div>
                
                <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Ready to improve your grades?</h2>
                <p class="text-maroon-100 text-lg mb-8 max-w-2xl mx-auto">Join thousands of students and teachers building the smartest campus community today.</p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-maroon-700 font-bold rounded-2xl hover:bg-gray-100 transition shadow-lg">
                        Create Free Account
                    </a>
                    <a href="{{ route('feed') }}" class="px-8 py-4 bg-maroon-800 text-white font-bold rounded-2xl hover:bg-maroon-900 transition border border-maroon-600">
                        Browse Questions
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-gray-100 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-12">
                <div class="flex items-center gap-3 mb-4 md:mb-0">
                    <img src="{{ asset('images/logo-no-text.svg') }}" alt="Logo" class="h-8 w-auto">
                    <span class="font-bold text-lg text-maroon-700">VincenThinks</span>
                </div>
                
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-maroon-700 transition text-2xl"><i class='bx bxl-facebook'></i></a>
                    <a href="#" class="text-gray-400 hover:text-maroon-700 transition text-2xl"><i class='bx bxl-twitter'></i></a>
                    <a href="#" class="text-gray-400 hover:text-maroon-700 transition text-2xl"><i class='bx bxl-instagram'></i></a>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} VincenThinks. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-gray-900 transition">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-900 transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>