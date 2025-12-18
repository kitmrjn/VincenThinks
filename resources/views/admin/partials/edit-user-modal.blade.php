<div x-show="activeModal === 'edit_{{ $user->id }}'" 
     x-teleport="body"
     class="fixed inset-0 z-[100] overflow-y-auto" 
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="activeModal = null"></div>
        
        <div class="bg-white rounded-xl shadow-xl transform transition-all sm:max-w-lg sm:w-full z-[110] overflow-hidden">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-6 text-left">
                @csrf
                
                {{-- Hidden Inputs to reopen this specific modal if validation fails --}}
                <input type="hidden" name="form_target_id" value="{{ $user->id }}">
                <input type="hidden" name="form_type" value="edit">

                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Profile: {{ $user->name }}</h3>
                
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                        @error('name') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                        @error('email') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Conditional Fields --}}
                    @if($user->member_type === 'student')
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Student Number</label>
                            <input type="text" name="student_number" value="{{ old('student_number', $user->student_number) }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                            @error('student_number') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Course</label>
                            <select name="course_id" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                                <option value="">Select Course...</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $user->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->acronym }} - {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teacher Number</label>
                            <input type="text" name="teacher_number" value="{{ old('teacher_number', $user->teacher_number) }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                            @error('teacher_number') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department / Faculty</label>
                            <select name="department_id" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                                <option value="">Select Department...</option>
                                @foreach($allDepartments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }} ({{ $dept->acronym }})
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-maroon-700 text-white rounded-lg font-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>