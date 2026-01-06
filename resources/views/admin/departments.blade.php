<x-admin-layout>
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
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">{{ $dept->acronym ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $dept->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.department.delete', $dept->id) }}" method="POST" onsubmit="return confirm('Delete this department? Teachers assigned to it will be set to N/A.');">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-300 hover:text-red-600 transition">
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
</x-admin-layout>