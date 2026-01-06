<x-admin-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-light text-gray-800">Moderation Queue</h1>
        <p class="text-sm text-gray-500 mt-1">Review content flagged by the Content Filter.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-white border-l-4 border-green-500 p-4 shadow-sm flex items-center">
            <i class='bx bx-check text-2xl text-green-500 mr-3 font-thin'></i>
            <span class="text-gray-600 text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Flagged Questions --}}
    <h2 class="text-xl font-medium text-gray-800 mb-4">Flagged Questions ({{ $flaggedQuestions->count() }})</h2>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
        @if($flaggedQuestions->count() > 0)
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4 w-1/2">Content Preview</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($flaggedQuestions as $q)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium">{{ $q->user->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800 text-sm mb-1">{{ $q->title }}</p>
                                <p class="text-xs text-gray-500 leading-relaxed">{{ Str::limit(strip_tags($q->content), 150) }}</p>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <form action="{{ route('admin.moderation.approve', ['type' => 'question', 'id' => $q->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-green-600 hover:text-green-800 text-xs font-bold border border-green-200 px-3 py-1 rounded hover:bg-green-50">Approve</button>
                                </form>
                                <form action="{{ route('admin.moderation.delete', ['type' => 'question', 'id' => $q->id]) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-xs font-bold border border-red-200 px-3 py-1 rounded hover:bg-red-50">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-6 text-center text-gray-400 text-sm">No flagged questions found.</div>
        @endif
    </div>

    {{-- Flagged Answers --}}
    <h2 class="text-xl font-medium text-gray-800 mb-4">Flagged Answers ({{ $flaggedAnswers->count() }})</h2>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($flaggedAnswers->count() > 0)
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4 w-1/2">Content Preview</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($flaggedAnswers as $a)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium">{{ $a->user->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-400 mb-1">On Question: {{ Str::limit($a->question->title ?? 'Deleted', 40) }}</div>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ Str::limit(strip_tags($a->content), 150) }}</p>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <form action="{{ route('admin.moderation.approve', ['type' => 'answer', 'id' => $a->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-green-600 hover:text-green-800 text-xs font-bold border border-green-200 px-3 py-1 rounded hover:bg-green-50">Approve</button>
                                </form>
                                <form action="{{ route('admin.moderation.delete', ['type' => 'answer', 'id' => $a->id]) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-xs font-bold border border-red-200 px-3 py-1 rounded hover:bg-red-50">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-6 text-center text-gray-400 text-sm">No flagged answers found.</div>
        @endif
    </div>
</x-admin-layout>