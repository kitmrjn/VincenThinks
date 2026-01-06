<x-admin-layout>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class='bx bx-cog mr-2 text-maroon-700'></i> System Settings
        </h1>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
            <i class='bx bx-check-circle text-xl mr-2'></i>
            <div>
                <p class="font-bold">Success</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- MAIN FORM START --}}
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        {{-- 1. Include Email Settings --}}
        @include('admin.settings.email')

        {{-- 2. Include Forum Settings (Note: Ensure this file is a partial, not a full page) --}}
        @include('admin.settings.forum')

        {{-- Save Button --}}
        <div class="flex justify-end pb-12 mt-8">
            <button type="submit" class="bg-maroon-700 hover:bg-maroon-800 text-white font-bold py-3 px-8 rounded-lg shadow-lg flex items-center transform transition hover:-translate-y-0.5">
                <i class='bx bx-save mr-2 text-xl'></i> Save Configuration
            </button>
        </div>

    </form>
</x-admin-layout>