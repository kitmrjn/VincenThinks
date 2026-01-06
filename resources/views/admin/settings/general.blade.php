<x-admin-layout>
    <h1 class="text-3xl font-light text-gray-800 mb-8">General Rules</h1>
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <p class="font-bold">Success</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <form action="{{ route('admin.settings.general.update') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        @csrf
        
        {{-- Time Limit --}}
        <div class="mb-8">
            <label class="block text-sm font-bold text-gray-800 mb-1">Content Edit Time Limit</label>
            <p class="text-xs text-gray-500 mb-3">How many seconds a user has to edit their post after publishing.</p>
            <div class="flex items-center">
                <input type="number" name="edit_time_limit" value="{{ $settings['edit_time_limit'] ?? '150' }}" class="w-32 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-maroon-700 focus:border-maroon-700 p-2.5 mr-2">
                <span class="text-gray-500 text-sm">Seconds</span>
            </div>
        </div>

        <hr class="border-gray-100 mb-8">

        {{-- Verification Toggle --}}
        <div class="flex items-center justify-between mb-2">
            <div>
                <label class="block text-sm font-bold text-gray-800">Require Email Verification</label>
                <p class="text-xs text-gray-500 mt-1">If enabled, new users must verify their email before posting.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="verification_required" value="1" class="sr-only peer" {{ ($settings['verification_required'] ?? '1') == '1' ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-maroon-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
            </label>
        </div>

        <div class="mt-10 flex justify-end">
            <button type="submit" class="bg-maroon-700 hover:bg-maroon-800 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                Save Changes
            </button>
        </div>
    </form>
</x-admin-layout>