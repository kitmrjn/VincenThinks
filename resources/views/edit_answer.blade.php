<x-public-layout>
    @push('styles')
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
        <style>
            .trix-content { min-height: 200px; overflow-y: auto; }
            trix-toolbar .trix-button.trix-active { color: #800000; }
        </style>
    @endpush

    @push('scripts')
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
        {{-- IMAGE UPLOAD SCRIPT --}}
        <script>
            addEventListener("trix-attachment-add", function(event) { if (event.attachment.file) { uploadFileAttachment(event.attachment) } })
            function uploadFileAttachment(attachment) { uploadFile(attachment.file, function(progress) { attachment.setUploadProgress(progress) }, function(attributes) { attachment.setAttributes(attributes) }) }
            function uploadFile(file, progressCallback, successCallback) {
                var formData = new FormData(); formData.append("image", file);
                var xhr = new XMLHttpRequest(); xhr.open("POST", "{{ route('editor.image.upload') }}", true);
                // This reads the new meta tag we just added
                xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').content);
                xhr.upload.addEventListener("progress", function(event) { progressCallback((event.loaded / event.total) * 100) });
                xhr.onload = function() { if (xhr.status === 200) { var response = JSON.parse(xhr.responseText); successCallback({ url: response.url, href: response.url }) } };
                xhr.send(formData);
            }
        </script>
    @endpush

    <div class="max-w-3xl mx-auto mt-8 px-4 mb-12">
        
        {{-- Context: Question Title --}}
        <div class="mb-4">
            <a href="{{ route('question.show', $answer->question_id) }}" class="text-sm text-gray-500 hover:text-maroon-700 flex items-center mb-2 transition">
                <i class='bx bx-arrow-back mr-1'></i> Back to Question
            </a>
            <h2 class="text-lg text-gray-700 font-light italic border-l-4 border-maroon-700 pl-4 bg-gray-50 py-2 rounded-r-lg">
                re: "{{ $answer->question->title }}"
            </h2>
        </div>

        {{-- Main Card --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-200 relative overflow-hidden group">
            {{-- Maroon Left Border --}}
            <div class="absolute top-0 left-0 w-1 h-full bg-maroon-700"></div>

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-light text-gray-800 flex items-center">
                    <i class='bx bx-edit text-2xl text-maroon-700 mr-2 font-thin'></i>
                    Edit Answer
                </h3>
                <a href="{{ route('question.show', $answer->question_id) }}" class="text-sm text-gray-400 hover:text-maroon-700 underline transition">Cancel</a>
            </div>
            
            <form action="{{ route('answer.update', $answer->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-2">
                    <x-trix-editor name="content" :value="$answer->content" placeholder="Update your answer..." max-images="1" />
                    
                    @error('content') 
                        <p class="text-red-500 text-xs mt-1 font-bold flex items-center"><i class='bx bx-error-circle mr-1'></i> {{ $message }}</p> 
                    @enderror
                </div>

                {{-- Help Text --}}
                <div class="mb-6 flex items-center justify-between text-xs text-gray-400 font-light">
                    <span><i class='bx bx-info-circle mr-1'></i> To delete an image, click it and press Delete.</span>
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-maroon-700 text-white px-6 py-2 rounded-lg font-normal hover:bg-maroon-800 transition shadow-sm flex items-center ml-auto tracking-wide">
                        <i class='bx bx-save mr-2'></i> Update Answer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>