<x-public-layout>
    @push('styles')
        <style>
            /* Trix Editor Styling to match Create Form */
            .trix-content { 
                min-height: 200px; 
                overflow-y: auto;
            }
            /* Ensure the toolbar icons match your theme */
            trix-toolbar .trix-button.trix-active { color: #800000; }
        </style>
    @endpush

    @push('scripts')
    @endpush

    <div class="max-w-3xl mx-auto mt-8 px-4 mb-12">
        
        {{-- Card Container --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-200 relative overflow-hidden group">
            {{-- Maroon Left Border --}}
            <div class="absolute top-0 left-0 w-1 h-full bg-maroon-700"></div>
            
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-light text-gray-800 flex items-center">
                    <i class='bx bx-edit text-2xl text-maroon-700 mr-2 font-thin'></i>
                    Edit Question
                </h3>
                <a href="{{ route('question.show', $question->id) }}" class="text-sm text-gray-400 hover:text-maroon-700 underline transition">Cancel</a>
            </div>
            
            <form action="{{ route('question.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Category Select --}}
                <select name="category_id" required class="w-full border-b border-gray-200 bg-transparent p-2 mb-4 focus:border-maroon-700 focus:outline-none text-sm font-light text-gray-600 cursor-pointer">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $question->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                {{-- Title Input --}}
                <input type="text" name="title" value="{{ $question->title }}" placeholder="What's your question?" required class="w-full border-b border-gray-200 bg-transparent p-2 mb-4 focus:border-maroon-700 focus:outline-none text-lg font-normal transition-colors placeholder-gray-400 text-gray-800">
                
                {{-- Trix Editor Component --}}
                <div class="mb-6">
                    {{-- :value loads the data, the component handles the toolbar --}}
                    <x-trix-editor name="content" :value="$question->content" placeholder="Type your question here..." />
                    @error('content')
                        <p class="text-red-500 text-xs mt-1 font-bold flex items-center"><i class='bx bx-error-circle mr-1'></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Manage Existing Images --}}
                @if($question->images->count() > 0)
                    <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2 flex items-center"><i class='bx bx-images mr-1'></i> Existing Images (Check to Delete)</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($question->images as $img)
                                <label class="relative group rounded-md overflow-hidden border border-gray-200 cursor-pointer w-20 h-20">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover object-center">
                                    <div class="absolute inset-0 bg-red-900/80 opacity-0 group-hover:opacity-100 transition flex items-center justify-center flex-col text-white">
                                        <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="form-checkbox text-red-600 rounded mb-1 accent-red-500">
                                        <span class="text-[10px] font-bold uppercase">Delete</span>
                                    </div>
                                    {{-- Visual indicator if checked (requires simple JS or peer CSS, relying on hover for now) --}}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Add New Images --}}
                <div class="mb-6 flex items-center justify-between">
                    <label class="cursor-pointer flex items-center text-xs text-gray-500 hover:text-maroon-700 transition">
                        <i class='bx bx-images text-lg mr-1'></i> Add New Images (Optional)
                        <input type="file" name="images[]" multiple class="hidden" 
                            onchange="document.getElementById('img-preview-count').innerText = this.files.length + ' file(s) selected'">
                    </label>
                    <span id="img-preview-count" class="ml-3 text-xs text-maroon-700 font-bold"></span>
                </div>

                {{-- Submit Button --}}
                <div class="text-right">
                    <button type="submit" class="bg-maroon-700 text-white px-6 py-2 rounded-lg font-normal hover:bg-maroon-800 transition shadow-sm flex items-center ml-auto tracking-wide">
                        <i class='bx bx-send mr-2'></i> Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>