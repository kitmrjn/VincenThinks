<div class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-60 backdrop-blur-sm" aria-hidden="true" @click="activeModal = null"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal Content --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
            
            {{-- Header --}}
            <div class="bg-gray-50 px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-bold text-gray-800 flex items-center">
                    <i class='bx bx-edit text-blue-600 mr-2 bg-blue-50 p-1.5 rounded-lg'></i> 
                    Edit User Details
                </h3>
                <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600 transition p-1 hover:bg-gray-100 rounded-full">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-6">
                <form id="edit-form-{{ $user->id }}" action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    {{-- FIX: Removed @method('PATCH') so it submits as a standard POST request --}}

                    <div class="space-y-5">
                        {{-- Name --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Full Name</label>
                            <div class="relative">
                                <i class='bx bx-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg'></i>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 pl-10 pr-3 transition">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Email Address</label>
                            <div class="relative">
                                <i class='bx bx-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg'></i>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 pl-10 pr-3 transition">
                            </div>
                        </div>

                        {{-- SEPARATOR --}}
                        <div class="border-t border-gray-100 pt-2">
                             <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Academic Information</span>

                            {{-- STUDENT FIELDS --}}
                            @if($user->member_type === 'student')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Student ID Number</label>
                                        <input type="text" name="student_number" value="{{ old('student_number', $user->student_number) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3 bg-gray-50 font-mono text-gray-700">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Course / Strand</label>
                                        <select name="course_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                                            <option value="">Select Course...</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" {{ $user->course_id == $course->id ? 'selected' : '' }}>
                                                    {{ $course->acronym }} - {{ $course->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            {{-- TEACHER FIELDS --}}
                            @if($user->member_type === 'teacher')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Teacher ID Number</label>
                                        <input type="text" name="teacher_number" value="{{ old('teacher_number', $user->teacher_number) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3 bg-gray-50 font-mono text-gray-700">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Department</label>
                                        <select name="department_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                                            <option value="">Select Department...</option>
                                            @foreach($allDepartments as $dept)
                                                <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }} @if($dept->acronym) ({{ $dept->acronym }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            
            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-100">
                <button type="button" @click="activeModal = null" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">
                    Cancel
                </button>
                <button type="submit" form="edit-form-{{ $user->id }}" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-6 py-2 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none transition">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>