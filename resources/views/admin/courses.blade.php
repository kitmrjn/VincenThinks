<x-admin-layout>
    <header class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-light text-gray-800">Courses & Strands</h1>
            <p class="text-sm text-gray-500 mt-1 font-light">Manage academic programs available for registration</p>
        </div>
        <div class="flex items-center bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm">
            <i class='bx bxs-graduation text-maroon-700 mr-3 text-xl'></i>
            <div class="text-right leading-tight">
                <span class="block text-xl font-bold text-gray-800">{{ $courses->count() }}</span>
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
        
        {{-- CREATE FORM --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
                <h2 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                    Add New Program
                </h2>
                
                {{-- Alpine Data Wrapper --}}
                <form action="{{ route('admin.course.store') }}" method="POST" x-data="{ selectedType: 'College' }">
                    @csrf
                    
                    {{-- Type Selection --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Type</label>
                        <select name="type" x-model="selectedType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light bg-white">
                            <option value="College">College Course</option>
                            <option value="SHS">Senior High Strand</option>
                            <option value="JHS">Junior High School</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    {{-- Conditional 'Other' Input --}}
                    <div class="mb-4" x-show="selectedType === 'Other'" style="display: none;">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Specify Type</label>
                        <input type="text" name="other_type" placeholder="e.g. Vocational" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light bg-gray-50">
                    </div>

                    {{-- Acronym --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Acronym</label>
                        <input type="text" name="acronym" placeholder="e.g. BSIT" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light uppercase">
                    </div>

                    {{-- Full Name --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Full Name</label>
                        <input type="text" name="name" placeholder="e.g. Bachelor of Science in Information Technology" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-maroon-700 font-light">
                    </div>

                    <button type="submit" class="w-full bg-maroon-700 text-white px-4 py-2 rounded-lg hover:bg-maroon-800 transition flex items-center justify-center">
                        <i class='bx bx-plus mr-2'></i> Add Course
                    </button>
                </form>
            </div>
        </div>

        {{-- LIST TABLE --}}
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-visible">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-500 font-medium">
                            <th class="px-6 py-4 font-normal">Type</th>
                            <th class="px-6 py-4 font-normal">Details</th>
                            <th class="px-6 py-4 font-normal text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($courses as $course)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 align-top">
                                {{-- UPDATED: Dynamic Badges with Purple Fallback --}}
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                    {{ $course->type == 'College' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                      ($course->type == 'SHS' ? 'bg-orange-50 text-orange-700 border-orange-200' : 
                                      ($course->type == 'JHS' ? 'bg-green-50 text-green-700 border-green-200' : 
                                      'bg-purple-50 text-purple-700 border-purple-200')) }}">
                                    {{ $course->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800 block">{{ $course->acronym }}</span>
                                <span class="text-sm text-gray-500 font-light">{{ $course->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.course.delete', $course->id) }}" method="POST" onsubmit="return confirm('Delete this course? Users currently assigned to this course will have their course set to null.');">
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