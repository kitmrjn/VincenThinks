<x-admin-layout>
    <div x-data="{ 
        editModalOpen: false, 
        editItem: { id: null, name: '', acronym: '' },
        openEdit(item) {
            this.editItem = item;
            this.editModalOpen = true;
        }
    }" class="space-y-8">
        
        {{-- HEADER: Aligned for Mobile --}}
        <header class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl md:text-3xl font-light text-gray-800">Departments</h1>
                <p class="text-xs md:text-sm text-gray-500 mt-1 font-light">Manage academic faculties</p>
            </div>
            
            {{-- Total Badge --}}
            <div class="flex items-center bg-white border border-gray-200 px-3 py-2 rounded-lg shadow-sm">
                <i class='bx bxs-graduation text-maroon-700 mr-2 md:mr-3 text-lg md:text-xl'></i>
                <div class="text-right leading-tight">
                    <span class="block text-lg md:text-xl font-bold text-gray-800">{{ $departments->count() }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-gray-400">Total</span>
                </div>
            </div>
        </header>

        {{-- 1. HORIZONTAL CREATE FORM (Replaces Sidebar) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center uppercase tracking-wide">
                Add New Department
            </h3>
            
            <form action="{{ route('admin.department.store') }}" method="POST">
                @csrf
                <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                    
                    {{-- Acronym Input (Small width) --}}
                    <div class="w-full md:w-32">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Acronym</label>
                        <input type="text" name="acronym" placeholder="E.g. CAS" value="{{ old('acronym') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-maroon-700 focus:ring-0 text-sm transition font-light uppercase shadow-sm">
                        @error('acronym') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Name Input (Flex Grow) --}}
                    <div class="w-full md:flex-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Department Name</label>
                        <input type="text" name="name" required placeholder="e.g. College of Arts and Sciences" value="{{ old('name') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-maroon-700 focus:ring-0 text-sm transition font-light shadow-sm">
                        @error('name') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full bg-maroon-700 hover:bg-maroon-800 text-white font-bold px-6 py-2 rounded-lg transition shadow-sm flex items-center justify-center h-[38px]">
                            <i class='bx bx-plus mr-2'></i> <span class="text-sm">Add</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 2. FULL WIDTH TABLE --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden font-light">
            <table class="w-full divide-y divide-gray-200 table-fixed md:table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest w-auto">Details</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest w-24 md:w-auto">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($departments as $dept)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-center flex-wrap">
                                    {{-- Acronym Badge --}}
                                    @if($dept->acronym)
                                        <span class="bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded border border-purple-100 mr-3 min-w-[40px] text-center flex-shrink-0 mb-1 md:mb-0">
                                            {{ $dept->acronym }}
                                        </span>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700">{{ $dept->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right align-top">
                                <div class="flex justify-end gap-1">
                                    {{-- Edit Button --}}
                                    <button @click="openEdit({{ $dept->toJson() }})" class="text-gray-400 hover:text-blue-600 transition p-2 rounded hover:bg-blue-50" title="Edit">
                                        <i class='bx bx-pencil text-xl'></i>
                                    </button>

                                    {{-- Delete Form --}}
                                    <form action="{{ route('admin.department.delete', $dept->id) }}" method="POST" onsubmit="return confirm('Delete this department? Teachers assigned to it will be set to N/A.');">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-400 hover:text-red-600 transition p-2 rounded hover:bg-red-50" title="Delete">
                                            <i class='bx bx-trash text-xl'></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-10 text-center text-gray-400 italic">No departments added yet.</td>
                        </tr>
                    @endforelse
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
                    
                    <form :action="'/admin/department/' + editItem.id" method="POST">
                        @csrf @method('PUT')
                        <div class="bg-white px-6 py-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-6">Edit Department</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Acronym</label>
                                    <input type="text" name="acronym" x-model="editItem.acronym" 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light uppercase">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Department Name</label>
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