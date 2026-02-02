<x-admin-layout>
    {{-- TOAST NOTIFICATIONS (Success & Error) --}}
    <div class="fixed top-5 right-5 z-[120] space-y-3 pointer-events-none">
        
        {{-- Success Message --}}
        @if (session('success'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 4000)" 
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="pointer-events-auto bg-white border-l-4 border-green-500 shadow-xl rounded-lg p-4 pr-8 flex items-start gap-3 min-w-[300px]">
                
                <div class="text-green-500 mt-0.5">
                    <i class='bx bx-check-circle text-2xl'></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm">Success</h4>
                    <p class="text-xs text-gray-600 mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="absolute top-2 right-2 text-gray-300 hover:text-gray-500 transition">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 5000)" 
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="pointer-events-auto bg-white border-l-4 border-red-500 shadow-xl rounded-lg p-4 pr-8 flex items-start gap-3 min-w-[300px]">
                
                <div class="text-red-500 mt-0.5">
                    <i class='bx bx-x-circle text-2xl'></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm">Error</h4>
                    <p class="text-xs text-gray-600 mt-0.5">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="absolute top-2 right-2 text-gray-300 hover:text-gray-500 transition">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
        @endif
    </div>

    {{-- SHARED STATE --}}
    <div x-data="{ 
        activeModal: null, 
        targetName: '', 
        targetUrl: '',
        targetId: '', 
        actionType: '' 
    }" class="space-y-6">
        
        {{-- Header & Search --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">User Management</h2>
                <p class="text-gray-500 text-sm">Manage student and teacher accounts.</p>
            </div>
            <form method="GET" action="{{ route('admin.users') }}" class="relative w-full md:w-64">
                <i class='bx bx-search absolute left-3 top-3 text-gray-400'></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search name, email, ID..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 transition">
            </form>
        </div>

        {{-- Global Validation Error Alert --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class='bx bx-error-circle text-red-500 text-xl'></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Operation Failed</h3>
                        <div class="mt-1 text-sm text-red-700">
                            <p>Please check the forms for validation errors.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Filter Tabs (Scrollable on Mobile) --}}
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="-mb-px flex space-x-8 min-w-max" aria-label="Tabs">
                <a href="{{ route('admin.users') }}" class="{{ !request('role') ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">All Users</a>
                <a href="{{ route('admin.users', ['role' => 'teacher']) }}" class="{{ request('role') == 'teacher' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Teachers</a>
                <a href="{{ route('admin.users', ['role' => 'student']) }}" class="{{ request('role') == 'student' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Students</a>
                <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="{{ request('role') == 'admin' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Admins</a>
                <a href="{{ route('admin.users', ['role' => 'banned']) }}" class="{{ request('role') == 'banned' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Banned</a>
            </nav>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Added table-fixed on mobile to force width containment --}}
            <table class="w-full divide-y divide-gray-200 table-fixed md:table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        {{-- Identity: Takes remaining space on mobile --}}
                        <th class="px-4 py-3 md:px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-auto">User Identity</th>
                        
                        {{-- Hide on Mobile --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell w-1/4">Role & ID</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Activity</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Status</th>
                        
                        {{-- Actions: Fixed width on mobile (approx 5rem) to ensure visibility --}}
                        <th class="px-4 py-3 md:px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-20 md:w-auto">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition" x-data="{ openDropdown: false }">
                            
                            {{-- User Identity Column --}}
                            {{-- Removed 'whitespace-nowrap' so it respects width --}}
                            <td class="px-4 py-4 md:px-6 align-top">
                                <div class="flex items-start overflow-hidden"> {{-- Added overflow-hidden to contain truncated text --}}
                                    <div class="flex-shrink-0 h-10 w-10 mt-1 mr-3">
                                        @if($user->avatar)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->avatar) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-maroon-100 flex items-center justify-center text-maroon-700 font-bold text-sm">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1"> {{-- min-w-0 required for flex child truncation --}}
                                        <div class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 truncate">{{ $user->email }}</div>
                                        
                                        {{-- Mobile ONLY Info (Stacked) --}}
                                        <div class="md:hidden mt-2 space-y-1">
                                            {{-- Role Badge --}}
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $user->is_admin ? 'bg-red-100 text-red-800' : ($user->member_type == 'teacher' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ $user->is_admin ? 'Admin' : ucfirst($user->member_type) }}
                                            </span>
                                            
                                            {{-- ID Number --}}
                                            <div class="text-xs text-gray-400">
                                                @if($user->member_type == 'student')
                                                    {{ $user->student_number }}
                                                @else
                                                    {{ $user->teacher_number }}
                                                @endif
                                            </div>

                                            {{-- Status Badge (Mobile) --}}
                                            <div>
                                                @if($user->is_banned)
                                                    <span class="text-xs font-bold text-red-600">Banned</span>
                                                @elseif($user->email_verified_at)
                                                    <span class="text-xs font-bold text-green-600">Verified</span>
                                                @else
                                                    <span class="text-xs font-bold text-yellow-600">Unverified</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Role & ID (Desktop Only) --}}
                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell align-top">
                                <div class="flex flex-col">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full w-fit mb-1
                                        {{ $user->is_admin ? 'bg-red-100 text-red-800' : ($user->member_type == 'teacher' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $user->is_admin ? 'Administrator' : ucfirst($user->member_type) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        @if($user->member_type == 'student')
                                            {{ $user->student_number }} • {{ $user->course->acronym ?? 'N/A' }}
                                        @else
                                            {{ $user->teacher_number }} • {{ $user->departmentInfo->acronym ?? ($user->departmentInfo->name ?? 'N/A') }}
                                        @endif
                                    </span>
                                </div>
                            </td>

                            {{-- Activity (Desktop Only) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 hidden md:table-cell align-top">
                                <span class="font-bold text-gray-800">{{ $user->questions_count }}</span> Q / 
                                <span class="font-bold text-gray-800">{{ $user->answers_count }}</span> A
                            </td>

                            {{-- Status (Desktop Only) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center hidden md:table-cell align-top">
                                @if($user->is_banned)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-800 text-white">Banned</span>
                                @elseif($user->email_verified_at)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Verified</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Unverified</span>
                                @endif
                            </td>

                            {{-- Actions (Always Visible) --}}
                            <td class="px-4 py-4 md:px-6 whitespace-nowrap text-right text-sm font-medium align-top">
                                <div class="flex justify-end space-x-2">
                                    
                                    {{-- TRIGGER: Ban/Unban --}}
                                    <button @click="activeModal = 'ban'; targetName = '{{ $user->name }}'; targetUrl = '{{ route('admin.users.ban', $user->id) }}'; actionType = '{{ $user->is_banned ? 'unban' : 'ban' }}'" 
                                            class="text-{{ $user->is_banned ? 'green' : 'red' }}-600 hover:text-{{ $user->is_banned ? 'green' : 'red' }}-900" 
                                            title="{{ $user->is_banned ? 'Unban' : 'Ban' }}">
                                        <i class='bx {{ $user->is_banned ? 'bx-lock-open' : 'bx-block' }} text-xl'></i>
                                    </button>

                                    {{-- Dropdown Trigger --}}
                                    <div class="relative">
                                        <button @click="openDropdown = !openDropdown" @click.away="openDropdown = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class='bx bx-dots-vertical-rounded text-xl'></i>
                                        </button>
                                        
                                        <div x-show="openDropdown" 
                                             class="absolute right-0 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100 
                                                    {{ $loop->last ? 'bottom-full mb-2 origin-bottom-right' : 'mt-2 origin-top-right' }}" 
                                             style="display: none;">
                                             
                                            <button @click="activeModal = 'edit_{{ $user->id }}'; openDropdown = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition"><i class='bx bx-edit-alt mr-2 text-blue-600'></i> Edit Info</button>
                                            
                                            <button @click="activeModal = 'password_{{ $user->id }}'; openDropdown = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition"><i class='bx bx-key mr-2 text-orange-600'></i> Reset Password</button>

                                            @if(!$user->email_verified_at)
                                                <form method="POST" action="{{ route('admin.users.verify', $user->id) }}">
                                                    @csrf
                                                    <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"><i class='bx bx-check-circle mr-2 text-green-600'></i> Force Verify</button>
                                                </form>
                                            @endif
                                            
                                            @if(!$user->is_admin)
                                                <button @click="activeModal = 'promote'; targetName = '{{ $user->name }}'; targetUrl = '{{ route('admin.users.promote', $user->id) }}'; openDropdown = false" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition">
                                                        <i class='bx bxs-shield-alt-2 mr-2 text-yellow-600'></i> Make Admin
                                                </button>
                                            @endif
                                            
                                            <div class="border-t border-gray-100 my-1"></div>

                                            <button @click="activeModal = 'delete'; targetName = '{{ $user->name }}'; targetUrl = '{{ route('admin.users.delete', $user->id) }}'; openDropdown = false" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                                    <i class='bx bx-trash mr-2'></i> Delete Account
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <template x-if="activeModal === 'edit_{{ $user->id }}'">
                                    @include('admin.partials.edit-user-modal', ['user' => $user, 'allDepartments' => $allDepartments, 'courses' => $courses])
                                </template>

                                <template x-if="activeModal === 'password_{{ $user->id }}'">
                                    @include('admin.partials.password-user-modal', ['user' => $user])
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                <i class='bx bx-user-x text-4xl mb-2'></i>
                                <p>No users found matching your search.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="mt-4">
            {{ $users->appends(request()->query())->links('partials.pagination') }}
        </div>

        {{-- SHARED MODALS (Ban, Promote, Delete) --}}
        <div x-show="activeModal === 'ban'" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="activeModal = null"></div>
                <div class="bg-white rounded-xl shadow-xl transform transition-all sm:max-w-sm sm:w-full z-[110] overflow-hidden p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" 
                         :class="actionType === 'ban' ? 'bg-red-100' : 'bg-green-100'">
                        <i class='bx text-2xl' :class="actionType === 'ban' ? 'bx-block text-red-600' : 'bx-check text-green-600'"></i>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="actionType === 'ban' ? 'Ban User' : 'Unban User'"></h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Are you sure you want to <span x-text="actionType === 'ban' ? 'suspend access for' : 'restore access for'"></span> <strong x-text="targetName"></strong>?
                    </p>
                    <div class="mt-6 flex justify-center gap-3">
                        <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                        <form method="POST" :action="targetUrl">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-white rounded-lg font-bold"
                                    :class="actionType === 'ban' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'">
                                Confirm
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeModal === 'promote'" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="activeModal = null"></div>
                <div class="bg-white rounded-xl shadow-xl transform transition-all sm:max-w-sm sm:w-full z-[110] overflow-hidden p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                        <i class='bx bxs-shield-alt-2 text-2xl text-yellow-600'></i>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Promote to Admin</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Are you sure you want to grant <strong>Administrator</strong> privileges to <strong x-text="targetName"></strong>?
                    </p>
                    <div class="mt-6 flex justify-center gap-3">
                        <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                        <form method="POST" :action="targetUrl">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-bold">
                                Yes, Promote
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeModal === 'delete'" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="activeModal = null"></div>
                <div class="bg-white rounded-xl shadow-xl transform transition-all sm:max-w-sm sm:w-full z-[110] overflow-hidden p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class='bx bx-trash text-2xl text-red-600'></i>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Account</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Are you absolutely sure you want to delete <strong x-text="targetName"></strong>? This action is <strong>irreversible</strong>.
                    </p>
                    <div class="mt-6 flex justify-center gap-3">
                        <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                        <form method="POST" :action="targetUrl">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold">
                                Delete Forever
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>