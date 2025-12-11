<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $question->title }} - VincenThinks</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script>
        tailwind.config = { theme: { extend: { colors: { maroon: { 700: '#800000', 800: '#600000' } } } } }
    </script>
    <style>
        .fade-enter-active { transition: all 0.3s ease; }
        .prose { max-width: none; }
        pre { border-radius: 0.5rem; }
    </style>
</head>
<body class="bg-gray-100 font-sans min-h-screen relative">

    @include('partials.navbar')

    <div class="max-w-3xl mx-auto mt-6 px-4">
        @if ($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200 mb-6 shadow-sm flex items-start">
                <i class='bx bx-error-circle text-xl mr-2 mt-0.5'></i>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        @if(session('message'))
            <div class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 mb-6 shadow-sm flex items-center"><i class='bx bx-check-circle text-xl mr-2'></i> {{ session('message') }}</div>
        @endif
        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 mb-6 shadow-sm flex items-center"><i class='bx bx-check-circle text-xl mr-2'></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200 mb-6 shadow-sm flex items-center"><i class='bx bx-block text-xl mr-2'></i> {{ session('error') }}</div>
        @endif
    </div>

    <div class="max-w-3xl mx-auto px-4 pb-12">

        <div class="bg-white rounded-xl shadow-sm p-8 mb-8 border border-gray-200 relative">
            
            <div class="flex justify-between items-start mb-6">
                
                <div class="flex items-center">
                    <div class="flex-shrink-0 mr-3">
                        @if($question->user->avatar)
                            <img src="{{ asset('storage/' . $question->user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="{{ $question->user->name }}">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 text-sm font-bold">
                                {{ substr($question->user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="text-sm">
                        <a href="{{ route('user.profile', $question->user->id) }}" class="font-bold text-gray-800 hover:text-maroon-700 hover:underline transition">
                            {{ $question->user->name }}
                        </a>
                        <div class="text-xs text-gray-500 font-light flex items-center flex-wrap">
                            <span>{{ $question->created_at->diffForHumans() }}</span>
                            @if($question->created_at != $question->updated_at)
                                <span class="mx-1">•</span>
                                <span class="italic" title="Edited {{ $question->updated_at->diffForHumans() }}">(edited)</span>
                            @endif
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="flex items-center text-gray-400">
                                <i class='bx bx-show mr-1'></i> {{ $question->views ?? 0 }} Views
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    
                    @if($question->best_answer_id)
                        <span class="bg-green-100 text-green-700 border border-green-200 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide flex items-center shadow-sm">
                            <i class='bx bx-check mr-1 text-base'></i> Solved
                        </span>
                    @endif

                    @auth
                        @if((Auth::id() === $question->user_id || Auth::user()->is_admin) && $question->created_at > now()->subSeconds(150))
                            <a href="{{ route('question.edit', $question->id) }}" class="text-gray-300 hover:text-blue-600 transition p-1" title="Edit">
                                <i class='bx bx-pencil text-2xl font-thin'></i>
                            </a>
                        @endif

                        @if(Auth::id() === $question->user_id || Auth::user()->is_admin)
                            <form action="{{ route('question.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Delete your question?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-red-600 transition p-1" title="Delete"><i class='bx bx-trash text-2xl font-thin'></i></button>
                            </form>
                        @endif
                        @if(Auth::id() !== $question->user_id)
                            <button onclick="openReportModal()" class="text-gray-300 hover:text-yellow-600 transition p-1" title="Report">
                                <i class='bx bx-error-circle text-2xl font-thin'></i>
                            </button>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="mb-4">
                @if($question->category)
                    <span class="bg-gray-100 text-maroon-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-gray-200 mb-2 inline-block">
                        {{ $question->category->name }}
                    </span>
                @endif
                <h1 class="text-3xl font-light text-gray-900 leading-tight">{{ $question->title }}</h1>
            </div>

            @if($question->images->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-6">
                    @foreach($question->images as $img)
                        <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank" class="block w-full aspect-square">
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover rounded border border-gray-200 hover:opacity-90 transition" alt="Question Image">
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="prose prose-stone prose-a:text-maroon-700 hover:prose-a:text-maroon-800 text-gray-800 leading-relaxed mb-6">
                {!! Str::markdown($question->content) !!}
            </div>
        </div>

        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-light text-gray-800 flex items-center"><i class='bx bx-message-alt-detail mr-2 text-maroon-700 font-thin'></i> {{ $question->answers->count() }} Answers</h3>
            <div class="h-px bg-gray-200 flex-grow ml-4"></div>
        </div>

        <div class="space-y-6">
        @foreach($question->answers as $answer)
            @php 
                $isBest = $question->best_answer_id === $answer->id;
                $isTopRated = isset($topRatedAnswerId) && $topRatedAnswerId === $answer->id;
                
                // Border Logic: Green for Best, Gold for Top Rated, or Gray
                $borderClass = $isBest 
                    ? 'border-green-500 ring-1 ring-green-500 bg-green-50/10' 
                    : ($isTopRated ? 'border-yellow-400 ring-1 ring-yellow-400' : 'border-gray-200');
            @endphp

            <div class="bg-white rounded-xl shadow-sm p-6 border {{ $borderClass }} relative group transition-all">
                
                <div class="absolute -top-3 left-6 flex space-x-2">
                    @if($isBest)
                        <div class="bg-green-100 text-green-700 text-[10px] font-bold px-3 py-1 rounded-full border border-green-300 shadow-sm flex items-center">
                            <i class='bx bx-check-circle text-base mr-1'></i> Accepted Solution
                        </div>
                    @endif

                    @if($isTopRated)
                        <div class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-3 py-1 rounded-full border border-yellow-300 shadow-sm flex items-center">
                            <i class='bx bxs-trophy text-base mr-1'></i> Top Rated
                        </div>
                    @endif
                </div>

                <div class="absolute top-4 right-4 flex items-center space-x-1">
                    @auth
                        @if(Auth::id() === $question->user_id)
                            <form action="{{ route('answer.best', $answer->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-1 rounded-full transition {{ $isBest ? 'text-green-600 bg-green-50 hover:bg-green-100' : 'text-gray-300 hover:text-green-600 hover:bg-gray-50' }}" title="{{ $isBest ? 'Unmark as Solution' : 'Mark as Accepted Solution' }}">
                                    <i class='bx bx-check text-2xl'></i>
                                </button>
                            </form>
                        @endif
                    
                        @if((Auth::id() === $answer->user_id || Auth::user()->is_admin) && $answer->created_at > now()->subSeconds(150))
                            <a href="{{ route('answer.edit', $answer->id) }}" class="text-gray-300 hover:text-blue-600 transition p-1" title="Edit Answer">
                                <i class='bx bx-pencil text-xl'></i>
                            </a>
                        @endif

                        @if(Auth::id() === $answer->user_id || Auth::user()->is_admin)
                            <form action="{{ route('answer.destroy', $answer->id) }}" method="POST" onsubmit="return confirm('Delete answer?');" class="opacity-0 group-hover:opacity-100 transition">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-red-500 transition p-1" title="Delete Answer"><i class='bx bx-trash text-xl'></i></button>
                            </form>
                        @endif
                    @endauth
                </div>

                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center">
                         @if($answer->user->avatar)
                            <img src="{{ asset('storage/' . $answer->user->avatar) }}" class="w-8 h-8 rounded-full object-cover border border-gray-200 mr-3" alt="{{ $answer->user->name }}">
                         @else
                            <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold mr-3">{{ substr($answer->user->name, 0, 1) }}</div>
                         @endif
                        <div>
                            <a href="{{ route('user.profile', $answer->user->id) }}" class="block font-medium text-gray-800 text-sm hover:underline">{{ $answer->user->name }}</a>
                            <span class="text-xs text-gray-400 font-light">
                                {{ $answer->created_at->diffForHumans() }}
                                @if($answer->created_at != $answer->updated_at)
                                    <span class="italic ml-1" title="Edited {{ $answer->updated_at->diffForHumans() }}">(edited)</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="prose prose-sm prose-stone mb-4 ml-11">
                    {!! Str::markdown($answer->content) !!}
                </div>
                
                <div class="ml-11 flex items-center mb-4">
                    <div class="flex items-center bg-gray-50 rounded-lg p-2 inline-flex border border-gray-200 mr-4">
                        <div class="flex items-center text-yellow-500 font-bold mr-3"><i class='bx bxs-star mr-1 text-lg'></i><span class="text-gray-700">{{ number_format($answer->ratings->avg('score'), 1) }}</span></div>
                        @auth
                            <div class="h-4 w-px bg-gray-300 mr-3"></div>
                            @if(Auth::id() === $answer->user_id)
                                <div class="flex items-center text-xs text-gray-400 font-light italic">(Your Answer)</div>
                            @else
                                @php $userRating = $answer->ratings->where('user_id', Auth::id())->first(); @endphp
                                @if($userRating)
                                    <div class="flex items-center text-xs text-maroon-700 font-medium"><i class='bx bx-check mr-1 text-lg'></i> Rated: {{ $userRating->score }}</div>
                                @else
                                    <form action="{{ route('answer.rate', $answer->id) }}" method="POST" class="flex items-center text-sm">
                                        @csrf
                                        <select name="score" class="border-none bg-transparent text-gray-600 text-xs font-bold focus:ring-0 cursor-pointer mr-2 p-0"><option value="5">5</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option></select>
                                        <button type="submit" class="text-maroon-700 hover:text-maroon-900 text-xs font-bold uppercase tracking-wide">Rate</button>
                                    </form>
                                @endif
                            @endif
                        @endauth
                    </div>
                    @auth
                        <button onclick="toggleReplyForm('main-reply-form-{{ $answer->id }}')" class="text-gray-400 hover:text-maroon-700 text-xs font-medium flex items-center transition"><i class='bx bx-reply mr-1 text-base'></i> Reply</button>
                    @endauth
                </div>

                @auth
                    <form id="main-reply-form-{{ $answer->id }}" action="{{ route('reply.store', $answer->id) }}" method="POST" class="hidden ml-11 mb-4">
                        @csrf
                        <div class="flex items-start gap-2">
                            <input type="text" name="content" required placeholder="Write a reply (Markdown supported)..." class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light shadow-sm">
                            <button type="submit" class="bg-maroon-700 text-white px-3 py-2 rounded text-xs font-bold hover:bg-maroon-800 transition">Post</button>
                        </div>
                    </form>
                @endauth

                <div class="ml-11 mt-4">
                    @php 
                        $topLevelReplies = $answer->replies->where('parent_id', null);
                        $limit = 2; 
                        $count = 0;
                    @endphp

                    @foreach($topLevelReplies as $reply)
                        @php $count++; @endphp
                        
                        <div class="{{ $count > $limit ? 'hidden-main-reply-' . $answer->id . ' hidden' : '' }}">
                            @include('partials.reply', ['reply' => $reply])
                        </div>
                    @endforeach

                    @if($topLevelReplies->count() > $limit)
                        <button onclick="toggleMainReplies('{{ $answer->id }}', this)" 
                                data-count="{{ $topLevelReplies->count() - $limit }}"
                                class="text-xs text-maroon-700 font-bold hover:underline mt-2 flex items-center">
                            <i class='bx bx-down-arrow-alt mr-1'></i> 
                            <span class="text-label">View {{ $topLevelReplies->count() - $limit }} more replies</span>
                        </button>
                    @endif
                </div>

            </div>
        @endforeach
        </div>

        <div class="mt-10">
            @if($question->best_answer_id)
                <div class="bg-green-50 border border-green-200 rounded-xl p-8 text-center shadow-sm">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-3 text-green-600">
                        <i class='bx bx-check-circle text-3xl'></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-800">This question is solved</h3>
                    <p class="text-gray-500 font-light mt-2">The question owner has accepted a solution. New answers are no longer being accepted.</p>
                </div>
            @else
                @auth
                    @if(Auth::id() !== $question->user_id)
                        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-lg font-normal text-gray-800 mb-4 flex items-center"><i class='bx bx-edit mr-2 text-maroon-700 font-thin'></i> Post your Answer</h3>
                            <form action="{{ route('answer.store', $question->id) }}" method="POST">
                                @csrf
                                <textarea name="content" required rows="4" class="w-full bg-white border border-gray-300 rounded-lg p-4 mb-3 focus:ring-1 focus:ring-maroon-700 focus:border-maroon-700 focus:outline-none transition resize-none font-light" placeholder="Write a helpful answer (Markdown supported: **bold**, `code`)..."></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-maroon-700 text-white px-6 py-2 rounded-lg font-normal hover:bg-maroon-800 transition shadow-sm flex items-center tracking-wide"><i class='bx bx-send mr-2'></i> Submit Answer</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="text-center p-6 bg-yellow-50 rounded-xl border border-yellow-200 text-yellow-800">
                            <p class="font-light text-sm"><i class='bx bx-info-circle mr-1'></i> You asked this question, so you cannot answer it directly. You can reply to others' answers above.</p>
                        </div>
                    @endif
                @else
                    <div class="mt-8 text-center p-8 bg-white rounded-xl shadow-sm border border-gray-200">
                        <p class="text-gray-600 mb-2 font-light">Know the answer?</p>
                        <p><a href="{{ route('login') }}" class="text-maroon-700 font-bold hover:underline">Log in</a> or <a href="{{ route('register') }}" class="text-maroon-700 font-bold hover:underline">Register</a> to help out.</p>
                    </div>
                @endauth
            @endif
        </div>
    </div>

    <div id="reportModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50 px-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 animate-bounce-in relative transform transition-all">
            <div class="flex items-center mb-4 text-red-600"><i class='bx bx-error-circle text-2xl mr-2 font-thin'></i><h2 class="text-xl font-bold text-gray-800">Report Question</h2></div>
            <p class="text-gray-600 text-sm mb-6 font-light">Why are you flagging this?</p>
            <form action="{{ route('question.report', $question->id) }}" method="POST">
                @csrf
                <div class="space-y-3 mb-6">
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"><input type="radio" name="reason" value="Spam" required class="mr-3" onchange="toggleOtherInput()"><span class="text-gray-800 font-normal text-sm">Spam</span></label>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"><input type="radio" name="reason" value="Harassment" class="mr-3" onchange="toggleOtherInput()"><span class="text-gray-800 font-normal text-sm">Harassment</span></label>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"><input type="radio" name="reason" value="Other" id="otherRadio" class="mr-3" onchange="toggleOtherInput()"><span class="text-gray-800 font-normal text-sm">Other</span></label>
                </div>
                <div id="otherInputContainer" class="mb-6 hidden"><textarea name="other_reason_details" rows="3" class="w-full border p-3 text-sm" placeholder="Please provide details..."></textarea></div>
                <div class="flex justify-end space-x-3"><button type="button" onclick="closeReportModal()" class="px-5 py-2 text-gray-600 rounded-lg text-sm">Cancel</button><button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-lg text-sm">Submit</button></div>
            </form>
            <button onclick="closeReportModal()" class="absolute top-4 right-4 text-gray-400"><i class='bx bx-x text-2xl font-thin'></i></button>
        </div>
    </div>

    <script>
        function openReportModal() { document.getElementById('reportModal').classList.remove('hidden'); }
        function closeReportModal() { document.getElementById('reportModal').classList.add('hidden'); resetModalForm(); }
        function resetModalForm() { document.querySelector('#reportModal form').reset(); toggleOtherInput(); }
        
        function toggleOtherInput() {
            const otherRadio = document.getElementById('otherRadio'); 
            const otherInputContainer = document.getElementById('otherInputContainer'); 
            const otherTextarea = otherInputContainer.querySelector('textarea');
            if (otherRadio.checked) { 
                otherInputContainer.classList.remove('hidden'); 
                otherTextarea.setAttribute('required', 'required'); 
            } else { 
                otherInputContainer.classList.add('hidden'); 
                otherTextarea.removeAttribute('required'); 
            }
        }
        window.onclick = function(event) { let modal = document.getElementById('reportModal'); if (event.target == modal) { closeReportModal(); } }

        function toggleReplyForm(id) {
            const form = document.getElementById(id);
            form.classList.toggle('hidden');
        }

        function toggleChildReplies(containerId, btn) {
            const container = document.getElementById(containerId);
            const count = btn.getAttribute('data-count');
            const textLabel = btn.querySelector('.text-label');
            const icon = btn.querySelector('.icon-indicator');

            container.classList.toggle('hidden');
            const isNowOpen = !container.classList.contains('hidden');

            if (isNowOpen) {
                textLabel.innerText = "Hide replies";
                icon.classList.remove('bx-subdirectory-right');
                icon.classList.add('bx-chevron-up');
            } else {
                textLabel.innerText = "View " + count + " replies";
                icon.classList.remove('bx-chevron-up');
                icon.classList.add('bx-subdirectory-right');
            }
        }

        // FIXED FUNCTION: Toggles replies instead of removing the button
        function toggleMainReplies(answerId, btn) {
            const hiddenElements = document.querySelectorAll('.hidden-main-reply-' + answerId);
            const textLabel = btn.querySelector('.text-label');
            const icon = btn.querySelector('i');
            const count = btn.getAttribute('data-count');
            
            // Check state based on the first hidden element
            let isHidden = true;
            if(hiddenElements.length > 0) {
                isHidden = hiddenElements[0].classList.contains('hidden');
            }

            hiddenElements.forEach(el => {
                el.classList.toggle('hidden');
            });

            if (isHidden) {
                textLabel.innerText = "Hide replies";
                icon.classList.remove('bx-down-arrow-alt');
                icon.classList.add('bx-up-arrow-alt');
            } else {
                textLabel.innerText = "View " + count + " more replies";
                icon.classList.remove('bx-up-arrow-alt');
                icon.classList.add('bx-down-arrow-alt');
            }
        }
    </script>
</body>
</html>