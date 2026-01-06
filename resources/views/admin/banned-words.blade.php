<x-admin-layout>
    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-light text-gray-800">Banned Words Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage the local blocklist for content moderation.</p>
        </div>
    </header>

    @if(session('success'))
        <div class="mb-6 bg-white border-l-4 border-green-500 p-4 shadow-sm flex items-center">
            <i class='bx bx-check text-2xl text-green-500 mr-3 font-thin'></i>
            <span class="text-gray-600 text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 shadow-sm">
            <ul class="text-xs text-red-600 list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Add New Word Form --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Add New Word</h3>
                <form action="{{ route('admin.banned_words.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="word" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Word or Phrase</label>
                        <input type="text" name="word" id="word" class="w-full border-gray-300 rounded-md shadow-sm focus:border-maroon-700 focus:ring focus:ring-maroon-200 focus:ring-opacity-50" placeholder="e.g. scam" required>
                        <p class="text-xs text-gray-400 mt-1">Case insensitive. Will flag content containing this word.</p>
                    </div>
                    <button type="submit" class="w-full bg-maroon-700 hover:bg-maroon-800 text-white font-medium py-2 px-4 rounded transition">
                        Add to Blocklist
                    </button>
                </form>
            </div>
        </div>

        {{-- List of Banned Words --}}
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-medium text-gray-700">Current Blocklist</h3>
                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">{{ $words->count() }} words</span>
                </div>
                
                @if($words->count() > 0)
                    <div class="p-4 grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-[500px] overflow-y-auto">
                        @foreach($words as $word)
                            <div class="flex justify-between items-center bg-gray-50 border border-gray-200 px-3 py-2 rounded group hover:border-red-200 transition">
                                <span class="font-medium text-gray-700">{{ $word->word }}</span>
                                <form action="{{ route('admin.banned_words.delete', $word->id) }}" method="POST" onsubmit="return confirm('Remove this word?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400 text-sm">
                        Blocklist is empty.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>