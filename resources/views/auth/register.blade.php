<x-guest-layout>
    <div class="text-left mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Create an Account</h2>
        <p class="text-gray-500 mt-2 text-sm">Join the discussion today. It's free and easy.</p>
    </div>

    {{-- Global Error Banner --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex items-center">
                <i class='bx bx-error-circle text-red-500 text-2xl mr-3'></i>
                <h3 class="text-red-800 font-bold">Registration Failed</h3>
            </div>
            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Calculate the starting step based on errors --}}
    @php
        $initialStep = 1;
        if ($errors->has('password') || $errors->has('terms')) {
            $initialStep = 3;
        } elseif ($errors->has('id_document') || $errors->has('student_number') || $errors->has('teacher_number') || $errors->has('course_id') || $errors->has('department_id')) {
            $initialStep = 2;
        }
    @endphp

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
          x-data="{ 
              step: {{ $initialStep }}, 
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
                  const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[\w\W]{8,}$/;
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
              hasReadTerms: false,
              checkScroll(e) {
                  const element = e.target;
                  if (element.scrollHeight - element.scrollTop <= element.clientHeight + 50) {
                      this.hasReadTerms = true;
                  }
              },

              get isStep1Valid() { return this.isNameValid === true && this.isEmailValid === true; },
              
              get isStep2Valid() {
                  if (this.role === 'student') {
                      return this.isStudentNumberValid === true && this.course !== '' && this.hasDocument === true;
                  } else {
                      return this.isTeacherNumberValid === true && this.department !== '' && this.hasDocument === true;
                  }
              },

              get isStep3Valid() { return this.isPasswordValid === true && this.isConfirmValid === true && this.terms === true; },
              get isFormValid() { return this.isStep1Valid && this.isStep2Valid && this.isStep3Valid; },

              isSubmitting: false,
              loadingText: 'Securely uploading your document...',
              startLoading() {
                  if(this.isFormValid) {
                      this.isSubmitting = true;
                      let idx = 0;
                      const messages = [
                          'Securely uploading your document...',
                          'Our AI is reading your ID card...',
                          'Cross-checking Name and ID Number...',
                          'Verifying school year validity...',
                          'Almost done, finalizing registration...'
                      ];
                      setInterval(() => {
                          idx++;
                          if(idx < messages.length) { this.loadingText = messages[idx]; }
                      }, 2500);
                  }
              }
          }"
          x-init="validateName(); validateEmail(); validateStudentNumber(); validateTeacherNumber();" 
          @submit="startLoading()"
    >
        @csrf

        {{-- LOADING OVERLAY --}}
        <div x-show="isSubmitting" style="display: none;" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-white/90 backdrop-blur-sm">
            <i class='bx bx-loader-alt bx-spin text-maroon-700 text-7xl mb-6'></i>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Verifying Identity</h3>
            <p class="text-lg text-gray-600 font-medium animate-pulse" x-text="loadingText"></p>
            <p class="text-sm text-gray-400 mt-6 max-w-sm text-center">Please do not refresh this page. Verification usually takes 5-10 seconds.</p>
        </div>

        {{-- TERMS MODAL --}}
        <div x-show="showTermsModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div @click.away="showTermsModal = false" class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h3 class="text-xl font-bold text-gray-900">Terms and Conditions</h3>
                    <button type="button" @click="showTermsModal = false" class="text-gray-400 hover:text-maroon-700 text-2xl font-bold focus:outline-none">&times;</button>
                </div>
                
                {{-- Robust Content --}}
                <div @scroll="checkScroll" class="p-6 overflow-y-auto flex-1 text-sm text-gray-600 space-y-5 leading-relaxed">
                    <p class="italic bg-gray-50 p-3 rounded border-l-4 border-maroon-700">
                        Last Updated: February 2026. By creating an account on VincenThinks, you agree to these legally binding terms.
                    </p>

                    <div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">1. Eligibility and Account Security</h4>
                        <p>Access is restricted to active students and faculty of <strong>St. Vincent College of Cabuyao</strong>. You are responsible for maintaining the confidentiality of your credentials.</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">2. AI-Powered Identity Verification</h4>
                        <p>To ensure a safe environment, we use AI to verify school IDs. By uploading a document, you grant VincenThinks temporary permission to process this image. <strong>Privacy Guarantee:</strong> Documents are processed in-memory and are permanently purged immediately after verification.</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">3. Content Moderation & AI Ethics</h4>
                        <p>VincenThinks utilizes automated moderation to scan for hate speech, harassment, and explicit material. Accounts found repeatedly violating community standards may be suspended without notice.</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">4. Prohibited Conduct</h4>
                        <p>You agree not to: (a) impersonate others; (b) use automated bots; (c) bypass verification with fraudulent documents; or (d) engage in dicing or sharing private information of peers.</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">5. Limitation of Liability</h4>
                        <p>VincenThinks is provided "as-is." While we strive for high uptime, we are not liable for data loss or the opinions expressed by users on the platform.</p>
                    </div>

                    <div class="bg-blue-50 p-3 rounded border border-blue-200">
                        <h4 class="font-bold text-blue-900 text-sm mb-1 uppercase tracking-wider">Institutional Notice:</h4>
                        <p class="text-blue-800 text-xs">Violations of these terms may be reported to the Office of Student Affairs and may result in disciplinary action under the Student Manual.</p>
                    </div>
                </div>

                <div class="p-5 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p x-show="!hasReadTerms" class="text-xs text-maroon-600 animate-pulse">Please scroll to the bottom to enable agreement.</p>
                    <button type="button" 
                            @click="showTermsModal = false; terms = true" 
                            :disabled="!hasReadTerms"
                            :class="hasReadTerms ? 'bg-maroon-700 hover:bg-maroon-800' : 'bg-gray-400 cursor-not-allowed'"
                            class="w-full sm:w-auto px-6 py-2.5 text-white font-bold rounded-lg transition-colors shadow-sm">
                        I Understand and Agree
                    </button>
                </div>
            </div>
        </div>

        {{-- STEP PROGRESS BAR --}}
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

        {{-- STEP 1: BASICS --}}
        <div x-show="step === 1" x-transition.opacity.duration.300ms>
            <div>
                <x-input-label for="name" :value="__('Full Name (Must match your ID)')" />
                <div class="relative mt-1">
                    <x-text-input id="name" class="block w-full p-3 pr-10 rounded-lg shadow-sm border-2"
                        x-bind:class="{'border-gray-300 focus:border-maroon-700': isNameValid === null, 'border-green-500 text-green-700': isNameValid === true, 'border-red-500 text-red-700': isNameValid === false}"
                        type="text" name="name" x-model="name" @input="validateName()" autofocus placeholder="John Doe" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i x-show="isNameValid === null" class='bx bx-user text-gray-400 text-xl'></i>
                        <i x-show="isNameValid === true" class='bx bx-check text-green-600 text-2xl'></i>
                        <i x-show="isNameValid === false" class='bx bx-x text-red-600 text-2xl'></i>
                    </div>
                </div>
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
                        <i x-show="isEmailValid === true" class='bx bx-check text-green-600 text-2xl'></i>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="member_type" :value="__('I am a...')" />
                <select id="member_type" name="member_type" x-model="role" class="block mt-1 w-full p-3 border-gray-300 focus:border-maroon-700 rounded-lg shadow-sm bg-white">
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="button" @click="step = 2" :disabled="!isStep1Valid"
                    class="px-6 py-3 text-white font-bold rounded-lg transition-all"
                    :class="isStep1Valid ? 'bg-maroon-700 hover:bg-maroon-800' : 'bg-gray-400 cursor-not-allowed'">
                    Next Step <i class='bx bx-right-arrow-alt align-middle ml-1'></i>
                </button>
            </div>
        </div>

        {{-- STEP 2: IDENTITY --}}
        <div x-show="step === 2" style="display: none;" x-transition.opacity.duration.300ms>
            <div x-show="role === 'student'" class="space-y-4">
                <div>
                    <x-input-label for="student_number" :value="__('Student Number')" />
                    <x-text-input id="student_number" class="block w-full mt-1 p-3 rounded-lg border-2"
                        x-bind:class="{'border-gray-300': isStudentNumberValid === null, 'border-green-500': isStudentNumberValid === true, 'border-red-500': isStudentNumberValid === false}"
                        type="text" name="student_number" x-model="studentNumber" @input="validateStudentNumber()" placeholder="AY2023-00123" />
                </div>
                <div>
                    <x-input-label for="course_id" :value="__('Course / Strand')" />
                    <select id="course_id" name="course_id" x-model="course" class="block mt-1 w-full p-3 border-gray-300 rounded-lg bg-white">
                        <option value="" disabled>Select your course...</option>
                        @isset($courses)
                            @foreach($courses as $type => $group)
                                <optgroup label="{{ $type }}">
                                    @foreach($group as $c)
                                        <option value="{{ $c->id }}">{{ $c->acronym }} - {{ $c->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>

            <div x-show="role === 'teacher'" class="space-y-4">
                <div>
                    <x-input-label for="teacher_number" :value="__('Teacher Number')" />
                    <x-text-input id="teacher_number" class="block w-full mt-1 p-3 rounded-lg border-2"
                        x-bind:class="{'border-gray-300': isTeacherNumberValid === null, 'border-green-500': isTeacherNumberValid === true, 'border-red-500': isTeacherNumberValid === false}"
                        type="text" name="teacher_number" x-model="teacherNumber" @input="validateTeacherNumber()" placeholder="AY2023-00123" />
                </div>
                <div>
                    <x-input-label for="department_id" :value="__('Department / Faculty')" />
                    <select id="department_id" name="department_id" x-model="department" class="block mt-1 w-full p-3 border-gray-300 rounded-lg bg-white">
                        <option value="" disabled>Select Department...</option>
                        @isset($departments)
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>

            <div class="mt-6 p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 text-center">
                <x-input-label for="id_document" :value="__('Upload School ID or Registration Form')" class="text-maroon-700 font-bold" />
                <p class="text-xs text-gray-500 mb-2">Our AI will verify your identity automatically.</p>
                <input type="file" id="id_document" name="id_document" accept="image/*" @change="validateDocument"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white p-2">
            </div>

            <div class="mt-8 flex justify-between">
                <button type="button" @click="step = 1" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-lg transition-all">Back</button>
                <button type="button" @click="step = 3" :disabled="!isStep2Valid"
                    class="px-6 py-3 text-white font-bold rounded-lg transition-all"
                    :class="isStep2Valid ? 'bg-maroon-700 hover:bg-maroon-800' : 'bg-gray-400 cursor-not-allowed'">
                    Next Step
                </button>
            </div>
        </div>

        {{-- STEP 3: SECURITY --}}
        <div x-show="step === 3" style="display: none;" x-transition.opacity.duration.300ms>
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block w-full mt-1 p-3 border-2"
                    x-bind:class="{'border-gray-300': isPasswordValid === null, 'border-green-500': isPasswordValid === true, 'border-red-500': isPasswordValid === false}"
                    type="password" name="password" x-model="password" @input="validatePassword()" placeholder="••••••••" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block w-full mt-1 p-3 border-2"
                    x-bind:class="{'border-gray-300': isConfirmValid === null, 'border-green-500': isConfirmValid === true, 'border-red-500': isConfirmValid === false}"
                    type="password" name="password_confirmation" x-model="passwordConfirmation" @input="validateConfirmPassword()" placeholder="••••••••" />
            </div>

            <div class="mt-6 flex items-start">
                <input id="terms" type="checkbox" name="terms" x-model="terms" class="w-4 h-4 text-maroon-600 border-gray-300 rounded focus:ring-maroon-700 mt-1">
                <div class="ml-3 text-sm text-gray-600">
                    I agree to the <button type="button" @click.prevent="showTermsModal = true" class="text-maroon-700 font-bold hover:underline">Terms and Conditions</button>.
                </div>
            </div>

            <div class="mt-8 flex justify-between">
                <button type="button" @click="step = 2" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-lg">Back</button>
                <button type="submit" :disabled="!isFormValid"
                    class="px-8 py-3 text-white font-bold rounded-lg transition-all"
                    :class="isFormValid && !isSubmitting ? 'bg-maroon-700 hover:bg-maroon-800' : 'bg-gray-400 cursor-not-allowed'">
                    <span x-show="!isSubmitting">Register & Verify</span>
                    <span x-show="isSubmitting" style="display: none;">Processing...</span>
                </button>
            </div>
        </div>

        <div class="mt-8 text-center text-sm text-gray-500">
            Already have an account? <a href="{{ route('login') }}" class="font-bold text-maroon-700 hover:underline">Log in</a>
        </div>
    </form>
</x-guest-layout>