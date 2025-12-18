<div x-show="activeModal === 'password_{{ $user->id }}'" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     style="display: none;">
    
    {{-- Backdrop --}}
    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-60 backdrop-blur-sm" aria-hidden="true" @click="activeModal = null"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal Content --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            
            {{-- Header --}}
            <div class="bg-gray-50 px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-bold text-gray-800 flex items-center">
                    <i class='bx bx-lock-open-alt text-orange-600 mr-2 bg-orange-100 p-1.5 rounded-lg'></i> 
                    Reset Password
                </h3>
                {{-- Close Button --}}
                <button type="button" @click="activeModal = null" class="text-gray-400 hover:text-gray-600 transition p-1 hover:bg-gray-200 rounded-full focus:outline-none">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-6">
                {{-- Warning Banner (Switched to Grid Layout for better wrapping) --}}
                <div class="mb-6 bg-orange-50 border border-orange-100 rounded-lg p-4 grid grid-cols-[auto_1fr] gap-3 items-start">
                    <i class='bx bx-error-circle text-orange-500 text-xl mt-0.5'></i>
                    
                    <div class="text-xs text-orange-800 leading-relaxed min-w-0">
                        <span class="font-bold block mb-1">Action Required</span>
                        <p class="break-words whitespace-normal">
                            This will set a new password for <span class="font-bold">{{ $user->name }}</span>. They will need this new password to log in.
                        </p>
                    </div>
                </div>

                {{-- FORM --}}
                <form id="reset-password-form-{{ $user->id }}" action="{{ route('admin.users.reset_password', $user->id) }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="form_target_id" value="{{ $user->id }}">
                    <input type="hidden" name="form_type" value="password">

                    <div class="space-y-5">
                        {{-- New Password --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">New Password</label>
                            <div class="relative">
                                <i class='bx bx-key absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg'></i>
                                <input type="password" name="password" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2.5 pl-10 pr-3 transition placeholder-gray-300" placeholder="Enter new password">
                            </div>
                            @error('password') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Confirm Password</label>
                            <div class="relative">
                                <i class='bx bx-check-double absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg'></i>
                                <input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2.5 pl-10 pr-3 transition placeholder-gray-300" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-100">
                <button type="button" @click="activeModal = null" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">
                    Cancel
                </button>
                <button type="submit" form="reset-password-form-{{ $user->id }}" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-6 py-2 bg-orange-600 text-sm font-bold text-white hover:bg-orange-700 focus:outline-none transition shadow-orange-200">
                    Update Password
                </button>
            </div>
        </div>
    </div>
</div>