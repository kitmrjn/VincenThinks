<x-admin-layout>
    <div class="space-y-6">
        
        {{-- Header & Search --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
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

        {{-- Filter Tabs --}}
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('admin.users') }}" 
                   class="{{ !request('role') ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    All Users
                </a>
                <a href="{{ route('admin.users', ['role' => 'teacher']) }}" 
                   class="{{ request('role') == 'teacher' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Teachers
                </a>
                <a href="{{ route('admin.users', ['role' => 'student']) }}" 
                   class="{{ request('role') == 'student' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Students
                </a>
                <a href="{{ route('admin.users', ['role' => 'admin']) }}" 
                   class="{{ request('role') == 'admin' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Admins
                </a>
                <a href="{{ route('admin.users', ['role' => 'banned']) }}" 
                   class="{{ request('role') == 'banned' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Banned
                </a>
            </nav>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Identity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & ID</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition" x-data="{ editModal: false, passwordModal: false }">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($user->avatar)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->avatar) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-maroon-100 flex items-center justify-center text-maroon-700 font-bold">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full w-fit mb-1
                                        {{ $user->is_admin ? 'bg-red-100 text-red-800' : ($user->member_type == 'teacher' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $user->is_admin ? 'Administrator' : ucfirst($user->member_type) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        @if($user->member_type == 'student')
                                            {{ $user->student_number }} • {{ $user->course->acronym ?? 'N/A' }}
                                        @else
                                            {{ $user->teacher_number }} • {{ $user->department ?? 'N/A' }}
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                <span class="font-bold text-gray-800">{{ $user->questions_count }}</span> Q / 
                                <span class="font-bold text-gray-800">{{ $user->answers_count }}</span> A
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($user->is_banned)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-800 text-white">Banned</span>
                                @elseif($user->email_verified_at)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Verified</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Unverified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2" x-data="{ open: false }">
                                    {{-- Ban/Unban Button --}}
                                    <form method="POST" action="{{ route('admin.users.ban', $user->id) }}">
                                        @csrf
                                        <button type="submit" class="text-{{ $user->is_banned ? 'green' : 'red' }}-600 hover:text-{{ $user->is_banned ? 'green' : 'red' }}-900" title="{{ $user->is_banned ? 'Unban' : 'Ban' }}">
                                            <i class='bx {{ $user->is_banned ? 'bx-lock-open' : 'bx-block' }} text-xl'></i>
                                        </button>
                                    </form>

                                    {{-- More Actions Dropdown --}}
                                    <div class="relative">
                                        <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class='bx bx-dots-vertical-rounded text-xl'></i>
                                        </button>
                                        
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100 
                                             {{ $loop->iteration >= $loop->count - 2 ? 'bottom-full mb-2 origin-bottom-right' : 'mt-2 origin-top-right' }}" 
                                             style="display: none;">

                                            <button @click="editModal = true; open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                                <i class='bx bx-edit-alt mr-2 text-blue-600'></i> Edit Info
                                            </button>

                                            <button @click="passwordModal = true; open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                                <i class='bx bx-key mr-2 text-orange-600'></i> Reset Password
                                            </button>

                                            @if(!$user->email_verified_at)
                                                <form method="POST" action="{{ route('admin.users.verify', $user->id) }}">
                                                    @csrf
                                                    <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"><i class='bx bx-check-circle mr-2 text-green-600'></i> Force Verify</button>
                                                </form>
                                            @endif
                                            
                                            @if(!$user->is_admin)
                                                <form method="POST" action="{{ route('admin.users.promote', $user->id) }}">
                                                    @csrf
                                                    <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"><i class='bx bxs-shield-alt-2 mr-2 text-yellow-600'></i> Make Admin</button>
                                                </form>
                                            @endif
                                            
                                            <div class="border-t border-gray-100 my-1"></div>

                                            <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" onsubmit="return confirm('Are you sure? This deletes ALL user data forever.');">
                                                @csrf @method('DELETE')
                                                <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition"><i class='bx bx-trash mr-2'></i> Delete Account</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL: EDIT USER --}}
                                <template x-if="editModal">
                                    <div class="fixed inset-0 z-[100] overflow-y-auto">
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="editModal = false"></div>
                                            <div class="bg-white rounded-xl shadow-xl transform transition-all sm:max-w-lg sm:w-full z-[110] overflow-hidden">
                                                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-6 text-left">
                                                    @csrf
                                                    <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Profile: {{ $user->name }}</h3>
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                                            <input type="text" name="name" value="{{ $user->name }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Email</label>
                                                            <input type="email" name="email" value="{{ $user->email }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                                                        </div>
                                                        @if($user->member_type === 'student')
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700">Student Number</label>
                                                                <input type="text" name="student_number" value="{{ $user->student_number }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                                                            </div>
                                                        @else
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700">Teacher Number</label>
                                                                <input type="text" name="teacher_number" value="{{ $user->teacher_number }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700">Department</label>
                                                                <input type="text" name="department" value="{{ $user->department }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mt-6 flex justify-end gap-3">
                                                        <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                                                        <button type="submit" class="px-4 py-2 bg-maroon-700 text-white rounded-lg font-bold">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- MODAL: RESET PASSWORD --}}
                                <template x-if="passwordModal">
                                    <div class="fixed inset-0 z-[100] overflow-y-auto">
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="passwordModal = false"></div>
                                            <div class="bg-white rounded-xl shadow-xl transform transition-all sm:max-w-md sm:w-full z-[110] overflow-hidden">
                                                <form method="POST" action="{{ route('admin.users.reset_password', $user->id) }}" class="p-6 text-left">
                                                    @csrf
                                                    <h3 class="text-lg font-bold text-gray-900 mb-2 text-orange-600">Reset Password</h3>
                                                    <p class="text-sm text-gray-500 mb-4">Set a new password for {{ $user->name }}.</p>
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">New Password</label>
                                                            <input type="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                                            <input type="password" name="password_confirmation" required class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700">
                                                        </div>
                                                    </div>
                                                    <div class="mt-6 flex justify-end gap-3">
                                                        <button type="button" @click="passwordModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
                                                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-bold">Update Password</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</x-admin-layout>