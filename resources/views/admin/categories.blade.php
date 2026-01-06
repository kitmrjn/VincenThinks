<x-admin-layout>
    <header class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-light text-gray-800">Categories</h1>
            <p class="text-sm text-gray-500 mt-1 font-light">Manage discussion topics</p>
        </div>
        <div class="flex items-center bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm">
            <i class='bx bx-tag-alt text-maroon-700 mr-3 text-xl font-thin'></i>
            <div class="text-right leading-tight">
                <span class="block text-xl font-bold text-gray-800">{{ $categories->count() }}</span>
                <span class="text-[10px] uppercase tracking-wider text-gray-400">Total</span>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="mb-6 bg-white border-l-4 border-green-500 p-4 shadow-sm flex items-center">
            <i class='bx bx-check text-2xl text-green-500 mr-3 font-thin'></i>
            <span class="text-gray-600 text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
                <h2 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                    Add New Category
                </h2>
                
                <form action="{{ route('admin.category.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Category Name</label>
                        <input type="text" name="name" placeholder="e.g. Information Technology" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light">
                    </div>

                    {{-- NEW INPUT: Acronym --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Acronym (Optional)</label>
                        <input type="text" name="acronym" placeholder="e.g. IT" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light uppercase">
                    </div>

                    <button type="submit" class="w-full bg-maroon-700 text-white px-4 py-2 rounded-lg hover:bg-maroon-800 transition flex items-center justify-center">
                        <i class='bx bx-plus mr-2'></i> Create Category
                    </button>
                </form>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-visible">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-500 font-medium">
                            <th class="px-6 py-4 font-normal">Name</th>
                            <th class="px-6 py-4 font-normal">Usage</th>
                            <th class="px-6 py-4 font-normal text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($categories as $cat)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    {{-- NEW: Display Acronym Badge --}}
                                    @if($cat->acronym)
                                        <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded border border-blue-100 mr-2">
                                            {{ $cat->acronym }}
                                        </span>
                                    @endif
                                    <span class="font-medium text-gray-700">{{ $cat->name }}</span>
                                </div>
                                <span class="block text-xs text-gray-400 font-light mt-0.5">{{ $cat->slug }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium border border-gray-200">
                                    {{ $cat->questions_count }} questions
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.category.delete', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category? Questions will lose their category tag.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition p-2 rounded hover:bg-red-50" title="Delete">
                                        <i class='bx bx-trash text-lg'></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admin-layout>