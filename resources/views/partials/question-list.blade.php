@forelse($questions as $q)
    <div class="bg-white rounded-xl shadow-sm p-5 border transition duration-200 group relative 
        {{ $q->status === 'pending_review' ? 'ring-2 ring-red-100 bg-red-50/20' : '' }}
        {{-- [NEW] Styling for Pinned Posts --}}
        {{ $q->is_pinned ? 'border-amber-400 ring-1 ring-amber-400 bg-amber-50/30' : '' }}
        {{ $q->best_answer_id 
            ? 'border-green-500 ring-1 ring-green-500' 
            : ($q->status !== 'pending_review' && !$q->is_pinned ? 'border-gray-100 hover:border-maroon-200 hover:shadow-md' : '') 
        }}">
        
        @if($q->best_answer_id)
            <div class="absolute -top-3 left-4 bg-green-100 text-green-700 border border-green-400 px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-sm flex items-center gap-1 z-10">
                <i class='bx bx-check text-base'></i> Solved
            </div>
        @endif

        {{-- [NEW] Pinned Badge --}}
        @if($q->is_pinned)
            <div class="absolute -top-3 right-4 bg-amber-100 text-amber-800 border border-amber-400 px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-sm flex items-center gap-1 z-10">
                <i class='bx bx-pin text-base'></i> Pinned
            </div>
        @endif

        <div class="flex items-start {{ $q->best_answer_id || $q->is_pinned ? 'mt-2' : '' }}">
            <div class="flex-shrink-0 mr-4">
                @if($q->user->avatar)
                    <img src="{{ asset('storage/' . $q->user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="{{ $q->user->name }}">
                @else
                    <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 text-sm font-bold">{{ substr($q->user->name, 0, 1) }}</div>
                @endif
            </div>
            <div class="flex-grow min-w-0">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-y-1 sm:gap-x-2 text-xs text-gray-400 font-light flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('user.profile', $q->user->id) }}" class="font-medium text-gray-600 hover:underline hover:text-maroon-700">{{ $q->user->name }}</a>
                            @if($q->user->member_type === 'student' && $q->user->course)
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 whitespace-nowrap" title="{{ $q->user->course->name }}">{{ $q->user->course->acronym }}</span>
                            @elseif($q->user->member_type === 'teacher' && $q->user->departmentInfo)
                                <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100 whitespace-nowrap" title="{{ $q->user->departmentInfo->name }}">{{ $q->user->departmentInfo->acronym ?? $q->user->departmentInfo->name }}</span>
                            @endif
                        </div>
                        <span class="hidden sm:inline text-gray-300">•</span>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="whitespace-nowrap">{{ $q->created_at->diffForHumans() }}</span>
                            @if($q->category)
                                <span class="text-gray-300">|</span>
                                <span class="bg-gray-100 text-maroon-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-gray-200 whitespace-nowrap" title="{{ $q->category->name }}">{{ $q->category->acronym ?? $q->category->name }}</span>
                            @endif

                            @if($q->status === 'pending_review')
                                <span class="ml-2 bg-red-100 text-red-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider border border-red-200 animate-pulse flex items-center shadow-sm whitespace-nowrap">
                                    <i class='bx bx-lock-alt mr-1'></i> Pending Review
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 pl-2 flex-shrink-0">
                        @auth
                            @if(Auth::id() === $q->user_id || Auth::user()->is_admin)
                                <form action="{{ route('question.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition" title="Delete Question"><i class='bx bx-trash text-lg'></i></button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
                <a href="{{ route('question.show', $q->id) }}" class="block group-hover:text-maroon-700 transition-colors">
                    <h3 class="text-lg font-bold text-gray-900 leading-tight mb-2 group-hover:text-maroon-700 transition-colors">{{ $q->title }}</h3>
                    <div class="prose prose-sm prose-stone text-gray-600 line-clamp-3 mb-4">{!! Str::markdown($q->content) !!}</div>
                    @if($q->images->count() > 0)
                        <div class="mt-2 rounded-lg overflow-hidden h-72 w-full border border-gray-200 relative bg-gray-100 flex justify-center items-center">
                            <img src="{{ asset('storage/' . $q->images->first()->image_path) }}" class="w-full h-full object-contain">
                            @if($q->images->count() > 1)
                                <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center shadow-sm"><i class='bx bx-images mr-1.5 text-sm'></i> +{{ $q->images->count() - 1 }}</div>
                            @endif
                        </div>
                    @endif
                </a>
                
                {{-- [NEW] Updated Footer with Actions --}}
                <div class="mt-4 pt-3 border-t border-gray-50 flex items-center justify-between">
                    <div class="flex items-center space-x-4 text-sm text-gray-400 font-light">
                        <span class="flex items-center"><i class='bx bx-message-alt mr-1 text-base'></i> {{ $q->answers_count }} Answers</span>
                        <span class="flex items-center"><i class='bx bx-show mr-1 text-lg'></i> {{ $q->views ?? 0 }} Views</span>
                    </div>

                    {{-- [NEW] "Answer" Button --}}
                    <a href="{{ route('question.show', $q->id) }}#answer-form" class="text-xs font-bold text-maroon-700 hover:text-maroon-900 bg-maroon-50 hover:bg-maroon-100 px-3 py-1.5 rounded-lg transition flex items-center">
                        <i class='bx bx-edit mr-1'></i> Answer
                    </a>
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

<div class="mt-8">
    {{ $questions->withQueryString()->links('partials.pagination') }}
</div>