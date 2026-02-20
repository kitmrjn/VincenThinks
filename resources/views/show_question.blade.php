<x-public-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
        <style>
            .fade-enter-active { transition: all 0.3s ease; }
            .prose { max-width: none; }
            pre { border-radius: 0.5rem; }
            [x-cloak] { display: none !important; }

            .prose img {
                max-width: 100%;
                max-height: 500px;     
                width: auto;
                height: auto;
                display: block;
                margin: 1.5rem auto;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
                background-color: #f9fafb;
                object-fit: contain;
                cursor: zoom-in;
                transition: all 0.2s ease; 
            }

            /* Responsive Image sizing for indented content */
            .md\:ml-11 .prose img { max-height: 350px !important; max-width: 80%; }

            @keyframes zoom-in {
                from { transform: scale(0.95); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            .animate-zoom-in { animation: zoom-in 0.2s ease-out forwards; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
        <script>hljs.highlightAll();</script>
        <script>
            function openReportModal() { document.getElementById('reportModal').classList.remove('hidden'); }
            function closeReportModal() { document.getElementById('reportModal').classList.add('hidden'); resetModalForm(); }
            function resetModalForm() { document.querySelector('#reportModal form').reset(); toggleOtherInput(); }
            function toggleOtherInput() { const o = document.getElementById('otherRadio'), c = document.getElementById('otherInputContainer'), t = c.querySelector('textarea'); if(o.checked){c.classList.remove('hidden');t.setAttribute('required','required')}else{c.classList.add('hidden');t.removeAttribute('required')} }
            window.onclick = function(e) { if(e.target == document.getElementById('reportModal')) closeReportModal(); }
            function toggleReplyForm(id) { document.getElementById(id).classList.toggle('hidden'); }
            function toggleMainReplies(answerId, btn) {
                const h = document.querySelectorAll('.hidden-main-reply-'+answerId), l = btn.querySelector('.text-label'), i = btn.querySelector('i'), c = btn.getAttribute('data-count');
                let isH = h.length > 0 && h[0].classList.contains('hidden');
                h.forEach(e => e.classList.toggle('hidden'));
                l.innerText = isH ? "Hide replies" : "View " + c + " more replies";
                i.className = isH ? 'bx bx-up-arrow-alt mr-1' : 'bx bx-down-arrow-alt mr-1';
            }
            function toggleChildReplies(containerId, btn) {
                const container = document.getElementById(containerId);
                container.classList.toggle('hidden');
                const isHidden = container.classList.contains('hidden');
                const label = btn.querySelector('.text-label');
                const icon = btn.querySelector('.icon-indicator');
                const count = btn.getAttribute('data-count');
                if (isHidden) {
                    label.innerText = "View " + count + " replies";
                    icon.classList.remove('bx-up-arrow-alt');
                    icon.classList.add('bx-subdirectory-right');
                } else {
                    label.innerText = "Hide replies";
                    icon.classList.remove('bx-subdirectory-right');
                    icon.classList.add('bx-up-arrow-alt');
                }
            }
        </script>
    @endpush

    @php
        $editLimit = (int) (\App\Models\Setting::where('key', 'edit_time_limit')->value('value') ?? 150);
    @endphp

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
        @if(session('warning'))
            <div class="bg-yellow-50 text-yellow-800 p-4 rounded-lg border border-yellow-200 mb-6 shadow-sm flex items-center">
                <i class='bx bx-info-circle text-xl mr-2'></i> {{ session('warning') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200 mb-6 shadow-sm flex items-center"><i class='bx bx-block text-xl mr-2'></i> {{ session('error') }}</div>
        @endif
    </div>

    <div class="max-w-3xl mx-auto px-4 pb-12">

        {{-- PENDING REVIEW BANNER --}}
        @if($question->status === 'pending_review')
            <div class="bg-red-50 text-red-800 p-4 rounded-xl border border-red-200 mb-6 shadow-sm flex items-center animate-pulse">
                <i class='bx bx-lock-alt text-2xl mr-3'></i> 
                <div>
                    <strong class="block text-sm font-bold uppercase tracking-wide">Pending Review</strong>
                    <span class="text-sm opacity-90">This question is currently hidden from the public while we check it for safety. Only you and the admins can see it.</span>
                </div>
            </div>
        @endif

        {{-- QUESTION CARD --}}
        <div class="bg-white rounded-xl shadow-sm p-6 md:p-8 mb-8 border {{ $question->status === 'pending_review' ? 'border-red-300 ring-2 ring-red-50' : 'border-gray-200' }} relative transition-all">
            
            {{-- HEADER --}}
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
                        <div class="flex items-center flex-wrap gap-y-1">
                            <a href="{{ route('user.profile', $question->user->id) }}" class="font-bold text-gray-800 hover:text-maroon-700 hover:underline transition mr-2">
                                {{ $question->user->name }}
                            </a>
                            @if($question->user->member_type === 'student' && $question->user->course)
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100" title="{{ $question->user->course->name }}">{{ $question->user->course->acronym }}</span>
                            @elseif($question->user->member_type === 'teacher' && $question->user->departmentInfo)
                                <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100" title="{{ $question->user->departmentInfo->name }}">{{ $question->user->departmentInfo->acronym ?? $question->user->departmentInfo->name }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 font-light flex items-center flex-wrap mt-1">
                            <span>{{ $question->created_at->diffForHumans() }}</span>
                            
                            @if($question->updated_at->diffInMinutes($question->created_at) > 5)
                                <span class="mx-1">•</span>
                                <span class="italic" title="Edited {{ $question->updated_at->diffForHumans() }}">(ed.)</span>
                            @endif
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="flex items-center text-gray-400">
                                <i class='bx bx-show mr-1'></i> {{ $question->views ?? 0 }}
                            </span>
                            @if($question->category)
                                <span class="mx-2 text-gray-300">|</span>
                                <span class="bg-gray-100 text-maroon-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-gray-200" title="{{ $question->category->name }}">
                                    {{ $question->category->acronym ?? $question->category->name }}
                                </span>
                            @endif

                            @if($question->status === 'pending_review')
                                <span class="mx-2 text-gray-300">|</span>
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-red-200 animate-pulse flex items-center">
                                    <i class='bx bx-lock-alt mr-1'></i> Review
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @if($question->best_answer_id)
                        <span class="bg-green-100 text-green-700 border border-green-200 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide flex items-center shadow-sm">
                            <i class='bx bx-check mr-1 text-base'></i> <span class="hidden md:inline">Solved</span>
                        </span>
                    @endif

                    @auth
                        @if((Auth::id() === $question->user_id || Auth::user()->is_admin) && $question->created_at > now()->subSeconds($editLimit))
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

            {{-- TITLE --}}
            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight tracking-tight">{{ $question->title }}</h1>
            </div>

            {{-- CONTENT --}}
            <div class="prose prose-lg prose-stone max-w-none text-gray-800 leading-relaxed mb-8
                prose-a:text-maroon-700 prose-a:font-semibold prose-a:no-underline hover:prose-a:underline
                prose-headings:font-bold prose-headings:text-gray-900 
                prose-blockquote:border-l-4 prose-blockquote:border-maroon-700 prose-blockquote:bg-gray-50 prose-blockquote:py-2 prose-blockquote:px-4 prose-blockquote:rounded-r-lg prose-blockquote:not-italic
                prose-code:text-maroon-700 prose-code:bg-gray-100 prose-code:rounded prose-code:px-1 prose-code:py-0.5 prose-code:before:content-none prose-code:after:content-none
                prose-pre:bg-[#0d1117] prose-pre:rounded-xl prose-pre:shadow-md prose-pre:border prose-pre:border-gray-700">
                {!! $question->content !!}
            </div>

            {{-- ATTACHMENTS --}}
            @if($question->images->count() > 0)
                <div class="mb-8">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center">
                        <i class='bx bx-paperclip mr-1 text-lg'></i> Attachments ({{ $question->images->count() }})
                    </h4>
                    <div class="overflow-hidden rounded-xl border border-gray-200 w-full">
                        @if($question->images->count() == 1)
                            <div class="w-full bg-gray-100 flex justify-center">
                                <a href="{{ asset('storage/' . $question->images->first()->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $question->images->first()->image_path) }}" class="w-full h-auto max-h-[800px] object-contain" alt="Question Image">
                                </a>
                            </div>
                        @elseif($question->images->count() == 2)
                            <div class="grid grid-cols-2 gap-1 aspect-[3/2] w-full">
                                @foreach($question->images as $img)
                                    <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank" class="block h-full w-full relative">
                                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="grid grid-cols-3 gap-1">
                                @foreach($question->images as $img)
                                    <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank" class="block w-full aspect-square relative">
                                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- ANSWERS HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-light text-gray-800 flex items-center"><i class='bx bx-message-alt-detail mr-2 text-maroon-700 font-thin'></i> {{ $question->answers->count() }} Answers</h3>
            <div class="h-px bg-gray-200 flex-grow ml-4"></div>
        </div>

        {{-- ANSWERS LIST --}}
        <div class="space-y-8">
        @foreach($question->answers as $answer)
            @php 
                $isBest = $question->best_answer_id === $answer->id;
                $isTopRated = isset($topRatedAnswerId) && $topRatedAnswerId === $answer->id;
                $borderClass = $isBest ? 'border-green-500 ring-1 ring-green-500 bg-green-50/10' : ($isTopRated ? 'border-yellow-400 ring-1 ring-yellow-400' : 'border-gray-200');
            @endphp

            <div class="bg-white rounded-xl shadow-sm p-6 md:p-6 border {{ $borderClass }} relative group transition-all mt-4">
                
                {{-- Badges --}}
                <div class="absolute -top-3 left-6 flex space-x-2 z-10">
                    @if($isBest)
                        <div class="bg-green-100 text-green-700 text-[10px] font-bold px-3 py-1 rounded-full border border-green-300 shadow-sm flex items-center">
                            <i class='bx bx-check-circle text-base mr-1'></i> Solution
                        </div>
                    @endif
                    @if($isTopRated)
                        <div class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-3 py-1 rounded-full border border-yellow-300 shadow-sm flex items-center">
                            <i class='bx bxs-trophy text-base mr-1'></i> Top Rated
                        </div>
                    @endif
                    @if($answer->status === 'pending_review')
                        <div class="bg-red-100 text-red-700 text-[10px] font-bold px-3 py-1 rounded-full border border-red-300 shadow-sm flex items-center animate-pulse">
                            <i class='bx bx-error-circle text-base mr-1'></i> Review
                        </div>
                    @endif
                </div>

                {{-- User Info --}}
                <div class="flex justify-between items-start mb-4 mt-2">
                    <div class="flex items-center">
                         @if($answer->user->avatar)
                            <img src="{{ asset('storage/' . $answer->user->avatar) }}" class="w-8 h-8 rounded-full object-cover border border-gray-200 mr-3" alt="{{ $answer->user->name }}">
                         @else
                            <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold mr-3">{{ substr($answer->user->name, 0, 1) }}</div>
                         @endif
                        <div>
                            <div class="flex items-center flex-wrap gap-y-1">
                                <a href="{{ route('user.profile', $answer->user->id) }}" class="block font-medium text-gray-800 text-sm hover:underline mr-2">{{ $answer->user->name }}</a>
                                @if($answer->user->member_type === 'student' && $answer->user->course)
                                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100" title="{{ $answer->user->course->name }}">{{ $answer->user->course->acronym }}</span>
                                @elseif($answer->user->member_type === 'teacher' && $answer->user->departmentInfo)
                                    <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100" title="{{ $answer->user->departmentInfo->name }}">{{ $answer->user->departmentInfo->acronym ?? 'Teacher' }}</span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-400 font-light block mt-0.5">
                                {{ $answer->created_at->diffForHumans() }}
                                @if($answer->updated_at->diffInMinutes($answer->created_at) > 5) 
                                    <span class="italic ml-1" title="Edited {{ $answer->updated_at->diffForHumans() }}">(ed.)</span> 
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Answer Actions (Right aligned) --}}
                    <div class="flex items-center space-x-1">
                        @auth
                            @if(Auth::id() === $question->user_id)
                                <form action="{{ route('answer.best', $answer->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-1 rounded-full transition {{ $isBest ? 'text-green-600 bg-green-50 hover:bg-green-100' : 'text-gray-300 hover:text-green-600 hover:bg-gray-50' }}" title="{{ $isBest ? 'Unmark as Solution' : 'Mark as Accepted Solution' }}"><i class='bx bx-check text-2xl'></i></button>
                                </form>
                            @endif
                            
                            @if((Auth::id() === $answer->user_id || Auth::user()->is_admin) && $answer->created_at > now()->subSeconds($editLimit))
                                <a href="{{ route('answer.edit', $answer->id) }}" class="text-gray-300 hover:text-blue-600 transition p-1" title="Edit"><i class='bx bx-pencil text-xl'></i></a>
                            @endif
                            
                            @if(Auth::id() === $answer->user_id || Auth::user()->is_admin)
                                <form action="{{ route('answer.destroy', $answer->id) }}" method="POST" onsubmit="return confirm('Delete answer?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition p-1" title="Delete"><i class='bx bx-trash text-xl'></i></button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
                
                {{-- Answer Content (Mobile Optimized Indentation) --}}
                <div class="prose prose-sm prose-stone mb-4 ml-0 md:ml-11">
                    {!! $answer->content !!}
                </div>
                
                {{-- Ratings & Reply Button (Mobile Optimized Indentation) --}}
                <div class="ml-0 md:ml-11 flex items-center mb-4">
                    <div class="flex items-center bg-gray-50 rounded-lg p-2 inline-flex border border-gray-200 mr-4">
                        {{-- Average Score --}}
                        <div class="flex items-center text-yellow-500 font-bold mr-3">
                            <i class='bx bxs-star mr-1 text-lg'></i>
                            <span class="text-gray-700">{{ number_format($answer->ratings->avg('score'), 1) }}</span>
                        </div>
                        
                        @auth
                            <div class="h-4 w-px bg-gray-300 mr-3"></div>
                            @if(Auth::id() === $answer->user_id)
                                <div class="flex items-center text-xs text-gray-400 font-light italic">(Your Answer)</div>
                            @else
                                @php $userRating = $answer->ratings->where('user_id', Auth::id())->first(); @endphp
                                @if($userRating)
                                    <div class="flex items-center text-xs text-maroon-700 font-medium"><i class='bx bx-check mr-1 text-lg'></i> Rated: {{ $userRating->score }}</div>
                                @else
                                    {{-- RATING FORM (FIXED) --}}
                                    <form action="{{ route('answer.rate', $answer->id) }}" method="POST" class="flex items-center text-sm">
                                        @csrf
                                        <select name="score" required class="bg-transparent border-none text-gray-600 text-xs font-bold focus:ring-0 cursor-pointer pr-8 pl-0 py-0 leading-tight">
                                            <option value="" disabled selected class="text-gray-400">Rate</option>
                                            <option value="5">5 ★</option>
                                            <option value="4">4 ★</option>
                                            <option value="3">3 ★</option>
                                            <option value="2">2 ★</option>
                                            <option value="1">1 ★</option>
                                        </select>
                                        <button type="submit" class="text-maroon-700 hover:text-maroon-900 text-xs font-bold uppercase tracking-wide">Submit</button>
                                    </form>
                                @endif
                            @endif
                        @endauth
                    </div>
                    
                    @auth
                        @if(!$question->best_answer_id)
                            <button onclick="toggleReplyForm('main-reply-form-{{ $answer->id }}')" class="text-gray-400 hover:text-maroon-700 text-xs font-medium flex items-center transition">
                                <i class='bx bx-reply mr-1 text-base'></i> Reply
                            </button>
                        @endif
                    @endauth
                </div>

                @auth
                    @if(!$question->best_answer_id)
                        {{-- [NEW] Added onsubmit rule to disable button immediately --}}
                        <form id="main-reply-form-{{ $answer->id }}" action="{{ route('reply.store', $answer->id) }}" method="POST" class="hidden ml-0 md:ml-11 mb-6 mt-4" onsubmit="let btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.classList.add('opacity-50', 'cursor-not-allowed'); btn.innerHTML = 'Processing...';">
                            @csrf
                            <div class="space-y-3">
                                <x-trix-editor name="content" placeholder="Write a reply..." max-images="1" />
                                @error('content')
                                    <p class="text-red-500 text-xs mt-1 font-bold flex items-center"><i class='bx bx-error-circle mr-1'></i> {{ $message }}</p>
                                @enderror
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="toggleReplyForm('main-reply-form-{{ $answer->id }}')" class="px-3 py-1.5 text-gray-500 hover:text-gray-700 text-xs font-bold uppercase tracking-wide transition">Cancel</button>
                                    <button type="submit" class="bg-maroon-700 text-white px-4 py-1.5 rounded text-xs font-bold hover:bg-maroon-800 transition shadow-sm uppercase tracking-wide">Reply</button>
                                </div>
                            </div>
                        </form>
                    @endif
                @endauth

                {{-- Threaded Replies (Mobile Optimized Indentation) --}}
                <div class="ml-0 md:ml-11 mt-4">
                    @php 
                        $topLevelReplies = $answer->replies->where('parent_id', null);
                        $limit = 2; 
                        $count = 0;
                    @endphp
                    @foreach($topLevelReplies as $reply)
                        @php $count++; @endphp
                        <div class="{{ $count > $limit ? 'hidden-main-reply-' . $answer->id . ' hidden' : '' }}">
                            @include('partials.reply', ['reply' => $reply, 'question' => $question, 'editLimit' => $editLimit])
                        </div>
                    @endforeach
                    @if($topLevelReplies->count() > $limit)
                        <button onclick="toggleMainReplies('{{ $answer->id }}', this)" data-count="{{ $topLevelReplies->count() - $limit }}" class="text-xs text-maroon-700 font-bold hover:underline mt-2 flex items-center">
                            <i class='bx bx-down-arrow-alt mr-1'></i> <span class="text-label">View {{ $topLevelReplies->count() - $limit }} more replies</span>
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
        </div>

        {{-- NEW ANSWER FORM --}}
        <div class="mt-10">
            @if($question->best_answer_id)
                <div class="bg-green-50 border border-green-200 rounded-xl p-8 text-center shadow-sm">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-3 text-green-600"><i class='bx bx-check-circle text-3xl'></i></div>
                    <h3 class="text-xl font-medium text-gray-800">This question is solved</h3>
                    <p class="text-gray-500 font-light mt-2">The question owner has accepted a solution. New answers and replies are no longer being accepted.</p>
                </div>
            @else
                @auth
                    @if(Auth::id() !== $question->user_id)
                        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-lg font-normal text-gray-800 mb-4 flex items-center"><i class='bx bx-edit mr-2 text-maroon-700 font-thin'></i> Post your Answer</h3>
                            {{-- [NEW] Added onsubmit rule to disable button immediately --}}
                            <form action="{{ route('answer.store', $question->id) }}" method="POST" onsubmit="let btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.classList.add('opacity-50', 'cursor-not-allowed'); btn.innerHTML = '<i class=\'bx bx-loader-alt bx-spin mr-2\'></i> Processing...';">
                                @csrf
                                <div class="mb-4">
                                    <x-trix-editor name="content" placeholder="Write a helpful answer..." max-images="1" />
                                    @error('content')
                                        <p class="text-red-500 text-xs mt-1 font-bold flex items-center">
                                            <i class='bx bx-error-circle mr-1'></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="flex justify-end"><button type="submit" class="bg-maroon-700 text-white px-6 py-2 rounded-lg font-normal hover:bg-maroon-800 transition shadow-sm flex items-center tracking-wide"><i class='bx bx-send mr-2'></i> Submit Answer</button></div>
                            </form>
                        </div>
                    @else
                        <div class="text-center p-6 bg-yellow-50 rounded-xl border border-yellow-200 text-yellow-800"><p class="font-light text-sm"><i class='bx bx-info-circle mr-1'></i> You asked this question, so you cannot answer it directly. You can reply to others' answers above.</p></div>
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

    {{-- REPORT MODAL --}}
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

    {{-- LIGHTBOX (FULL SCREEN IMAGE VIEWER) --}}
    <div x-data="{ 
            open: false, 
            imgSrc: '', 
            init() {
                document.querySelectorAll('.prose img').forEach(img => {
                    img.addEventListener('click', () => {
                        this.imgSrc = img.src;
                        this.open = true;
                    });
                });
            }
        }" 
        @keydown.escape.window="open = false"
    >
        <div x-show="open" 
             x-transition.opacity 
             class="fixed inset-0 z-50 bg-black/95 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;"
             x-cloak>
            
            <button @click="open = false" class="absolute top-4 right-4 text-white/80 hover:text-white z-50 transition">
                <i class='bx bx-x text-5xl'></i>
            </button>

            <img :src="imgSrc" 
                 @click.outside="open = false"
                 class="max-w-full max-h-[90vh] object-contain rounded shadow-2xl animate-zoom-in">
        </div>
    </div>
</x-public-layout>