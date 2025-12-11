@props(['reply', 'depth' => 0])

<div class="mb-2 {{ $depth > 0 ? 'mt-2' : '' }}">
    
    <div class="relative transition-all 
        {{ $depth == 0 ? 'bg-white border border-gray-200 shadow-sm p-4 rounded-lg' : '' }}
        {{ $depth > 0 ? 'bg-gray-50 border-l-4 border-gray-300 p-3 rounded-r-lg' : '' }}
    ">
        
        @if($depth > 0)
            <div class="absolute -left-3 top-4 w-3 h-0.5 bg-gray-300"></div>
        @endif

        <div class="flex justify-between items-start mb-1">
            <div class="flex items-center">
                @if($reply->user->avatar)
                    <img src="{{ asset('storage/' . $reply->user->avatar) }}" class="{{ $depth == 0 ? 'w-6 h-6' : 'w-5 h-5' }} rounded-full object-cover border border-gray-300 mr-2" alt="{{ $reply->user->name }}">
                @else
                    <div class="{{ $depth == 0 ? 'w-6 h-6' : 'w-5 h-5' }} rounded-full bg-gray-200 flex items-center justify-center text-[10px] text-gray-600 font-bold mr-2 border border-gray-300">
                        {{ substr($reply->user->name, 0, 1) }}
                    </div>
                @endif
                <a href="{{ route('user.profile', $reply->user->id) }}" class="font-bold text-gray-800 hover:underline {{ $depth == 0 ? 'text-sm' : 'text-xs' }}">
                    {{ $reply->user->name }}
                </a>
            </div>
            <span class="text-gray-400 text-[10px]">
                {{ $reply->created_at->diffForHumans() }}
                @if($reply->created_at != $reply->updated_at)
                    <span class="italic ml-1" title="Edited {{ $reply->updated_at->diffForHumans() }}">(edited)</span>
                @endif
            </span>
        </div>

        <div class="prose prose-sm prose-stone text-gray-700 font-light leading-relaxed {{ $depth == 0 ? 'text-sm' : 'text-xs' }}">
            {!! Str::markdown($reply->content) !!}
        </div>
        
        @auth
            <div class="mt-2 flex items-center space-x-3">
                <button onclick="toggleReplyForm('reply-form-{{ $reply->id }}')" class="text-[10px] font-bold text-gray-400 hover:text-maroon-700 uppercase tracking-wide flex items-center cursor-pointer transition">
                    <i class='bx bx-reply mr-1 text-sm'></i> Reply
                </button>
                
                @if((Auth::id() === $reply->user->id || Auth::user()->is_admin) && $reply->created_at > now()->subSeconds(150))
                    <a href="{{ route('reply.edit', $reply->id) }}" class="text-[10px] font-bold text-gray-400 hover:text-blue-600 uppercase tracking-wide flex items-center cursor-pointer transition">
                        <i class='bx bx-pencil mr-1 text-sm'></i> Edit
                    </a>
                @endif
            </div>
        @endauth
    </div>

    @auth
        <form id="reply-form-{{ $reply->id }}" action="{{ route('reply.store', $reply->answer_id) }}" method="POST" class="hidden mt-2 ml-4 fade-enter-active relative z-10">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
            <div class="flex items-start gap-2">
                <div class="absolute -left-4 top-4 w-4 h-0.5 bg-gray-300"></div>
                <input type="text" name="content" required placeholder="Reply to {{ $reply->user->name }} (Markdown works)..." class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:border-maroon-700 font-light shadow-sm">
                <button type="submit" class="bg-gray-700 text-white px-3 py-2 rounded text-xs font-bold hover:bg-gray-800 transition">Post</button>
            </div>
        </form>
    @endauth

    @if($reply->children->count() > 0)
        <div class="{{ $depth < 3 ? 'ml-6' : 'ml-0 border-l-2 border-gray-200 pl-2 mt-2' }} relative">
             @if($depth < 3)
                <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gray-200 -ml-3"></div>
            @endif

            <button 
                onclick="toggleChildReplies('children-container-{{ $reply->id }}', this)" 
                data-count="{{ $reply->children->count() }}"
                class="relative z-10 text-[10px] text-maroon-700 font-bold hover:underline ml-3 mt-2 block flex items-center bg-gray-50 px-2 py-1 rounded w-fit border border-gray-200"
            >
                <div class="absolute -left-4 top-1/2 w-3 h-0.5 bg-gray-300"></div>
                <i class='bx bx-subdirectory-right mr-1 text-sm icon-indicator'></i> 
                <span class="text-label">View {{ $reply->children->count() }} replies</span>
            </button>

            <div id="children-container-{{ $reply->id }}" class="hidden mt-2">
                @foreach($reply->children as $child)
                    @include('partials.reply', ['reply' => $child, 'depth' => $depth + 1])
                @endforeach
            </div>
        </div>
    @endif
</div>