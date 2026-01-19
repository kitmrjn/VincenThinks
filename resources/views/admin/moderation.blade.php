<x-admin-layout>
    {{-- HEADER WITH ACTIONS --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Moderation Queue</h1>
            <p class="text-xs text-gray-500 mt-1">Review and action flagged content.</p>
        </div>
        <div class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded border border-gray-200">
            <strong>Total Pending:</strong> {{ $flaggedQuestions->count() + $flaggedAnswers->count() + $flaggedReplies->count() }} items
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 text-green-700 px-4 py-3 rounded-lg border border-green-200 shadow-sm flex items-center text-sm">
            <i class='bx bx-check-circle text-lg mr-2'></i> {{ session('success') }}
        </div>
    @endif

    {{-- MAIN INTERFACE (TABS + TABLES) --}}
    <div x-data="{ 
            activeTab: 'questions',
            modalOpen: false,
            selectedItem: null,
            openReview(item, type) {
                this.selectedItem = item;
                this.selectedItem.type = type;
                this.modalOpen = true;
            }
        }" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden min-h-[600px] flex flex-col">

        {{-- TABS HEADER --}}
        <div class="flex border-b border-gray-200 bg-gray-50/50">
            {{-- Questions Tab --}}
            <button @click="activeTab = 'questions'" 
                :class="activeTab === 'questions' ? 'border-maroon-700 text-maroon-700 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="flex-1 py-4 text-sm font-bold border-b-2 transition-all flex justify-center items-center group focus:outline-none">
                <i class='bx bx-question-mark text-lg mr-2'></i> Questions
                @if($flaggedQuestions->count() > 0)
                    <span class="ml-2 bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full border border-red-200">{{ $flaggedQuestions->count() }}</span>
                @endif
            </button>

            {{-- Answers Tab --}}
            <button @click="activeTab = 'answers'" 
                :class="activeTab === 'answers' ? 'border-maroon-700 text-maroon-700 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="flex-1 py-4 text-sm font-bold border-b-2 transition-all flex justify-center items-center group focus:outline-none">
                <i class='bx bx-message-dots text-lg mr-2'></i> Answers
                @if($flaggedAnswers->count() > 0)
                    <span class="ml-2 bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full border border-red-200">{{ $flaggedAnswers->count() }}</span>
                @endif
            </button>

            {{-- Replies Tab --}}
            <button @click="activeTab = 'replies'" 
                :class="activeTab === 'replies' ? 'border-maroon-700 text-maroon-700 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="flex-1 py-4 text-sm font-bold border-b-2 transition-all flex justify-center items-center group focus:outline-none">
                <i class='bx bx-reply text-lg mr-2'></i> Replies
                @if($flaggedReplies->count() > 0)
                    <span class="ml-2 bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full border border-red-200">{{ $flaggedReplies->count() }}</span>
                @endif
            </button>
        </div>

        {{-- CONTENT AREA --}}
        <div class="flex-grow bg-white relative">
            
            {{-- QUESTIONS LIST --}}
            <div x-show="activeTab === 'questions'" class="absolute inset-0 overflow-y-auto" x-transition.opacity>
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10 text-xs uppercase text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 w-1/4">Author</th>
                            <th class="px-6 py-3">Content Preview</th>
                            <th class="px-6 py-3 w-32 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($flaggedQuestions as $q)
                            <tr class="hover:bg-blue-50/50 transition-colors group cursor-pointer" @click="openReview({{ $q->toJson() }}, 'question')">
                                <td class="px-6 py-3">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-maroon-100 text-maroon-700 flex items-center justify-center text-xs font-bold mr-3 border border-maroon-200">
                                            {{ substr($q->user->name, 0, 1) }}
                                        </div>
                                        <div class="truncate max-w-[150px]">
                                            <div class="text-sm font-bold text-gray-800 truncate">{{ $q->user->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $q->created_at->diffForHumans(null, true, true) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-gray-800 text-sm truncate mb-0.5 group-hover:text-maroon-700">{{ $q->title }}</div>
                                            <p class="text-xs text-gray-500 truncate">{{ strip_tags($q->content) }}</p>
                                        </div>
                                        @if($q->images->count() > 0)
                                            <span class="ml-3 bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded border border-gray-200 flex items-center whitespace-nowrap">
                                                <i class='bx bx-images mr-1'></i> {{ $q->images->count() }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button class="text-gray-400 hover:text-maroon-700 group-hover:bg-white group-hover:border-gray-300 border border-transparent p-1.5 rounded transition">
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
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10 text-xs uppercase text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 w-1/4">Author</th>
                            <th class="px-6 py-3">Answer Preview</th>
                            <th class="px-6 py-3 w-32 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($flaggedAnswers as $a)
                            <tr class="hover:bg-blue-50/50 transition-colors group cursor-pointer" @click="openReview({{ json_encode($a->load('question')) }}, 'answer')">
                                <td class="px-6 py-3">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold mr-3 border border-blue-200">
                                            {{ substr($a->user->name, 0, 1) }}
                                        </div>
                                        <div class="truncate max-w-[150px]">
                                            <div class="text-sm font-bold text-gray-800 truncate">{{ $a->user->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $a->created_at->diffForHumans(null, true, true) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-600 truncate">{{ strip_tags($a->content) }}</p>
                                            <div class="text-[10px] text-gray-400 mt-0.5 truncate">On: {{ $a->question->title ?? 'Unknown Question' }}</div>
                                        </div>
                                        @if(str_contains($a->content, '<img'))
                                            <span class="ml-3 bg-red-50 text-red-600 text-[10px] font-bold px-2 py-1 rounded border border-red-100 flex items-center whitespace-nowrap">
                                                <i class='bx bx-image mr-1'></i> Image
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button class="text-gray-400 hover:text-maroon-700 p-1.5 rounded transition"><i class='bx bx-chevron-right text-xl'></i></button>
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
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10 text-xs uppercase text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 w-1/4">Author</th>
                            <th class="px-6 py-3">Reply Preview</th>
                            <th class="px-6 py-3 w-32 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($flaggedReplies as $r)
                            <tr class="hover:bg-blue-50/50 transition-colors group cursor-pointer" @click="openReview({{ json_encode($r->load('answer.question')) }}, 'reply')">
                                <td class="px-6 py-3">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold mr-3 border border-purple-200">
                                            {{ substr($r->user->name, 0, 1) }}
                                        </div>
                                        <div class="truncate max-w-[150px]">
                                            <div class="text-sm font-bold text-gray-800 truncate">{{ $r->user->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $r->created_at->diffForHumans(null, true, true) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-600 truncate">{{ strip_tags($r->content) }}</p>
                                            <div class="text-[10px] text-gray-400 mt-0.5 truncate">
                                                On: {{ Str::limit($r->answer->question->title ?? 'Unknown', 30) }}
                                            </div>
                                        </div>
                                        @if(str_contains($r->content, '<img'))
                                            <span class="ml-3 bg-red-50 text-red-600 text-[10px] font-bold px-2 py-1 rounded border border-red-100 flex items-center whitespace-nowrap">
                                                <i class='bx bx-image mr-1'></i> Image
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button class="text-gray-400 hover:text-maroon-700 p-1.5 rounded transition"><i class='bx bx-chevron-right text-xl'></i></button>
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
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200">
                    
                    <template x-if="selectedItem">
                        <div class="flex flex-col max-h-[80vh]">
                            {{-- Modal Header --}}
                            <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Review Content</h3>
                                    <p class="text-xs text-gray-500">ID: <span x-text="selectedItem.id"></span> • Type: <span class="uppercase font-bold" x-text="selectedItem.type"></span></p>
                                </div>
                                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition">
                                    <i class='bx bx-x text-xl'></i>
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="px-6 py-6 overflow-y-auto">
                                <div class="bg-red-50 text-red-800 text-xs p-3 rounded-lg border border-red-100 mb-4 flex items-start">
                                    <i class='bx bx-error-circle text-lg mr-2 mt-0.5'></i>
                                    <div><strong>AI Flagged:</strong> Potentially unsafe content detected.</div>
                                </div>

                                <template x-if="selectedItem.title">
                                    <h4 class="text-xl font-bold text-gray-900 mb-4 leading-tight" x-text="selectedItem.title"></h4>
                                </template>

                                <div class="prose prose-sm prose-stone max-w-none p-4 bg-gray-50 rounded-xl border border-gray-100 mb-4" x-html="selectedItem.content"></div>

                                {{-- Images Grid --}}
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
                            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100 flex-shrink-0">
                                <form :action="'/admin/moderation/' + selectedItem.type + '/' + selectedItem.id + '/delete'" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete permanently?')" class="text-red-600 hover:text-white hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-bold border border-red-200 hover:border-red-600 transition">Delete</button>
                                </form>
                                <form :action="'/admin/moderation/' + selectedItem.type + '/' + selectedItem.id + '/approve'" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                                        <i class='bx bx-check mr-1.5 text-lg'></i> Approve
                                    </button>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>