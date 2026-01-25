<x-admin-layout>
    <div x-data="{ 
        editModalOpen: false, 
        editItem: { id: null, name: '', acronym: '' },
        openEdit(item) {
            this.editItem = item;
            this.editModalOpen = true;
        }
    }">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-light text-gray-800">Departments & Faculties</h2>
                <p class="text-gray-500 text-sm italic font-light">Manage academic departments available for teachers.</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center">
                <i class='bx bxs-graduation text-maroon-700 mr-2 text-xl'></i>
                <span class="text-maroon-700 font-bold text-lg leading-none">{{ $departments->count() }}</span>
                <span class="text-gray-400 text-[10px] uppercase font-bold ml-2 tracking-widest">Total</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Add New Department --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center uppercase tracking-wider">
                        Add New Department
                    </h3>
                    
                    <form action="{{ route('admin.department.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Acronym / Code</label>
                            <input type="text" name="acronym" placeholder="E.g. CAS, IT-CS" value="{{ old('acronym') }}"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-maroon-700 focus:ring-0 text-sm transition">
                            @error('acronym') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Department Name</label>
                            <input type="text" name="name" required placeholder="e.g. College of Arts and Sciences" value="{{ old('name') }}"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-maroon-700 focus:ring-0 text-sm transition">
                            @error('name') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="w-full bg-maroon-700 hover:bg-maroon-800 text-white font-bold py-2.5 rounded-lg transition shadow-sm flex items-center justify-center">
                            <i class='bx bx-plus mr-2'></i> Add Department
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right Column: List of Departments --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible font-light">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Details</th>
                                <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($departments as $dept)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            {{-- [UPDATED] Acronym Badge --}}
                                            @if($dept->acronym)
                                                <span class="bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded border border-purple-100 mr-2 min-w-[40px] text-center">
                                                    {{ $dept->acronym }}
                                                </span>
                                            @endif
                                            <span class="text-sm font-medium text-gray-700">{{ $dept->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                                        {{-- Edit Button --}}
                                        <button @click="openEdit({{ $dept->toJson() }})" class="text-gray-400 hover:text-blue-600 transition p-2 rounded hover:bg-blue-50" title="Edit">
                                            <i class='bx bx-pencil text-lg'></i>
                                        </button>

                                        {{-- Delete Form --}}
                                        <form action="{{ route('admin.department.delete', $dept->id) }}" method="POST" onsubmit="return confirm('Delete this department? Teachers assigned to it will be set to N/A.');">
                                            @csrf @method('DELETE')
                                            <button class="text-gray-400 hover:text-red-600 transition p-2 rounded hover:bg-red-50" title="Delete">
                                                <i class='bx bx-trash text-lg'></i>
                                            </button>
                                        </form>
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
            </div>
        </div>

        {{-- EDIT MODAL --}}
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="editModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="editModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200">
                    
                    <form :action="'/admin/department/' + editItem.id" method="POST">
                        @csrf @method('PUT')
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Department</h3>
                            
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Acronym</label>
                                <input type="text" name="acronym" x-model="editItem.acronym" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light">
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Name</label>
                                <input type="text" name="name" x-model="editItem.name" required 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-maroon-700 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-maroon-800 sm:ml-3 sm:w-auto">Update</button>
                            <button type="button" @click="editModalOpen = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>