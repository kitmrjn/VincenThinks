<div x-show="activeModal === 'password_{{ $user->id }}'" 
     x-teleport="body"
     class="fixed inset-0 z-[100] overflow-y-auto" 
     style="display: none;">

    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="activeModal = null"></div>
        
        <div class="bg-white rounded-xl shadow-xl transform transition-all sm:max-w-md sm:w-full z-[110] overflow-hidden">
            <form method="POST" action="{{ route('admin.users.reset_password', $user->id) }}" class="p-6 text-left">
                @csrf
                
                {{-- Hidden Inputs --}}
                <input type="hidden" name="form_target_id" value="{{ $user->id }}">
                <input type="hidden" name="form_type" value="password">

                <h3 class="text-lg font-bold text-gray-900 mb-2 text-orange-600">Reset Password</h3>
                <p class="text-sm text-gray-500 mb-4">Set a new password for {{ $user->name }}.</p>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                        @error('password') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-bold">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>