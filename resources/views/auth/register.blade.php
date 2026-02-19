<x-guest-layout>
    <div class="text-left mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Create an Account</h2>
        <p class="text-gray-500 mt-2 text-sm">Join the discussion today. It's free and easy.</p>
    </div>

    {{-- 
        STRONG CLIENT-SIDE VALIDATION LOGIC
        - Added hasDocument to ensure the user uploads an ID
    --}}
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
          x-data="{ 
              role: '{{ old('member_type', 'student') }}', 
              
              // --- NAME (At least 2 Words) ---
              name: '{{ old('name') }}',
              isNameValid: null,
              validateName() {
                  if (!this.name) { this.isNameValid = null; return; }
                  const regex = /^[a-zA-Z\.]+(?:\s+[a-zA-Z\.]+)+$/;
                  this.isNameValid = regex.test(this.name.trim());
              },

              // --- EMAIL ---
              email: '{{ old('email') }}',
              isEmailValid: null,
              validateEmail() {
                  if (!this.email) { this.isEmailValid = null; return; }
                  const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                  this.isEmailValid = regex.test(this.email);
              },

              // --- IDS (Auto-Caps Enabled) ---
              studentNumber: '{{ old('student_number') }}',
              isStudentNumberValid: null,
              validateStudentNumber() {
                  if (!this.studentNumber) { this.isStudentNumberValid = null; return; }
                  this.studentNumber = this.studentNumber.toUpperCase();
                  const regex = /^AY\d{4}-\d{5}$/; 
                  this.isStudentNumberValid = regex.test(this.studentNumber);
              },

              teacherNumber: '{{ old('teacher_number') }}',
              isTeacherNumberValid: null,
              validateTeacherNumber() {
                  if (!this.teacherNumber) { this.isTeacherNumberValid = null; return; }
                  this.teacherNumber = this.teacherNumber.toUpperCase();
                  const regex = /^AY\d{4}-\d{5}$/;
                  this.isTeacherNumberValid = regex.test(this.teacherNumber);
              },

              // --- PASSWORD (Strong) ---
              password: '',
              isPasswordValid: null,
              validatePassword() {
                  if (!this.password) { this.isPasswordValid = null; return; }
                  const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                  this.isPasswordValid = regex.test(this.password);
                  this.validateConfirmPassword();
              },

              passwordConfirmation: '',
              isConfirmValid: null,
              validateConfirmPassword() {
                  if (!this.passwordConfirmation) { this.isConfirmValid = null; return; }
                  this.isConfirmValid = (this.password === this.passwordConfirmation);
              },

              // --- DOCUMENT UPLOAD ---
              hasDocument: false,
              validateDocument(event) {
                  this.hasDocument = event.target.files.length > 0;
              },

              // --- THE GATEKEEPER ---
              get isFormValid() {
                  const commonValid = this.isNameValid === true && this.isEmailValid === true && 
                                      this.isPasswordValid === true && this.isConfirmValid === true &&
                                      this.hasDocument === true;
                  
                  if (this.role === 'student') {
                      return commonValid && this.isStudentNumberValid === true;
                  } else {
                      return commonValid && this.isTeacherNumberValid === true;
                  }
              }
          }"
          x-init="validateName(); validateEmail(); validateStudentNumber(); validateTeacherNumber();" 
    >
        @csrf

        {{-- 1. FULL NAME --}}
        <div>
            <x-input-label for="name" :value="__('Full Name (Must match your ID)')" />
            <div class="relative mt-1">
                <x-text-input 
                    id="name" 
                    class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2 transition-colors duration-200 outline-none"
                    x-bind:class="{
                        'border-gray-300 focus:border-maroon-700 focus:ring-maroon-700': isNameValid === null,
                        'border-green-500 text-green-700 focus:border-green-500 focus:ring-green-500': isNameValid === true,
                        'border-red-500 text-red-700 focus:border-red-500 focus:ring-red-500': isNameValid === false
                    }"
                    type="text" name="name" x-model="name" @input="validateName()" required autofocus autocomplete="name" placeholder="John Doe" 
                />
                
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i x-show="isNameValid === null" class='bx bx-user text-gray-400 text-xl'></i>
                    <i x-show="isNameValid === true" class='bx bx-check text-green-600 text-2xl animate-bounce' style="animation-iteration-count: 1;"></i>
                    <i x-show="isNameValid === false" class='bx bx-x text-red-600 text-2xl animate-pulse'></i>
                </div>
            </div>
            <p x-show="isNameValid === false" class="text-red-500 text-xs mt-1" style="display: none;">
                Must be at least 2 words (e.g. First & Last Name).
            </p>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- 2. EMAIL ADDRESS --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="relative mt-1">
                <x-text-input 
                    id="email" 
                    class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2 transition-colors duration-200 outline-none"
                    x-bind:class="{
                        'border-gray-300 focus:border-maroon-700 focus:ring-maroon-700': isEmailValid === null,
                        'border-green-500 text-green-700 focus:border-green-500 focus:ring-green-500': isEmailValid === true,
                        'border-red-500 text-red-700 focus:border-red-500 focus:ring-red-500': isEmailValid === false
                    }"
                    type="email" name="email" x-model="email" @input="validateEmail()" required autocomplete="username" placeholder="name@school.edu" 
                />

                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i x-show="isEmailValid === null" class='bx bx-envelope text-gray-400 text-xl'></i>
                    <i x-show="isEmailValid === true" class='bx bx-check text-green-600 text-2xl animate-bounce' style="animation-iteration-count: 1;"></i>
                    <i x-show="isEmailValid === false" class='bx bx-x text-red-600 text-2xl animate-pulse'></i>
                </div>
            </div>
            <p x-show="isEmailValid === false" class="text-red-500 text-xs mt-1" style="display: none;">Please enter a valid email address.</p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- MEMBER TYPE --}}
        <div class="mt-4">
            <x-input-label for="member_type" :value="__('I am a...')" />
            <select id="member_type" name="member_type" x-model="role" @change="setTimeout(() => { validateStudentNumber(); validateTeacherNumber(); }, 100)" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 rounded-lg shadow-sm bg-white text-gray-900">
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
            <x-input-error :messages="$errors->get('member_type')" class="mt-2" />
        </div>

        {{-- 3. STUDENT FIELDS --}}
        <div x-show="role === 'student'" class="space-y-4 mt-4 transition-all" style="display: none;">
            <div>
                <x-input-label for="student_number" :value="__('Student Number')" />
                <div class="relative mt-1">
                    <x-text-input 
                        id="student_number" 
                        class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2 transition-colors duration-200 outline-none"
                        x-bind:class="{
                            'border-gray-300 focus:border-maroon-700 focus:ring-maroon-700': isStudentNumberValid === null,
                            'border-green-500 text-green-700 focus:border-green-500 focus:ring-green-500': isStudentNumberValid === true,
                            'border-red-500 text-red-700 focus:border-red-500 focus:ring-red-500': isStudentNumberValid === false
                        }"
                        type="text" name="student_number" x-model="studentNumber" @input="validateStudentNumber()" placeholder="e.g., AY2023-00123" 
                    />

                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i x-show="isStudentNumberValid === null" class='bx bx-id-card text-gray-400 text-xl'></i>
                        <i x-show="isStudentNumberValid === true" class='bx bx-check text-green-600 text-2xl animate-bounce' style="animation-iteration-count: 1;"></i>
                        <i x-show="isStudentNumberValid === false" class='bx bx-x text-red-600 text-2xl animate-pulse'></i>
                    </div>
                </div>
                <p x-show="isStudentNumberValid === false" class="text-red-500 text-xs mt-1" style="display: none;">Format must be AYxxxx-xxxxx (e.g. AY2023-00123)</p>
                <x-input-error :messages="$errors->get('student_number')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="course_id" :value="__('Course / Strand')" />
                <select id="course_id" name="course_id" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 rounded-lg shadow-sm bg-white text-gray-900">
                    <option value="" disabled selected>Select your course...</option>
                    @if(isset($courses))
                        @foreach($courses as $type => $group)
                            <optgroup label="{{ $type }}">
                                @foreach($group as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->acronym }} - {{ $course->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @endif
                </select>
                <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
            </div>
        </div>

        {{-- 4. TEACHER FIELDS --}}
        <div x-show="role === 'teacher'" class="space-y-4 mt-4 transition-all" style="display: none;">
            <div>
                <x-input-label for="teacher_number" :value="__('Teacher Number')" />
                <div class="relative mt-1">
                    <x-text-input 
                        id="teacher_number" 
                        class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2 transition-colors duration-200 outline-none"
                        x-bind:class="{
                            'border-gray-300 focus:border-maroon-700 focus:ring-maroon-700': isTeacherNumberValid === null,
                            'border-green-500 text-green-700 focus:border-green-500 focus:ring-green-500': isTeacherNumberValid === true,
                            'border-red-500 text-red-700 focus:border-red-500 focus:ring-red-500': isTeacherNumberValid === false
                        }"
                        type="text" name="teacher_number" x-model="teacherNumber" @input="validateTeacherNumber()" placeholder="e.g., AY2023-00123" 
                    />

                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i x-show="isTeacherNumberValid === null" class='bx bx-id-card text-gray-400 text-xl'></i>
                        <i x-show="isTeacherNumberValid === true" class='bx bx-check text-green-600 text-2xl animate-bounce' style="animation-iteration-count: 1;"></i>
                        <i x-show="isTeacherNumberValid === false" class='bx bx-x text-red-600 text-2xl animate-pulse'></i>
                    </div>
                </div>
                <p x-show="isTeacherNumberValid === false" class="text-red-500 text-xs mt-1" style="display: none;">Format must be AYxxxx-xxxxx</p>
                <x-input-error :messages="$errors->get('teacher_number')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="department_id" :value="__('Department / Faculty')" />
                <select id="department_id" name="department_id" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 rounded-lg shadow-sm bg-white text-gray-900">
                    <option value="" disabled selected>Select Department...</option>
                    @if(isset($departments))
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} @if($dept->acronym) ({{ $dept->acronym }}) @endif
                            </option>
                        @endforeach
                    @endif
                </select>
                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
            </div>
        </div>

        {{-- 5. UPLOAD ID DOCUMENT (AI Verification) --}}
        <div class="mt-6 p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
            <x-input-label for="id_document" :value="__('Upload School ID or Registration Form')" class="text-maroon-700 font-bold" />
            <p class="text-xs text-gray-500 mb-2">Our AI will verify your Name and ID number automatically. This image is not saved.</p>
            <input 
                type="file" 
                id="id_document" 
                name="id_document" 
                accept="image/jpeg, image/png, image/jpg"
                @change="validateDocument"
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none p-2"
                required
            >
            <x-input-error :messages="$errors->get('id_document')" class="mt-2" />
        </div>

        {{-- 6. PASSWORD (Strong) --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <x-text-input 
                    id="password" 
                    class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2 transition-colors duration-200 outline-none"
                    x-bind:class="{
                        'border-gray-300 focus:border-maroon-700 focus:ring-maroon-700': isPasswordValid === null,
                        'border-green-500 text-green-700 focus:border-green-500 focus:ring-green-500': isPasswordValid === true,
                        'border-red-500 text-red-700 focus:border-red-500 focus:ring-red-500': isPasswordValid === false
                    }"
                    type="password" name="password" x-model="password" @input="validatePassword()" required autocomplete="new-password" placeholder="••••••••" 
                />

                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i x-show="isPasswordValid === null" class='bx bx-lock-alt text-gray-400 text-xl'></i>
                    <i x-show="isPasswordValid === true" class='bx bx-check text-green-600 text-2xl animate-bounce' style="animation-iteration-count: 1;"></i>
                    <i x-show="isPasswordValid === false" class='bx bx-x text-red-600 text-2xl animate-pulse'></i>
                </div>
            </div>
            <p x-show="isPasswordValid === false" class="text-red-500 text-xs mt-1" style="display: none;">
                Must be 8+ chars, with Uppercase, Lowercase, Number, & Symbol.
            </p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- 7. CONFIRM PASSWORD --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative mt-1">
                <x-text-input 
                    id="password_confirmation" 
                    class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2 transition-colors duration-200 outline-none"
                    x-bind:class="{
                        'border-gray-300 focus:border-maroon-700 focus:ring-maroon-700': isConfirmValid === null,
                        'border-green-500 text-green-700 focus:border-green-500 focus:ring-green-500': isConfirmValid === true,
                        'border-red-500 text-red-700 focus:border-red-500 focus:ring-red-500': isConfirmValid === false
                    }"
                    type="password" name="password_confirmation" x-model="passwordConfirmation" @input="validateConfirmPassword()" required autocomplete="new-password" placeholder="••••••••" 
                />

                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i x-show="isConfirmValid === null" class='bx bx-lock-alt text-gray-400 text-xl'></i>
                    <i x-show="isConfirmValid === true" class='bx bx-check text-green-600 text-2xl animate-bounce' style="animation-iteration-count: 1;"></i>
                    <i x-show="isConfirmValid === false" class='bx bx-x text-red-600 text-2xl animate-pulse'></i>
                </div>
            </div>
            <p x-show="isConfirmValid === false" class="text-red-500 text-xs mt-1" style="display: none;">Passwords do not match.</p>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-8">
            <button 
                type="submit"
                x-bind:disabled="!isFormValid"
                class="w-full justify-center py-3 text-white font-bold rounded-lg transition-all text-base"
                :class="{
                    'bg-maroon-700 hover:bg-maroon-800 cursor-pointer': isFormValid,
                    'bg-gray-400 cursor-not-allowed opacity-50': !isFormValid
                }"
            >
                {{ __('Register & Verify') }}
            </button>
        </div>

        <div class="mt-6 text-center text-sm text-gray-500">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-maroon-700 hover:text-maroon-900 hover:underline">Log in</a>
        </div>
    </form>
</x-guest-layout>