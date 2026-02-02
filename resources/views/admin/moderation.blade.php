<x-admin-layout>
    {{-- HEADER WITH ACTIONS --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Moderation Queue</h1>
            <p class="text-xs text-gray-500 mt-1">Review and action flagged content.</p>
        </div>
        <div class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded border border-gray-200 self-start md:self-auto">
            <strong>Total Pending:</strong> {{ $flaggedQuestions->count() + $flaggedAnswers->count() + $flaggedReplies->count() }} items
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 text-green-700 px-4 py-3 rounded-lg border border-green-200 shadow-sm flex items-center text-sm">
            <i class='bx bx-check-circle text-lg mr-2'></i> {{ session('success') }}
        </div>
    @endif

    {{-- MAIN INTERFACE --}}
    <div x-data="{ 
            activeTab: 'questions',
            modalOpen: false,
            selectedItem: null,
            formatDate(dateString) {
                const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                return new Date(dateString).toLocaleDateString('en-US', options);
            },
            openReview(item, type, url) {
                this.selectedItem = item;
                this.selectedItem.type = type;
                this.selectedItem.public_url = url; // Store URL
                this.modalOpen = true;
            }
        }" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden min-h-[600px] flex flex-col">

        {{-- TABS HEADER --}}
        <div class="flex border-b border-gray-200 bg-gray-50/50">
            {{-- Questions Tab --}}
            <button @click="activeTab = 'questions'" 
                :class="activeTab === 'questions' ? 'border-maroon-700 text-maroon-700 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="flex-1 py-3 md:py-4 text-xs md:text-sm font-bold border-b-2 transition-all flex justify-center items-center group focus:outline-none">
                <i class='bx bx-question-mark text-base md:text-lg mr-1 md:mr-2'></i> Questions
                @if($flaggedQuestions->count() > 0)
                    <span class="ml-1 md:ml-2 bg-red-100 text-red-600 text-[10px] px-1.5 md:px-2 py-0.5 rounded-full border border-red-200">{{ $flaggedQuestions->count() }}</span>
                @endif
            </button>

            {{-- Answers Tab --}}
            <button @click="activeTab = 'answers'" 
                :class="activeTab === 'answers' ? 'border-maroon-700 text-maroon-700 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="flex-1 py-3 md:py-4 text-xs md:text-sm font-bold border-b-2 transition-all flex justify-center items-center group focus:outline-none">
                <i class='bx bx-message-dots text-base md:text-lg mr-1 md:mr-2'></i> Answers
                @if($flaggedAnswers->count() > 0)
                    <span class="ml-1 md:ml-2 bg-red-100 text-red-600 text-[10px] px-1.5 md:px-2 py-0.5 rounded-full border border-red-200">{{ $flaggedAnswers->count() }}</span>
                @endif
            </button>

            {{-- Replies Tab --}}
            <button @click="activeTab = 'replies'" 
                :class="activeTab === 'replies' ? 'border-maroon-700 text-maroon-700 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="flex-1 py-3 md:py-4 text-xs md:text-sm font-bold border-b-2 transition-all flex justify-center items-center group focus:outline-none">
                <i class='bx bx-reply text-base md:text-lg mr-1 md:mr-2'></i> Replies
                @if($flaggedReplies->count() > 0)
                    <span class="ml-1 md:ml-2 bg-red-100 text-red-600 text-[10px] px-1.5 md:px-2 py-0.5 rounded-full border border-red-200">{{ $flaggedReplies->count() }}</span>
                @endif
            </button>
        </div>

        {{-- CONTENT AREA --}}
        <div class="flex-grow bg-white relative">
            
            {{-- QUESTIONS LIST --}}
            <div x-show="activeTab === 'questions'" class="absolute inset-0 overflow-y-auto" x-transition.opacity>
                <table class="w-full text-left border-collapse table-fixed md:table-auto">
                    <thead class="bg-gray-50 sticky top-0 z-10 text-xs uppercase text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-3 md:px-6 w-1/3 md:w-1/4">Author</th>
                            <th class="px-2 py-3 md:px-6 w-auto">Content Preview</th>
                            {{-- Increased Padding & Hidden Text on Mobile --}}
                            <th class="px-4 py-3 md:px-6 w-12 md:w-32 text-right"><span class="hidden md:inline">Action</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($flaggedQuestions as $q)
                            <tr class="hover:bg-blue-50/50 transition-colors group cursor-pointer" @click="openReview({{ $q->toJson() }}, 'question', '{{ route('question.show', $q->id) }}')">
                                <td class="px-3 py-3 md:px-6">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 md:h-8 md:w-8 rounded-full bg-maroon-100 text-maroon-700 flex items-center justify-center text-xs font-bold mr-2 md:mr-3 border border-maroon-200 flex-shrink-0">
                                            {{ substr($q->user->name, 0, 1) }}
                                        </div>
                                        <div class="truncate max-w-[80px] md:max-w-[150px]">
                                            <div class="text-sm font-bold text-gray-800 truncate">{{ $q->user->name }}</div>
                                            <div class="text-[10px] text-gray-400 truncate">{{ $q->created_at->diffForHumans(null, true, true) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-3 md:px-6">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-gray-800 text-sm truncate mb-0.5 group-hover:text-maroon-700">{{ $q->title }}</div>
                                            <p class="text-xs text-gray-500 truncate">{{ strip_tags($q->content) }}</p>
                                        </div>
                                        @if($q->images->count() > 0)
                                            <span class="ml-1 md:ml-3 bg-gray-100 text-gray-600 text-[10px] font-bold px-1.5 md:px-2 py-1 rounded border border-gray-200 flex items-center whitespace-nowrap">
                                                <i class='bx bx-images mr-1'></i> <span class="hidden md:inline">{{ $q->images->count() }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                {{-- Increased Padding for "Space next to action" --}}
                                <td class="px-4 py-3 md:px-6 text-right">
                                    <button class="text-gray-400 hover:text-maroon-700 group-hover:bg-white group-hover:border-gray-300 border border-transparent p-1 md:p-1.5 rounded transition">
                                        <i class='bx bx-chevron-right text-xl'></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-20 text-center text-gray-400 text-sm flex flex-col items-center justify-center"><i class='bx bx-check-shield text-4xl mb-2 text-gray-200'></i> All clear! No flagged questions.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ANSWERS LIST --}}
            <div x-show="activeTab === 'answers'" style="display: none;" class="absolute inset-0 overflow-y-auto" x-transition.opacity>
                <table class="w-full text-left border-collapse table-fixed md:table-auto">
                    <thead class="bg-gray-50 sticky top-0 z-10 text-xs uppercase text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-3 md:px-6 w-1/3 md:w-1/4">Author</th>
                            <th class="px-2 py-3 md:px-6 w-auto">Answer Preview</th>
                            <th class="px-4 py-3 md:px-6 w-12 md:w-32 text-right"><span class="hidden md:inline">Action</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($flaggedAnswers as $a)
                            <tr class="hover:bg-blue-50/50 transition-colors group cursor-pointer" @click="openReview({{ json_encode($a->load('question')) }}, 'answer', '{{ route('question.show', $a->question_id) }}#answer-{{ $a->id }}')">
                                <td class="px-3 py-3 md:px-6">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 md:h-8 md:w-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold mr-2 md:mr-3 border border-blue-200 flex-shrink-0">
                                            {{ substr($a->user->name, 0, 1) }}
                                        </div>
                                        <div class="truncate max-w-[80px] md:max-w-[150px]">
                                            <div class="text-sm font-bold text-gray-800 truncate">{{ $a->user->name }}</div>
                                            <div class="text-[10px] text-gray-400 truncate">{{ $a->created_at->diffForHumans(null, true, true) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-3 md:px-6">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-600 truncate">{{ strip_tags($a->content) }}</p>
                                            <div class="text-[10px] text-gray-400 mt-0.5 truncate">On: {{ $a->question->title ?? 'Unknown Question' }}</div>
                                        </div>
                                        @if(str_contains($a->content, '<img'))
                                            <span class="ml-1 md:ml-3 bg-red-50 text-red-600 text-[10px] font-bold px-1.5 md:px-2 py-1 rounded border border-red-100 flex items-center whitespace-nowrap">
                                                <i class='bx bx-image mr-1'></i> <span class="hidden md:inline">Img</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 md:px-6 text-right">
                                    <button class="text-gray-400 hover:text-maroon-700 p-1 md:p-1.5 rounded transition"><i class='bx bx-chevron-right text-xl'></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-20 text-center text-gray-400 text-sm flex flex-col items-center justify-center"><i class='bx bx-check-shield text-4xl mb-2 text-gray-200'></i> No flagged answers.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- REPLIES LIST --}}
            <div x-show="activeTab === 'replies'" style="display: none;" class="absolute inset-0 overflow-y-auto" x-transition.opacity>
                <table class="w-full text-left border-collapse table-fixed md:table-auto">
                    <thead class="bg-gray-50 sticky top-0 z-10 text-xs uppercase text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-3 md:px-6 w-1/3 md:w-1/4">Author</th>
                            <th class="px-2 py-3 md:px-6 w-auto">Reply Preview</th>
                            <th class="px-4 py-3 md:px-6 w-12 md:w-32 text-right"><span class="hidden md:inline">Action</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($flaggedReplies as $r)
                            <tr class="hover:bg-blue-50/50 transition-colors group cursor-pointer" @click="openReview({{ json_encode($r->load('answer.question')) }}, 'reply', '{{ route('question.show', $r->answer->question_id) }}#reply-{{ $r->id }}')">
                                <td class="px-3 py-3 md:px-6">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 md:h-8 md:w-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold mr-2 md:mr-3 border border-purple-200 flex-shrink-0">
                                            {{ substr($r->user->name, 0, 1) }}
                                        </div>
                                        <div class="truncate max-w-[80px] md:max-w-[150px]">
                                            <div class="text-sm font-bold text-gray-800 truncate">{{ $r->user->name }}</div>
                                            <div class="text-[10px] text-gray-400 truncate">{{ $r->created_at->diffForHumans(null, true, true) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-3 md:px-6">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-600 truncate">{{ strip_tags($r->content) }}</p>
                                            <div class="text-[10px] text-gray-400 mt-0.5 truncate">
                                                On: {{ Str::limit($r->answer->question->title ?? 'Unknown', 30) }}
                                            </div>
                                        </div>
                                        @if(str_contains($r->content, '<img'))
                                            <span class="ml-1 md:ml-3 bg-red-50 text-red-600 text-[10px] font-bold px-1.5 md:px-2 py-1 rounded border border-red-100 flex items-center whitespace-nowrap">
                                                <i class='bx bx-image mr-1'></i> <span class="hidden md:inline">Img</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 md:px-6 text-right">
                                    <button class="text-gray-400 hover:text-maroon-700 p-1 md:p-1.5 rounded transition"><i class='bx bx-chevron-right text-xl'></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-20 text-center text-gray-400 text-sm flex flex-col items-center justify-center"><i class='bx bx-check-shield text-4xl mb-2 text-gray-200'></i> No flagged replies.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- REVIEW MODAL --}}
        <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="modalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200 w-full">
                    
                    <template x-if="selectedItem">
                        <div class="flex flex-col max-h-[85vh]">
                            {{-- Modal Header --}}
                            <div class="bg-white px-4 md:px-6 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                                <div>
                                    <h3 class="text-base md:text-lg font-bold text-gray-800">Review Content</h3>
                                    <p class="text-[10px] md:text-xs text-gray-500">
                                        ID: <span x-text="selectedItem.id"></span> • 
                                        Type: <span class="uppercase font-bold" x-text="selectedItem.type"></span>
                                    </p>
                                </div>
                                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition">
                                    <i class='bx bx-x text-xl'></i>
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="px-4 md:px-6 py-6 overflow-y-auto">
                                
                                {{-- 1. Author Info Section --}}
                                <div class="flex items-center mb-6">
                                    <div class="h-10 w-10 rounded-full bg-maroon-50 text-maroon-700 flex items-center justify-center text-sm font-bold border border-maroon-100 mr-3">
                                        <span x-text="selectedItem.user.name.charAt(0)"></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800" x-text="selectedItem.user.name"></div>
                                        <div class="text-xs text-gray-500">
                                            Posted on <span x-text="formatDate(selectedItem.created_at)"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. Context Box --}}
                                <template x-if="selectedItem.type === 'answer' && selectedItem.question">
                                    <div class="mb-4 bg-blue-50 border border-blue-100 rounded-lg p-3 text-sm text-blue-800">
                                        <strong>Answering Question:</strong> 
                                        <span class="italic" x-text="selectedItem.question.title"></span>
                                    </div>
                                </template>

                                <template x-if="selectedItem.type === 'reply' && selectedItem.answer && selectedItem.answer.question">
                                    <div class="mb-4 bg-purple-50 border border-purple-100 rounded-lg p-3 text-sm text-purple-800">
                                        <strong>Replying to Answer on:</strong> 
                                        <span class="italic" x-text="selectedItem.answer.question.title"></span>
                                    </div>
                                </template>

                                {{-- 3. AI Warning --}}
                                <div class="bg-red-50 text-red-800 text-xs p-3 rounded-lg border border-red-100 mb-4 flex items-start">
                                    <i class='bx bx-error-circle text-lg mr-2 mt-0.5'></i>
                                    <div><strong>AI Flagged:</strong> Potentially unsafe content detected by automated systems.</div>
                                </div>

                                {{-- 4. Content --}}
                                <template x-if="selectedItem.title">
                                    <h4 class="text-xl font-bold text-gray-900 mb-4 leading-tight" x-text="selectedItem.title"></h4>
                                </template>

                                <div class="prose prose-sm prose-stone max-w-none p-4 bg-gray-50 rounded-xl border border-gray-100 mb-4" x-html="selectedItem.content"></div>

                                {{-- 5. Images --}}
                                <template x-if="selectedItem.images && selectedItem.images.length > 0">
                                    <div>
                                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Attachments</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <template x-for="img in selectedItem.images">
                                                <a :href="'/storage/' + img.image_path" target="_blank" class="block border border-gray-200 rounded-lg overflow-hidden h-32 bg-gray-100 relative group">
                                                    <img :src="'/storage/' + img.image_path" class="w-full h-full object-cover">
                                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                                        <i class='bx bx-expand text-white text-2xl'></i>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="bg-gray-50 px-4 md:px-6 py-4 flex justify-between items-center border-t border-gray-100 flex-shrink-0">
                                {{-- Left: Context Link --}}
                                <div>
                                    <template x-if="selectedItem.public_url">
                                        <a :href="selectedItem.public_url" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs md:text-sm font-bold hover:underline flex items-center transition">
                                            <i class='bx bx-link-external mr-1'></i> <span class="hidden md:inline">View Context</span><span class="md:hidden">Context</span>
                                        </a>
                                    </template>
                                </div>

                                {{-- Right: Actions (Icons on Mobile, Text on Desktop) --}}
                                <div class="flex gap-2 md:gap-3">
                                    {{-- Delete Button --}}
                                    <form :action="'/admin/moderation/' + selectedItem.type + '/' + selectedItem.id + '/delete'" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete permanently?')" class="text-red-600 hover:text-white hover:bg-red-600 px-3 md:px-6 py-2 rounded-lg text-sm font-bold border border-red-200 hover:border-red-600 transition flex items-center" title="Delete">
                                            <i class='bx bx-trash md:mr-1.5 text-lg'></i> <span class="hidden md:inline">Delete</span>
                                        </button>
                                    </form>
                                    {{-- Approve Button --}}
                                    <form :action="'/admin/moderation/' + selectedItem.type + '/' + selectedItem.id + '/approve'" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 md:px-6 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center" title="Approve">
                                            <i class='bx bx-check md:mr-1.5 text-lg'></i> <span class="hidden md:inline">Approve</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>