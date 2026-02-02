<x-admin-layout>
    <header class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-light text-gray-800">Moderation Queue</h1>
            <p class="text-sm text-gray-500 mt-1 font-light">Review and resolve user-reported content.</p>
        </div>
        
        <div class="flex items-center bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm">
            <i class='bx bx-flag text-red-500 mr-3 text-xl font-thin'></i>
            <div class="text-right leading-tight">
                <span class="block text-xl font-bold text-gray-800">{{ $reports->count() }}</span>
                <span class="text-[10px] uppercase tracking-wider text-gray-400">Pending</span>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="mb-6 bg-white border-l-4 border-green-500 p-4 shadow-sm flex items-center">
            <i class='bx bx-check text-2xl text-green-500 mr-3 font-thin'></i>
            <span class="text-gray-600 text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Reports Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($reports->count() > 0)
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-500 font-medium">
                        {{-- Reporter --}}
                        <th class="px-4 py-3 md:px-6 font-normal">Reporter</th>
                        
                        {{-- Reported Question --}}
                        <th class="px-4 py-3 md:px-6 font-normal">Reported</th>
                        
                        {{-- HIDE Reason on Mobile --}}
                        <th class="px-6 py-4 font-normal hidden md:table-cell">Reason</th>
                        
                        {{-- HIDE Date on Mobile (We show it under the title instead) --}}
                        <th class="px-6 py-4 font-normal hidden md:table-cell">Date</th>
                        
                        {{-- Actions --}}
                        <th class="px-4 py-3 md:px-6 font-normal text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reports as $report)
                        <tr class="hover:bg-gray-50 transition duration-150 group">
                            
                            {{-- Column 1: Reporter --}}
                            <td class="px-4 py-4 md:px-6 text-sm text-gray-800 whitespace-nowrap align-top">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs mr-2 text-gray-600 flex-shrink-0">
                                        {{ substr($report->user->name ?? 'A', 0, 1) }}
                                    </div>
                                    <span class="truncate max-w-[80px] md:max-w-none">
                                        {{ $report->user->name ?? 'Unknown' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Column 2: Reported Question (+ Date on Mobile) --}}
                            <td class="px-4 py-4 md:px-6 text-sm text-maroon-700 font-medium align-top">
                                @if($report->question)
                                    <div class="flex flex-col">
                                        <a href="{{ route('question.show', $report->question->id) }}" target="_blank" class="hover:underline flex items-center">
                                            {{-- Heavily truncate title on mobile --}}
                                            <span class="block truncate max-w-[120px] md:max-w-xs md:whitespace-normal">
                                                {{ $report->question->title }}
                                            </span>
                                            <i class='bx bx-link-external ml-1 text-xs opacity-50 flex-shrink-0 hidden md:inline'></i>
                                        </a>
                                        
                                        {{-- SHOW DATE HERE ON MOBILE ONLY --}}
                                        <span class="text-xs text-gray-400 font-light mt-1 md:hidden">
                                            {{ $report->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Deleted</span>
                                @endif
                            </td>

                            {{-- Column 3: Reason (Hidden on Mobile) --}}
                            <td class="px-6 py-4 text-sm text-gray-600 hidden md:table-cell align-top">
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs border border-gray-200">
                                    {{ $report->reason }}
                                </span>
                            </td>

                            {{-- Column 4: Date (Hidden on Mobile) --}}
                            <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell align-top">
                                {{ $report->created_at->format('M d, Y') }}
                            </td>

                            {{-- Column 5: Actions --}}
                            <td class="px-4 py-4 md:px-6 text-sm text-right align-top">
                                <div class="flex justify-end space-x-2">
                                    {{-- Dismiss Button --}}
                                    <form action="{{ route('admin.dismiss_report', $report->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        {{-- Mobile: Icon Only (X) | Desktop: Text --}}
                                        <button type="submit" class="text-gray-400 hover:text-gray-600 font-medium text-xs px-2 py-1 md:px-3 border border-transparent hover:border-gray-300 rounded transition" title="Dismiss">
                                            <span class="hidden md:inline">Dismiss</span>
                                            <i class='bx bx-x text-lg md:hidden'></i>
                                        </button>
                                    </form>

                                    {{-- Delete Button --}}
                                    @if($report->question)
                                        <form action="{{ route('admin.delete_question', $report->question->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question? This action cannot be undone.');" class="inline">
                                            @csrf @method('DELETE')
                                            {{-- Mobile: Icon Only (Trash) | Desktop: Text --}}
                                            <button type="submit" class="text-red-600 hover:bg-red-50 font-medium text-xs px-2 py-1 md:px-3 border border-red-200 rounded transition" title="Delete Content">
                                                <span class="hidden md:inline">Delete Content</span>
                                                <i class='bx bx-trash text-lg md:hidden'></i>
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="text-gray-300 cursor-not-allowed font-medium text-xs px-3 py-1 border border-gray-100 rounded">
                                            <span class="hidden md:inline">Deleted</span>
                                            <i class='bx bx-block md:hidden'></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="py-24 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                    <i class='bx bx-check-shield text-4xl text-gray-300 font-thin'></i>
                </div>
                <h3 class="text-lg font-medium text-gray-800">All caught up</h3>
                <p class="text-gray-400 font-light text-sm mt-1">No pending reports to review.</p>
            </div>
        @endif
    </div>
</x-admin-layout>