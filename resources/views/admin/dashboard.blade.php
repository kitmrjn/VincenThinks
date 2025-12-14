<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - VincenThinks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script>
        tailwind.config = { theme: { extend: { colors: { maroon: { 700: '#800000', 800: '#600000', 900: '#400000' } } } } }
    </script>
</head>
<body class="bg-gray-100 font-sans text-gray-600 antialiased min-h-screen flex">

    <aside class="w-64 bg-maroon-700 text-white flex flex-col fixed h-full shadow-xl z-10">
        <div class="h-16 flex items-center px-6 border-b border-maroon-800">
            <i class='bx bx-grid-alt mr-3 text-2xl'></i>
            <span class="font-bold text-lg tracking-wide">Administrator</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 bg-white/10 text-white rounded-lg transition">
                <i class='bx bx-error-circle mr-3 text-xl'></i><span class="font-medium">Reports</span>
            </a>

            <a href="{{ route('admin.categories') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-lg transition">
                <i class='bx bx-category mr-3 text-xl'></i><span class="font-medium">Categories</span>
            </a>

            {{-- DROPDOWN MENU --}}
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-lg transition focus:outline-none">
                    <div class="flex items-center"><i class='bx bx-cog mr-3 text-xl'></i><span class="font-medium">Settings</span></div>
                    <i class='bx bx-chevron-down transition-transform' :class="open ? 'rotate-180' : ''"></i>
                </button>
                {{-- x-show connects to open state --}}
                <div x-show="open" x-transition class="pl-12 space-y-1 mt-1">
                    <a href="{{ route('admin.settings.general') }}" class="block py-2 text-sm text-gray-400 hover:text-white">General Rules</a>
                    <a href="{{ route('admin.settings.email') }}" class="block py-2 text-sm text-gray-400 hover:text-white">Email Server</a>
                </div>
            </div>
        </nav>

        <div class="p-4 border-t border-maroon-800">
            <a href="/" class="flex items-center px-4 py-2 text-red-200 hover:text-white transition">
                <i class='bx bx-log-out-circle mr-3 text-xl'></i><span>Back to Site</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 ml-64 p-8">
        <header class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-3xl font-light text-gray-800">Reported Content</h1>
                <p class="text-sm text-gray-500 mt-1 font-light">Overview of flagged questions</p>
            </div>
            <div class="flex items-center bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm">
                <i class='bx bx-flag text-red-500 mr-3 text-xl font-thin'></i>
                <div class="text-right leading-tight">
                    <span class="block text-xl font-bold text-gray-800">{{ $reports->count() }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-gray-400">Pending</span>
                </div>
            </div>
        </header>
        
        @if(session('success'))
            <div class="mb-6 bg-white border-l-4 border-green-500 p-4 shadow-sm flex items-center">
                <i class='bx bx-check text-2xl text-green-500 mr-3 font-thin'></i>
                <span class="text-gray-600 text-sm">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Reports Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($reports->count() > 0)
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-500 font-medium">
                            <th class="px-6 py-4 font-normal">Details</th>
                            <th class="px-6 py-4 font-normal w-1/2">Content</th>
                            <th class="px-6 py-4 font-normal text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($reports as $report)
                            <tr class="hover:bg-gray-50 transition duration-150 group">
                                <td class="px-6 py-5 align-top">
                                    <div class="flex items-start">
                                        <i class='bx bx-error-circle text-red-500 mt-0.5 mr-2 text-lg font-thin'></i>
                                        <div>
                                            <span class="block text-gray-800 font-medium text-sm">{{ $report->reason }}</span>
                                            <div class="text-xs text-gray-400 mt-1 font-light flex items-center">
                                                <span>{{ $report->created_at->format('M d') }}</span>
                                                <span class="mx-1">•</span>
                                                <i class='bx bx-user text-xs mr-1'></i>{{ $report->user->name ?? 'Anon' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 align-top">
                                    @if($report->question)
                                        <div class="mb-1">
                                            <a href="{{ route('question.show', $report->question->id) }}" target="_blank" class="text-maroon-700 font-bold hover:underline text-sm flex items-center">
                                                {{ $report->question->title }}<i class='bx bx-link-external ml-2 text-xs opacity-50'></i>
                                            </a>
                                        </div>
                                        <p class="text-sm text-gray-500 font-light leading-relaxed line-clamp-2">{{ Str::limit($report->question->content, 120) }}</p>
                                    @else
                                        <span class="text-gray-300 italic text-sm flex items-center"><i class='bx bx-trash mr-1'></i> Content deleted</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 align-top text-right">
                                    <div class="flex justify-end space-x-3">
                                        <form action="{{ route('admin.dismiss_report', $report->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="flex items-center px-3 py-1.5 text-xs text-gray-500 hover:text-gray-800 border border-gray-300 hover:border-gray-500 rounded transition bg-transparent" title="Dismiss"><i class='bx bx-x mr-1 text-base'></i> Dismiss</button>
                                        </form>
                                        @if($report->question)
                                            <form action="{{ route('admin.delete_question', $report->question->id) }}" method="POST" onsubmit="return confirm('Delete this question?');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="flex items-center px-3 py-1.5 text-xs text-red-600 hover:text-white border border-red-200 hover:bg-red-600 hover:border-red-600 rounded transition bg-transparent" title="Delete"><i class='bx bx-trash mr-1 text-base'></i> Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-24 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4"><i class='bx bx-shield-quarter text-4xl text-gray-300 font-thin'></i></div>
                    <h3 class="text-lg font-medium text-gray-800">All caught up</h3><p class="text-gray-400 font-light text-sm mt-1">No pending reports to review.</p>
                </div>
            @endif
        </div>
    </main>
</body>
</html>