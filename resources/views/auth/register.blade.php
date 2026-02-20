<x-guest-layout>
    <div class="text-left mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Create an Account</h2>
        <p class="text-gray-500 mt-2 text-sm">Join the discussion today. It's free and easy.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
          x-data="{ 
              step: 1, // [NEW] Track current step
              
              role: '{{ old('member_type', 'student') }}', 
              course: '{{ old('course_id') }}',
              department: '{{ old('department_id') }}',
              
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
              showTermsModal: false,

              // [NEW] Step Validation Logic
              get isStep1Valid() {
                  return this.isNameValid === true && this.isEmailValid === true;
              },
              
              get isStep2Valid() {
                  if (this.role === 'student') {
                      return this.isStudentNumberValid === true && this.course !== '' && this.hasDocument === true;
                  } else {
                      return this.isTeacherNumberValid === true && this.department !== '' && this.hasDocument === true;
                  }
              },

              get isStep3Valid() {
                  return this.isPasswordValid === true && this.isConfirmValid === true && this.terms === true;
              },

              get isFormValid() {
                  return this.isStep1Valid && this.isStep2Valid && this.isStep3Valid;
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

        {{-- LOADING OVERLAY & MODAL (Keep Existing) --}}
        <div x-show="isSubmitting" style="display: none;" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-white/90 backdrop-blur-sm transition-opacity duration-300">
            <i class='bx bx-loader-alt bx-spin text-maroon-700 text-7xl mb-6'></i>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Verifying Identity</h3>
            <p class="text-lg text-gray-600 font-medium animate-pulse" x-text="loadingText"></p>
            <p class="text-sm text-gray-400 mt-6 max-w-sm text-center">Please do not refresh or close this page. This process usually takes 5-10 seconds.</p>
        </div>

        <div x-show="showTermsModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-opacity">
            <div @click.away="showTermsModal = false" class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden transform transition-all">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h3 class="text-xl font-bold text-gray-900">Terms and Conditions</h3>
                    <button type="button" @click="showTermsModal = false" class="text-gray-400 hover:text-maroon-700 text-2xl font-bold focus:outline-none transition-colors">&times;</button>
                </div>
                <div class="p-6 overflow-y-auto flex-1 text-sm text-gray-600 space-y-5 leading-relaxed">
                    <p>Welcome to <strong>VincenThinks</strong>. By registering for an account, you agree to comply with and be bound by the following terms and conditions...</p>
                    <div><h4 class="font-bold text-gray-900 text-base mb-1">1. Data Privacy and Identity Verification</h4><p>Your uploaded document is permanently deleted from our servers immediately after verification.</p></div>
                    <div><h4 class="font-bold text-gray-900 text-base mb-1">2. Acceptable Use</h4><p>The platform utilizes an automated AI moderation system. Content containing hate speech or harassment will be flagged.</p></div>
                    <div><h4 class="font-bold text-gray-900 text-base mb-1">3. Institutional Rules</h4><p>The rules and regulations of St. Vincent College of Cabuyao fully apply to your conduct here.</p></div>
                </div>
                <div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end">
                    <button type="button" @click="showTermsModal = false; terms = true" class="px-6 py-2.5 bg-maroon-700 text-white font-bold rounded-lg hover:bg-maroon-800 transition-colors shadow-sm">I Understand and Agree</button>
                </div>
            </div>
        </div>

        {{-- [NEW] STEP PROGRESS BAR --}}
        <div class="mb-8 relative">
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                <div :style="'width: ' + ((step / 3) * 100) + '%'" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-maroon-700 transition-all duration-500"></div>
            </div>
            <div class="flex justify-between text-xs font-bold text-gray-400">
                <span :class="{'text-maroon-700': step >= 1}">Basics</span>
                <span :class="{'text-maroon-700': step >= 2}">Identity</span>
                <span :class="{'text-maroon-700': step === 3}">Security</span>
            </div>
        </div>

        {{-- ================= STEP 1: BASICS ================= --}}
        <div x-show="step === 1" x-transition.opacity.duration.300ms>
            <div>
                <x-input-label for="name" :value="__('Full Name (Must match your ID)')" />
                <div class="relative mt-1">
                    <x-text-input id="name" class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2"
                        x-bind:class="{'border-gray-300 focus:border-maroon-700': isNameValid === null, 'border-green-500 text-green-700': isNameValid === true, 'border-red-500 text-red-700': isNameValid === false}"
                        type="text" name="name" x-model="name" @input="validateName()" autofocus placeholder="John Doe" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i x-show="isNameValid === null" class='bx bx-user text-gray-400 text-xl'></i>
                        <i x-show="isNameValid === true" class='bx bx-check text-green-600 text-2xl animate-bounce' style="animation-iteration-count: 1;"></i>
                        <i x-show="isNameValid === false" class='bx bx-x text-red-600 text-2xl animate-pulse'></i>
                    </div>
                </div>
                <p x-show="isNameValid === false" class="text-red-500 text-xs mt-1" style="display: none;">Must be at least 2 words.</p>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" :value="__('Email Address')" />
                <div class="relative mt-1">
                    <x-text-input id="email" class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2"
                        x-bind:class="{'border-gray-300 focus:border-maroon-700': isEmailValid === null, 'border-green-500 text-green-700': isEmailValid === true, 'border-red-500 text-red-700': isEmailValid === false}"
                        type="email" name="email" x-model="email" @input="validateEmail()" placeholder="name@school.edu" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i x-show="isEmailValid === null" class='bx bx-envelope text-gray-400 text-xl'></i>
                        <i x-show="isEmailValid === true" class='bx bx-check text-green-600 text-2xl animate-bounce' style="animation-iteration-count: 1;"></i>
                        <i x-show="isEmailValid === false" class='bx bx-x text-red-600 text-2xl animate-pulse'></i>
                    </div>
                </div>
                <p x-show="isEmailValid === false" class="text-red-500 text-xs mt-1" style="display: none;">Please enter a valid email.</p>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="member_type" :value="__('I am a...')" />
                <select id="member_type" name="member_type" x-model="role" @change="setTimeout(() => { validateStudentNumber(); validateTeacherNumber(); }, 100)" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 rounded-lg shadow-sm bg-white">
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                </select>
                <x-input-error :messages="$errors->get('member_type')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end">
                <button type="button" @click="step = 2" :disabled="!isStep1Valid"
                    class="px-6 py-3 text-white font-bold rounded-lg transition-all"
                    :class="isStep1Valid ? 'bg-maroon-700 hover:bg-maroon-800' : 'bg-gray-400 cursor-not-allowed'">
                    Next Step <i class='bx bx-right-arrow-alt align-middle ml-1'></i>
                </button>
            </div>
        </div>

        {{-- ================= STEP 2: IDENTITY ================= --}}
        <div x-show="step === 2" style="display: none;" x-transition.opacity.duration.300ms>
            
            {{-- Student Section --}}
            <div x-show="role === 'student'" class="space-y-4">
                <div>
                    <x-input-label for="student_number" :value="__('Student Number')" />
                    <div class="relative mt-1">
                        <x-text-input id="student_number" class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2"
                            x-bind:class="{'border-gray-300 focus:border-maroon-700': isStudentNumberValid === null, 'border-green-500 text-green-700': isStudentNumberValid === true, 'border-red-500 text-red-700': isStudentNumberValid === false}"
                            type="text" name="student_number" x-model="studentNumber" @input="validateStudentNumber()" placeholder="AY2023-00123" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i x-show="isStudentNumberValid === true" class='bx bx-check text-green-600 text-2xl'></i>
                            <i x-show="isStudentNumberValid === false" class='bx bx-x text-red-600 text-2xl'></i>
                        </div>
                    </div>
                    <p x-show="isStudentNumberValid === false" class="text-red-500 text-xs mt-1" style="display: none;">Format: AYxxxx-xxxxx</p>
                </div>
                <div>
                    <x-input-label for="course_id" :value="__('Course / Strand')" />
                    <select id="course_id" name="course_id" x-model="course" class="block mt-1 w-full p-3 border-gray-300 rounded-lg shadow-sm bg-white">
                        <option value="" disabled>Select your course...</option>
                        @if(isset($courses))
                            @foreach($courses as $type => $group)
                                <optgroup label="{{ $type }}">
                                    @foreach($group as $c)
                                        <option value="{{ $c->id }}">{{ $c->acronym }} - {{ $c->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            {{-- Teacher Section --}}
            <div x-show="role === 'teacher'" class="space-y-4">
                <div>
                    <x-input-label for="teacher_number" :value="__('Teacher Number')" />
                    <div class="relative mt-1">
                        <x-text-input id="teacher_number" class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2"
                            x-bind:class="{'border-gray-300 focus:border-maroon-700': isTeacherNumberValid === null, 'border-green-500 text-green-700': isTeacherNumberValid === true, 'border-red-500 text-red-700': isTeacherNumberValid === false}"
                            type="text" name="teacher_number" x-model="teacherNumber" @input="validateTeacherNumber()" placeholder="AY2023-00123" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i x-show="isTeacherNumberValid === true" class='bx bx-check text-green-600 text-2xl'></i>
                            <i x-show="isTeacherNumberValid === false" class='bx bx-x text-red-600 text-2xl'></i>
                        </div>
                    </div>
                </div>
                <div>
                    <x-input-label for="department_id" :value="__('Department / Faculty')" />
                    <select id="department_id" name="department_id" x-model="department" class="block mt-1 w-full p-3 border-gray-300 rounded-lg shadow-sm bg-white">
                        <option value="" disabled>Select Department...</option>
                        @if(isset($departments))
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            {{-- Document Upload --}}
            <div class="mt-6 p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                <x-input-label for="id_document" :value="__('Upload School ID or Registration Form')" class="text-maroon-700 font-bold" />
                <p class="text-xs text-gray-500 mb-2">Our AI will verify your Name and ID number automatically.</p>
                <input type="file" id="id_document" name="id_document" accept="image/jpeg, image/png, image/jpg" @change="validateDocument"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white p-2">
                <x-input-error :messages="$errors->get('id_document')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-between">
                <button type="button" @click="step = 1" class="px-6 py-3 text-gray-600 font-bold rounded-lg hover:bg-gray-100 transition-all">
                    <i class='bx bx-left-arrow-alt align-middle mr-1'></i> Back
                </button>
                <button type="button" @click="step = 3" :disabled="!isStep2Valid"
                    class="px-6 py-3 text-white font-bold rounded-lg transition-all"
                    :class="isStep2Valid ? 'bg-maroon-700 hover:bg-maroon-800' : 'bg-gray-400 cursor-not-allowed'">
                    Next Step <i class='bx bx-right-arrow-alt align-middle ml-1'></i>
                </button>
            </div>
        </div>

        {{-- ================= STEP 3: SECURITY ================= --}}
        <div x-show="step === 3" style="display: none;" x-transition.opacity.duration.300ms>
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <div class="relative mt-1">
                    <x-text-input id="password" class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2"
                        x-bind:class="{'border-gray-300 focus:border-maroon-700': isPasswordValid === null, 'border-green-500 text-green-700': isPasswordValid === true, 'border-red-500 text-red-700': isPasswordValid === false}"
                        type="password" name="password" x-model="password" @input="validatePassword()" placeholder="••••••••" />
                </div>
                <p x-show="isPasswordValid === false" class="text-red-500 text-xs mt-1" style="display: none;">Must be 8+ chars, with Uppercase, Lowercase, Number, & Symbol.</p>
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <div class="relative mt-1">
                    <x-text-input id="password_confirmation" class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2"
                        x-bind:class="{'border-gray-300 focus:border-maroon-700': isConfirmValid === null, 'border-green-500 text-green-700': isConfirmValid === true, 'border-red-500 text-red-700': isConfirmValid === false}"
                        type="password" name="password_confirmation" x-model="passwordConfirmation" @input="validateConfirmPassword()" placeholder="••••••••" />
                </div>
                <p x-show="isConfirmValid === false" class="text-red-500 text-xs mt-1" style="display: none;">Passwords do not match.</p>
            </div>

            <div class="mt-6 flex items-start">
                <div class="flex items-center h-5">
                    <input id="terms" type="checkbox" name="terms" x-model="terms" class="w-4 h-4 text-maroon-600 border-gray-300 rounded focus:ring-maroon-700 cursor-pointer">
                </div>
                <div class="ml-3 text-sm text-gray-600">
                    I agree to the <button type="button" @click.prevent="showTermsModal = true" class="text-maroon-700 font-bold hover:underline">Terms and Conditions</button>.
                </div>
            </div>

            <div class="mt-8 flex justify-between">
                <button type="button" @click="step = 2" class="px-6 py-3 text-gray-600 font-bold rounded-lg hover:bg-gray-100 transition-all">
                    <i class='bx bx-left-arrow-alt align-middle mr-1'></i> Back
                </button>
                <button type="submit" :disabled="!isFormValid"
                    class="px-8 py-3 text-white font-bold rounded-lg transition-all"
                    :class="isFormValid && !isSubmitting ? 'bg-maroon-700 hover:bg-maroon-800' : 'bg-gray-400 cursor-not-allowed'">
                    <span x-show="!isSubmitting">Register & Verify <i class='bx bx-check-shield align-middle ml-1'></i></span>
                    <span x-show="isSubmitting" style="display: none;">Processing...</span>
                </button>
            </div>
        </div>

        <div class="mt-8 text-center text-sm text-gray-500">
            Already have an account? <a href="{{ route('login') }}" class="font-bold text-maroon-700 hover:underline">Log in</a>
        </div>
    </form>
</x-guest-layout>