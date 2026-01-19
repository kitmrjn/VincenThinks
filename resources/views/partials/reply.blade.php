<div class="mt-4 border-l-2 border-gray-100 pl-4" id="reply-{{ $reply->id }}">
    
    {{-- REPLY HEADER --}}
    <div class="flex justify-between items-start mb-2">
        <div class="flex items-center">
            @if($reply->user->avatar)
                <img src="{{ asset('storage/' . $reply->user->avatar) }}" class="w-6 h-6 rounded-full object-cover border border-gray-200 mr-2">
            @else
                <div class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 text-[10px] font-bold mr-2">
                    {{ substr($reply->user->name, 0, 1) }}
                </div>
            @endif
            
            <div class="flex items-baseline">
                <a href="{{ route('user.profile', $reply->user->id) }}" class="text-xs font-bold text-gray-700 hover:text-maroon-700 mr-2">
                    {{ $reply->user->name }}
                </a>
                {{-- REPLY AUTHOR FLAIR --}}
                @if($reply->user->member_type === 'student' && $reply->user->course)
                    <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-1 py-0.5 rounded border border-blue-100 mr-2" title="{{ $reply->user->course->name }}">{{ $reply->user->course->acronym }}</span>
                @elseif($reply->user->member_type === 'teacher' && $reply->user->departmentInfo)
                    <span class="text-[9px] font-bold text-purple-600 bg-purple-50 px-1 py-0.5 rounded border border-purple-100 mr-2">{{ $reply->user->departmentInfo->name }}</span>
                @endif

                <span class="text-[10px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                
                {{-- [FIX] Only show (edited) if the difference is greater than 5 minutes --}}
                @if($reply->updated_at->diffInMinutes($reply->created_at) > 5)
                    <span class="text-[10px] text-gray-300 italic ml-1" title="Edited {{ $reply->updated_at->diffForHumans() }}">(edited)</span>
                @endif

                {{-- [NEW] PENDING REVIEW BADGE --}}
                @if($reply->status === 'pending_review')
                    <span class="text-[9px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100 ml-2 animate-pulse">
                        <i class='bx bx-lock-alt align-middle'></i> Pending Review
                    </span>
                @endif
            </div>
        </div>

        {{-- TOP RIGHT ACTION BUTTONS (Edit/Delete) --}}
        @auth
            <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                @if((Auth::id() === $reply->user_id || Auth::user()->is_admin) && $reply->created_at > now()->subSeconds($editLimit ?? 150))
                    <a href="{{ route('reply.edit', $reply->id) }}" class="text-gray-300 hover:text-blue-600" title="Edit Reply">
                        <i class='bx bx-pencil text-sm'></i>
                    </a>
                @endif
                
                @if(Auth::id() === $reply->user_id || Auth::user()->is_admin)
                    <form action="{{ route('reply.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Delete this reply?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-300 hover:text-red-500" title="Delete Reply">
                            <i class='bx bx-trash text-sm'></i>
                        </button>
                    </form>
                @endif
            </div>
        @endauth
    </div>

    {{-- CONTENT --}}
    <div class="prose prose-sm prose-stone mb-2 text-sm text-gray-600">
        {!! $reply->content !!}
    </div>

    {{-- BOTTOM REPLY BUTTON --}}
    @auth
        @if(!$question->best_answer_id)
            <button onclick="toggleReplyForm('reply-form-{{ $reply->id }}')" class="text-xs text-gray-400 hover:text-maroon-700 font-medium flex items-center mt-1 mb-3">
                <i class='bx bx-reply mr-1 text-sm'></i> Reply
            </button>
        @endif
    @endauth

    {{-- HIDDEN REPLY FORM --}}
    @auth
        @if(!$question->best_answer_id)
            <form id="reply-form-{{ $reply->id }}" action="{{ route('reply.store', $reply->answer_id) }}" method="POST" class="hidden mt-2 mb-4">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                <div class="space-y-2">
                    <x-trix-editor name="content" placeholder="Reply to {{ $reply->user->name }}..." max-images="1" />
                    
                    @error('content')
                        <p class="text-red-500 text-xs mt-1 font-bold flex items-center"><i class='bx bx-error-circle mr-1'></i> {{ $message }}</p>
                    @enderror

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="toggleReplyForm('reply-form-{{ $reply->id }}')" class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700 font-bold uppercase">Cancel</button>
                        <button type="submit" class="bg-gray-100 hover:bg-maroon-700 hover:text-white text-gray-700 px-3 py-1 rounded text-xs font-bold transition uppercase">Reply</button>
                    </div>
                </div>
            </form>
        @endif
    @endauth

    {{-- NESTED REPLIES (Hidden by Default) --}}
    @if($reply->children->count() > 0)
        <div class="mt-2">
            {{-- 1. THE TOGGLE BUTTON --}}
            <button onclick="toggleChildReplies('children-container-{{ $reply->id }}', this)" 
                    data-count="{{ $reply->children->count() }}" 
                    class="text-[10px] text-maroon-700 font-bold hover:underline mb-2 flex items-center">
                <i class='bx bx-subdirectory-right mr-1 icon-indicator'></i> 
                <span class="text-label">View {{ $reply->children->count() }} replies</span>
            </button>

            {{-- 2. THE HIDDEN CONTAINER --}}
            <div id="children-container-{{ $reply->id }}" class="hidden">
                @foreach($reply->children as $child)
                    {{-- UPDATED: Pass $editLimit recursively --}}
                    @include('partials.reply', ['reply' => $child, 'question' => $question, 'editLimit' => $editLimit ?? 150])
                @endforeach
            </div>
        </div>
    @endif
</div>