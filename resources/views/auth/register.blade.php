<x-guest-layout>
    <div class="text-left mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Create an Account</h2>
        <p class="text-gray-500 mt-2 text-sm">Join the discussion today. It's free and easy.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
          x-data="{ 
              role: '{{ old('member_type', 'student') }}', 
              
              name: '{{ old('name') }}',
              isNameValid: null,
              validateName() {
                  if (!this.name) { this.isNameValid = null; return; }
                  const regex = /^[a-zA-Z\.]+(?:\s+[a-zA-Z\.]+)+$/;
                  this.isNameValid = regex.test(this.name.trim());
              },

              email: '{{ old('email') }}',
              isEmailValid: null,
              validateEmail() {
                  if (!this.email) { this.isEmailValid = null; return; }
                  const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                  this.isEmailValid = regex.test(this.email);
              },

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

              hasDocument: false,
              validateDocument(event) {
                  this.hasDocument = event.target.files.length > 0;
              },

              terms: false,
              
              // [NEW] Modal State Variable
              showTermsModal: false,

              get isFormValid() {
                  const commonValid = this.isNameValid === true && this.isEmailValid === true && 
                                      this.isPasswordValid === true && this.isConfirmValid === true &&
                                      this.hasDocument === true && this.terms === true;
                  
                  if (this.role === 'student') {
                      return commonValid && this.isStudentNumberValid === true;
                  } else {
                      return commonValid && this.isTeacherNumberValid === true;
                  }
              },

              isSubmitting: false,
              loadingText: 'Securely uploading your document...',
              startLoading() {
                  if(this.isFormValid) {
                      this.isSubmitting = true;
                      
                      let step = 0;
                      const messages = [
                          'Securely uploading your document...',
                          'Our AI is reading your ID card...',
                          'Cross-checking Name and ID Number...',
                          'Verifying school year validity...',
                          'Almost done, finalizing registration...'
                      ];
                      
                      setInterval(() => {
                          step++;
                          if(step < messages.length) {
                              this.loadingText = messages[step];
                          }
                      }, 2500);
                  }
              }
          }"
          x-init="validateName(); validateEmail(); validateStudentNumber(); validateTeacherNumber();" 
          @submit="startLoading()"
    >
        @csrf

        {{-- FULL SCREEN LOADING OVERLAY --}}
        <div x-show="isSubmitting" 
             style="display: none;" 
             class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-white/90 backdrop-blur-sm transition-opacity duration-300">
            <i class='bx bx-loader-alt bx-spin text-maroon-700 text-7xl mb-6'></i>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Verifying Identity</h3>
            <p class="text-lg text-gray-600 font-medium animate-pulse" x-text="loadingText"></p>
            <p class="text-sm text-gray-400 mt-6 max-w-sm text-center">
                Please do not refresh or close this page. This process usually takes 5-10 seconds.
            </p>
        </div>

        {{-- [NEW] TERMS AND CONDITIONS MODAL --}}
        <div x-show="showTermsModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-opacity">
            <div @click.away="showTermsModal = false" class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden transform transition-all">
                
                <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h3 class="text-xl font-bold text-gray-900">Terms and Conditions</h3>
                    <button type="button" @click="showTermsModal = false" class="text-gray-400 hover:text-maroon-700 text-2xl font-bold focus:outline-none transition-colors">&times;</button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1 text-sm text-gray-600 space-y-5 leading-relaxed">
                    <p>Welcome to <strong>VincenThinks</strong>. By registering for an account, you agree to comply with and be bound by the following terms and conditions, which govern your use of this web application in conjunction with the official policies of St. Vincent College of Cabuyao.</p>
                    
                    <div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">1. Data Privacy and Identity Verification</h4>
                        <p>To ensure a secure academic environment, we require identity verification. The identification document (School ID or Registration Form) you upload is processed securely by an AI system strictly to verify your name and student/teacher number. <strong>Your uploaded document is permanently deleted from our servers immediately after verification.</strong> We do not store or retain copies of your IDs.</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">2. Acceptable Use and Community Guidelines</h4>
                        <p>VincenThinks is an academic web application. You agree to interact respectfully with peers and faculty. The web application utilizes an automated AI moderation system. Content containing hate speech, harassment, explicit material, or academic dishonesty will be automatically flagged, blocked, and may result in disciplinary action or account suspension.</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">3. Institutional Rules</h4>
                        <p>All activities on this platform are considered an extension of the campus. Therefore, the rules, regulations, and sanctions outlined in the official St. Vincent College of Cabuyao Student and Faculty Handbooks fully apply to your conduct here.</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">4. Account Security</h4>
                        <p>You are responsible for maintaining the confidentiality of your account password and OTP codes. You agree to notify administrators immediately of any unauthorized use of your account.</p>
                    </div>
                </div>
                
                <div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end">
                    <button type="button" @click="showTermsModal = false; terms = true" class="px-6 py-2.5 bg-maroon-700 text-white font-bold rounded-lg hover:bg-maroon-800 transition-colors shadow-sm">
                        I Understand and Agree
                    </button>
                </div>
            </div>
        </div>

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

        {{-- 5. UPLOAD ID DOCUMENT --}}
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

        {{-- 6. PASSWORD --}}
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

        {{-- 8. TERMS AND CONDITIONS --}}
        <div class="mt-6 flex items-start">
            <div class="flex items-center h-5">
                <input id="terms" type="checkbox" name="terms" x-model="terms" required class="w-4 h-4 text-maroon-600 bg-white border-gray-300 rounded focus:ring-maroon-700 transition duration-150 ease-in-out cursor-pointer">
            </div>
            <div class="ml-3 text-sm text-gray-600">
                I agree to the 
                {{-- [UPDATED] Separated the button from the label so it triggers the modal, not the checkbox --}}
                <button type="button" @click.prevent="showTermsModal = true" class="text-maroon-700 font-bold hover:underline focus:outline-none">
                    Terms and Conditions
                </button>, 
                acknowledging that my data will be used securely for system operations and that the official rules and regulations of St. Vincent College of Cabuyao strictly apply within this platform.
            </div>
        </div>
        <x-input-error :messages="$errors->get('terms')" class="mt-2" />

        <div class="mt-8">
            <button 
                type="submit"
                x-bind:disabled="!isFormValid"
                class="w-full justify-center py-3 text-white font-bold rounded-lg transition-all text-base relative"
                :class="{
                    'bg-maroon-700 hover:bg-maroon-800 cursor-pointer': isFormValid && !isSubmitting,
                    'bg-gray-400 cursor-not-allowed opacity-50': !isFormValid || isSubmitting
                }"
            >
                <span x-show="!isSubmitting">{{ __('Register & Verify') }}</span>
                <span x-show="isSubmitting" style="display: none;">Processing...</span>
            </button>
        </div>

        <div class="mt-6 text-center text-sm text-gray-500">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-maroon-700 hover:text-maroon-900 hover:underline">Log in</a>
        </div>
    </form>
</x-guest-layout>