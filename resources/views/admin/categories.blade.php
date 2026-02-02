<x-admin-layout>
    <div x-data="{ 
        editModalOpen: false, 
        editItem: { id: null, name: '', acronym: '' },
        openEdit(item) {
            this.editItem = item;
            this.editModalOpen = true;
        }
    }" class="space-y-8">
        
        {{-- HEADER: Fixed Mobile Alignment --}}
        <header class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl md:text-3xl font-light text-gray-800">Categories</h1>
                <p class="text-xs md:text-sm text-gray-500 mt-1 font-light">Manage discussion topics</p>
            </div>
            
            {{-- Total Count Badge --}}
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center">
                <i class='bx bx-tag-alt text-maroon-700 mr-2 text-xl'></i>
                <span class="text-maroon-700 font-bold text-lg leading-none">{{ $categories->count() }}</span>
                <span class="text-gray-400 text-[10px] uppercase font-bold ml-2 tracking-widest">Total</span>
            </div>
        </header>

        @if(session('success'))
            <div class="bg-white border-l-4 border-green-500 p-4 shadow-sm flex items-center">
                <i class='bx bx-check text-2xl text-green-500 mr-3 font-thin'></i>
                <span class="text-gray-600 text-sm">{{ session('success') }}</span>
            </div>
        @endif

        {{-- 1. HORIZONTAL CREATE FORM --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4">Add New Category</h2>
            
            <form action="{{ route('admin.category.store') }}" method="POST">
                @csrf
                <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                    
                    {{-- Name Input --}}
                    <div class="w-full md:flex-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Name</label>
                        <input type="text" name="name" placeholder="e.g. Information Technology" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light transition shadow-sm">
                    </div>

                    {{-- Acronym Input --}}
                    <div class="w-full md:w-32">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Acronym</label>
                        <input type="text" name="acronym" placeholder="e.g. IT" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light uppercase transition shadow-sm">
                    </div>

                    {{-- Submit Button --}}
                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full bg-maroon-700 text-white px-6 py-2 rounded-lg hover:bg-maroon-800 transition shadow-sm flex items-center justify-center h-[38px]">
                            <i class='bx bx-plus mr-2'></i> <span class="font-medium text-sm">Add</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 2. FULL WIDTH TABLE --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse table-fixed md:table-auto">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-500 font-medium">
                        {{-- Name --}}
                        <th class="pl-6 pr-4 py-4 font-normal w-auto text-left">Name</th>
                        
                        {{-- Usage (Hidden on mobile) --}}
                        <th class="px-4 py-4 font-normal hidden md:table-cell md:w-1/4 text-center">Usage</th>
                        
                        {{-- Actions --}}
                        <th class="pl-4 pr-6 py-4 font-normal text-right w-24 md:w-auto">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-gray-50 transition group">
                        
                        {{-- Name Column --}}
                        <td class="pl-6 pr-4 py-4 align-top">
                            <div class="flex items-center flex-wrap">
                                @if($cat->acronym)
                                    <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded border border-blue-100 mr-2 mb-1">
                                        {{ $cat->acronym }}
                                    </span>
                                @endif
                                <span class="font-medium text-gray-700 mr-2 mb-1 group-hover:text-maroon-700 transition">{{ $cat->name }}</span>
                            </div>
                            
                            {{-- REMOVED: $cat->slug display --}}
                            
                            {{-- Mobile Usage Stack --}}
                            <div class="md:hidden mt-1.5">
                                <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full border border-gray-200">
                                    {{ $cat->questions_count }} used
                                </span>
                            </div>
                        </td>

                        {{-- Usage Column --}}
                        <td class="px-4 py-4 hidden md:table-cell align-top text-center">
                            <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-full text-xs font-medium border border-gray-200">
                                {{ $cat->questions_count }} questions
                            </span>
                        </td>

                        {{-- Actions Column --}}
                        <td class="pl-4 pr-6 py-4 text-right align-top">
                            <div class="flex justify-end gap-1">
                                <button @click="openEdit({{ $cat->toJson() }})" class="text-gray-400 hover:text-blue-600 hover:bg-blue-50 p-2 rounded transition" title="Edit">
                                    <i class='bx bx-pencil text-xl'></i>
                                </button>

                                <form action="{{ route('admin.category.delete', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 hover:bg-red-50 p-2 rounded transition" title="Delete">
                                        <i class='bx bx-trash text-xl'></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- EDIT MODAL --}}
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="editModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="editModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200">
                    
                    <form :action="'/admin/category/' + editItem.id" method="POST">
                        @csrf @method('PUT')
                        <div class="bg-white px-6 py-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-6">Edit Category</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Category Name</label>
                                    <input type="text" name="name" x-model="editItem.name" required 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Acronym</label>
                                    <input type="text" name="acronym" x-model="editItem.acronym" 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light uppercase">
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-maroon-700 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-maroon-800 sm:w-auto">Update</button>
                            <button type="button" @click="editModalOpen = false" class="inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>