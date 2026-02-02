<div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition duration-200 hover:border-maroon-200 group relative mb-4 animate-fade-in-down {{ $q->status === 'pending_review' ? 'ring-2 ring-red-100 bg-red-50/10' : '' }}">
    
    {{-- 1. HEADER --}}
    <div class="flex items-center mb-3">
        {{-- Avatar --}}
        <div class="flex-shrink-0 mr-3">
            @if($q->user->avatar)
                <img src="{{ asset('storage/' . $q->user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="{{ $q->user->name }}">
            @else
                <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-maroon-700 text-sm font-bold">{{ substr($q->user->name, 0, 1) }}</div>
            @endif
        </div>

        {{-- Meta --}}
        <div class="flex flex-col">
            <div class="flex items-center flex-wrap gap-x-2 gap-y-1 text-xs">
                <a href="{{ route('user.profile', $q->user->id) }}" class="font-bold text-gray-700 hover:underline hover:text-maroon-700 text-sm">{{ $q->user->name }}</a>
                @if($q->user->member_type === 'student' && $q->user->course)
                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">{{ $q->user->course->acronym }}</span>
                @elseif($q->user->member_type === 'teacher' && $q->user->departmentInfo)
                    <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">{{ $q->user->departmentInfo->acronym ?? 'Teacher' }}</span>
                @endif
            </div>
            
            <div class="flex items-center text-[11px] text-gray-400 mt-0.5 gap-2">
                <span>{{ $q->created_at->diffForHumans() }}</span>
                @if($q->category)
                    <span class="text-gray-300">|</span>
                    <span class="bg-gray-100 text-maroon-700 px-1.5 py-0.5 rounded text-[9px] uppercase font-bold tracking-wider border border-gray-200">{{ $q->category->acronym ?? $q->category->name }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. CONTENT (Indented Desktop, Full Width Mobile) --}}
    <div class="ml-0 md:ml-14">
        <a href="{{ route('question.show', $q->id) }}" class="block group-hover:text-maroon-700 transition-colors">
            <h3 class="text-lg font-bold text-gray-900 leading-tight mb-2 group-hover:text-maroon-700 transition-colors">{{ $q->title }}</h3>
            <div class="prose prose-sm prose-stone text-gray-600 line-clamp-3 mb-4">{!! Str::markdown($q->content) !!}</div>
            
            @if($q->images->count() > 0)
                <div class="mt-2 rounded-lg overflow-hidden h-64 md:h-72 w-full border border-gray-200 relative bg-gray-50 flex justify-center items-center">
                    <img src="{{ asset('storage/' . $q->images->first()->image_path) }}" class="w-full h-full object-contain">
                    @if($q->images->count() > 1)
                        <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center shadow-sm"><i class='bx bx-images mr-1.5 text-sm'></i> +{{ $q->images->count() - 1 }}</div>
                    @endif
                </div>
            @endif
        </a>

        {{-- 3. FOOTER --}}
        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center space-x-4 text-sm text-gray-400 font-light">
                <span class="flex items-center"><i class='bx bx-message-alt mr-1 text-base'></i> {{ $q->answers_count ?? 0 }} Answers</span>
                <span class="flex items-center"><i class='bx bx-show mr-1 text-lg'></i> {{ $q->views }} Views</span>
            </div>
            
            @if($q->best_answer_id)
                <span class="text-green-600 text-xs font-bold flex items-center bg-green-50 px-2 py-1 rounded-full border border-green-100"><i class='bx bx-check-circle mr-1 text-sm'></i> Solved</span>
            @endif
        </div>
    </div>
</div>