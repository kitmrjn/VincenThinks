<x-admin-layout>
    <div x-data="{ 
        editModalOpen: false, 
        editItem: { id: null, name: '', acronym: '', type: 'College', other_type: '' },
        openEdit(item) {
            this.editItem = item;
            if (!['College', 'SHS', 'JHS'].includes(item.type)) {
                this.editItem.type = 'Other';
                this.editItem.other_type = item.type;
            } else {
                this.editItem.other_type = '';
            }
            this.editModalOpen = true;
        }
    }" class="space-y-8">
        
        {{-- HEADER: Aligned for Mobile --}}
        <header class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl md:text-3xl font-light text-gray-800">Courses & Strands</h1>
                <p class="text-xs md:text-sm text-gray-500 mt-1 font-light">Manage academic programs</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center">
                <i class='bx bxs-graduation text-maroon-700 mr-2 text-xl'></i>
                <span class="text-maroon-700 font-bold text-lg leading-none">{{ $courses->count() }}</span>
                <span class="text-gray-400 text-[10px] uppercase font-bold ml-2 tracking-widest">Total</span>
            </div>
        </header>

        @if(session('success'))
            <div class="bg-white border-l-4 border-green-500 p-4 shadow-sm flex items-center">
                <i class='bx bx-check text-2xl text-green-500 mr-3 font-thin'></i>
                <span class="text-gray-600 text-sm">{{ session('success') }}</span>
            </div>
        @endif

        {{-- 1. TOP FORM (Replaces Sidebar) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4">Add New Program</h2>
            
            <form action="{{ route('admin.course.store') }}" method="POST" x-data="{ selectedType: 'College' }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start md:items-end">
                    
                    {{-- Type Selection --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Type</label>
                        <select name="type" x-model="selectedType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light bg-white shadow-sm">
                            <option value="College">College</option>
                            <option value="SHS">Senior High</option>
                            <option value="JHS">Junior High</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    {{-- Conditional 'Other' Input --}}
                    <div class="md:col-span-2" x-show="selectedType === 'Other'" style="display: none;">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Specify</label>
                        <input type="text" name="other_type" placeholder="e.g. Vocational" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light bg-gray-50 shadow-sm">
                    </div>

                    {{-- Acronym --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Acronym</label>
                        <input type="text" name="acronym" placeholder="e.g. BSIT" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light uppercase shadow-sm">
                    </div>

                    {{-- Full Name --}}
                    <div :class="selectedType === 'Other' ? 'md:col-span-4' : 'md:col-span-6'">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Full Name</label>
                        <input type="text" name="name" placeholder="e.g. Bachelor of Science in Information Technology" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light shadow-sm">
                    </div>

                    {{-- Submit Button --}}
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full bg-maroon-700 text-white px-4 py-2 rounded-lg hover:bg-maroon-800 transition shadow-sm flex items-center justify-center h-[38px]">
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
                        {{-- Type: Hidden on Mobile --}}
                        <th class="px-6 py-4 font-normal hidden md:table-cell w-1/6">Type</th>
                        
                        {{-- Details: Auto width --}}
                        <th class="px-4 py-3 md:px-6 font-normal w-auto">Details</th>
                        
                        {{-- Actions: Fixed width --}}
                        <th class="px-4 py-3 md:px-6 font-normal text-right w-24 md:w-auto">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($courses as $course)
                    <tr class="hover:bg-gray-50 transition group">
                        
                        {{-- Type Column (Desktop Only) --}}
                        <td class="px-6 py-4 align-top hidden md:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                {{ $course->type == 'College' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                  ($course->type == 'SHS' ? 'bg-orange-50 text-orange-700 border-orange-200' : 
                                  ($course->type == 'JHS' ? 'bg-green-50 text-green-700 border-green-200' : 
                                  'bg-purple-50 text-purple-700 border-purple-200')) }}">
                                {{ $course->type }}
                            </span>
                        </td>

                        {{-- Details Column --}}
                        <td class="px-4 py-3 md:px-6 align-top">
                            <div class="flex flex-col">
                                {{-- Mobile Only: Type Badge Stacked --}}
                                <div class="md:hidden mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wide
                                        {{ $course->type == 'College' ? 'bg-blue-50 text-blue-700 border-blue-100' : 
                                          ($course->type == 'SHS' ? 'bg-orange-50 text-orange-700 border-orange-100' : 
                                          ($course->type == 'JHS' ? 'bg-green-50 text-green-700 border-green-100' : 
                                          'bg-purple-50 text-purple-700 border-purple-100')) }}">
                                        {{ $course->type }}
                                    </span>
                                </div>

                                <span class="font-bold text-gray-800 block text-sm md:text-base">{{ $course->acronym }}</span>
                                <span class="text-xs md:text-sm text-gray-500 font-light truncate md:whitespace-normal group-hover:text-maroon-700 transition">
                                    {{ $course->name }}
                                </span>
                            </div>
                        </td>

                        {{-- Actions Column --}}
                        <td class="px-4 py-3 md:px-6 text-right align-top">
                            <div class="flex justify-end gap-1">
                                <button @click="openEdit({{ $course->toJson() }})" class="text-gray-400 hover:text-blue-600 hover:bg-blue-50 p-2 rounded transition" title="Edit">
                                    <i class='bx bx-pencil text-xl'></i>
                                </button>

                                <form action="{{ route('admin.course.delete', $course->id) }}" method="POST" onsubmit="return confirm('Delete this course?');">
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
                    
                    <form :action="'/admin/course/' + editItem.id" method="POST">
                        @csrf @method('PUT')
                        <div class="bg-white px-6 py-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-6">Edit Course</h3>
                            
                            <div class="space-y-4">
                                {{-- Edit Type --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Type</label>
                                    <select name="type" x-model="editItem.type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light bg-white">
                                        <option value="College">College Course</option>
                                        <option value="SHS">Senior High Strand</option>
                                        <option value="JHS">Junior High School</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div x-show="editItem.type === 'Other'" style="display: none;">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Specify Type</label>
                                    <input type="text" name="other_type" x-model="editItem.other_type" 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light bg-gray-50">
                                </div>

                                {{-- Edit Acronym --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Acronym</label>
                                    <input type="text" name="acronym" x-model="editItem.acronym" required 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light uppercase">
                                </div>

                                {{-- Edit Name --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Full Name</label>
                                    <input type="text" name="name" x-model="editItem.name" required 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light">
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