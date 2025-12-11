<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VincenThinks - Home</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <script>
        tailwind.config = { theme: { extend: { colors: { maroon: { 700: '#800000', 800: '#600000', 900: '#400000' } } } } }
    </script>
    <style>
        .line-clamp-3 pre { overflow: hidden; }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal flex flex-col min-h-screen">

    @include('partials.navbar')

    <div class="flex-grow max-w-3xl mx-auto w-full mt-8 px-4">

        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 mb-6 shadow-sm flex items-center">
                <i class='bx bx-check-circle text-xl mr-2'></i> {{ session('success') }}
            </div>
        @endif

        @auth
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-maroon-700"></div>
            <h3 class="text-xl font-light text-gray-800 mb-4 flex items-center">
                <i class='bx bx-edit text-2xl text-maroon-700 mr-2 font-thin'></i>
                Ask the Community
            </h3>
            
            <form action="{{ route('question.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <select name="category_id" required class="w-full border-b border-gray-200 bg-transparent p-2 mb-4 focus:border-maroon-700 focus:outline-none text-sm font-light text-gray-600 cursor-pointer">
                    <option value="" disabled selected>Select a Category...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <input type="text" name="title" placeholder="What's your question?" required class="w-full border-b border-gray-200 bg-transparent p-2 mb-4 focus:border-maroon-700 focus:outline-none text-lg font-normal transition-colors placeholder-gray-400">
                
                <textarea name="content" placeholder="Type here... Use ``` for code blocks!" required class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 mb-2 h-24 focus:outline-none focus:ring-1 focus:ring-maroon-700 focus:border-maroon-700 transition resize-none text-sm font-light"></textarea>
                
                <div class="mb-4 flex items-center">
                    <label class="cursor-pointer flex items-center text-xs text-gray-500 hover:text-maroon-700 transition">
                        <i class='bx bx-images text-lg mr-1'></i> Add Images (Optional)
                        <input type="file" name="images[]" multiple class="hidden" 
                               onchange="document.getElementById('img-preview-count').innerText = this.files.length + ' files selected'">
                    </label>
                    <span id="img-preview-count" class="ml-3 text-xs text-maroon-700 font-bold"></span>
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-maroon-700 text-white px-6 py-2 rounded-lg font-normal hover:bg-maroon-800 transition shadow-sm flex items-center ml-auto tracking-wide"><i class='bx bx-send mr-2'></i> Post Question</button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-white border-l-4 border-blue-400 text-gray-600 p-6 mb-8 rounded-lg shadow-sm flex items-center">
            <i class='bx bx-info-circle text-3xl text-blue-400 mr-4 font-thin'></i>
            <div>
                <p class="font-normal text-lg text-gray-800">Join the conversation!</p>
                <p class="text-sm font-light mt-1">Please <a href="{{ route('login') }}" class="underline hover:text-blue-600">login</a> to ask questions.</p>
            </div>
        </div>
        @endauth

        <div class="flex flex-col sm:flex-row items-center justify-between mb-6 space-y-4 sm:space-y-0">
            <h2 class="text-2xl font-light text-gray-800 flex items-center"><i class='bx bx-message-square-dots text-maroon-700 mr-2 font-thin'></i> Recent Discussions</h2>
            <form action="/" method="GET" class="relative w-full sm:w-64">
                <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg'></i>
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-full focus:outline-none focus:border-maroon-700 focus:ring-1 focus:ring-maroon-700 text-sm font-light bg-white shadow-sm transition">
            </form>
        </div>

        @if(request('search'))
            <div class="mb-4 text-sm text-gray-500 font-light flex items-center">
                <span>Showing results for: <strong>"{{ request('search') }}"</strong></span>
                <a href="/" class="ml-2 text-maroon-700 hover:underline flex items-center"><i class='bx bx-x'></i> Clear</a>
            </div>
        @endif
        
        <div class="space-y-4 pb-12">
            @forelse($questions as $q)
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition duration-200 hover:border-maroon-200 group relative">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-4">
                            @if($q->user->avatar)
                                <img src="{{ asset('storage/' . $q->user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="{{ $q->user->name }}">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 text-sm font-bold">
                                    {{ substr($q->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-grow min-w-0">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center text-xs text-gray-400 font-light">
                                    <a href="{{ route('user.profile', $q->user->id) }}" class="font-medium text-gray-600 mr-2 hover:underline hover:text-maroon-700">{{ $q->user->name }}</a>
                                    <span class="mx-1">•</span>
                                    <span>{{ $q->created_at->diffForHumans() }}</span>
                                    @if($q->category)
                                        <span class="mx-2 text-gray-300">|</span>
                                        <span class="bg-gray-100 text-maroon-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-gray-200">{{ $q->category->name }}</span>
                                    @endif
                                </div>

                                @if($q->best_answer_id)
                                    <span class="flex-shrink-0 bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide flex items-center border border-green-200">
                                        <i class='bx bx-check mr-1'></i> Solved
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('question.show', $q->id) }}" class="block group-hover:text-maroon-700 transition-colors">
                                
                                <h3 class="text-lg font-normal text-gray-800 leading-tight mb-2">{{ $q->title }}</h3>
                                
                                @if($q->images->count() > 0)
                                    <div class="mb-3 mt-2 rounded overflow-hidden h-48 w-full border border-gray-200">
                                        @if($q->images->count() == 1)
                                            <img src="{{ asset('storage/' . $q->images->first()->image_path) }}" class="w-full h-full object-cover" alt="Question Image">
                                        @else
                                            <div class="grid grid-cols-2 gap-0.5 h-full">
                                                <div class="h-full">
                                                    <img src="{{ asset('storage/' . $q->images[0]->image_path) }}" class="w-full h-full object-cover" alt="Image 1">
                                                </div>
                                                <div class="h-full relative">
                                                    <img src="{{ asset('storage/' . $q->images[1]->image_path) }}" class="w-full h-full object-cover" alt="Image 2">
                                                    @if($q->images->count() > 2)
                                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                                            <span class="text-white font-bold text-lg">+{{ $q->images->count() - 2 }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="prose prose-sm prose-stone text-gray-500 line-clamp-3">
                                    {!! Str::markdown($q->content) !!}
                                </div>
                            </a>
                            
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-400 font-light">
                                    <span class="flex items-center">
                                        <i class='bx bx-message-alt mr-1 text-base'></i> {{ $q->answers->count() }} Answers
                                    </span>
                                    <span class="flex items-center">
                                        <i class='bx bx-show mr-1 text-lg'></i> {{ $q->views ?? 0 }} Views
                                    </span>
                                </div>

                                @auth
                                    @if((Auth::id() === $q->user_id || Auth::user()->is_admin) && $q->created_at > now()->subSeconds(150))
                                        <form action="{{ route('question.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-300 hover:text-red-500 transition" title="Delete Question">
                                                <i class='bx bx-trash text-lg'></i>
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                    <i class='bx bx-search-alt text-4xl text-gray-300 mb-2'></i>
                    <p class="text-gray-500 font-light">No questions found.</p>
                </div>
            @endforelse
        </div>
    </div>

</body>
</html>