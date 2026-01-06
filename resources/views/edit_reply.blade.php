<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reply - VincenThinks</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <script>
        tailwind.config = { theme: { extend: { colors: { maroon: { 700: '#800000', 800: '#600000', 900: '#400000' } } } } }
    </script>
    <style>
        .trix-content { min-height: 150px; overflow-y: auto; }
        trix-toolbar .trix-button.trix-active { color: #800000; }
    </style>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

    @include('partials.navbar')

    <div class="max-w-2xl mx-auto mt-12 px-4 mb-12">
        
        {{-- Main Card (Consistent Design) --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-maroon-700"></div>

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-light text-gray-800 flex items-center">
                    <i class='bx bx-edit text-xl text-maroon-700 mr-2 font-thin'></i>
                    Edit Reply
                </h3>
                <a href="{{ route('question.show', $reply->answer->question_id ?? $reply->question_id) }}" class="text-xs text-gray-400 hover:text-maroon-700 underline font-medium transition">Cancel</a>
            </div>

            <form action="{{ route('reply.update', $reply->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-2">
                    <x-trix-editor name="content" :value="$reply->content" placeholder="Update your reply..." max-images="1" />
                    
                    @error('content') 
                        <p class="text-red-500 text-xs mt-1 font-bold flex items-center"><i class='bx bx-error-circle mr-1'></i> {{ $message }}</p> 
                    @enderror
                </div>

                {{-- Help Text --}}
                <div class="mb-6 flex items-center justify-between text-xs text-gray-400 font-light">
                    <span><i class='bx bx-info-circle mr-1'></i> To delete an image, click it and press Delete.</span>
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-maroon-700 text-white px-6 py-2 rounded-lg font-normal hover:bg-maroon-800 transition shadow-sm flex items-center ml-auto tracking-wide text-sm">
                        <i class='bx bx-save mr-2'></i> Update Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- IMAGE UPLOAD SCRIPT --}}
    <script>
        addEventListener("trix-attachment-add", function(event) { if (event.attachment.file) { uploadFileAttachment(event.attachment) } })
        function uploadFileAttachment(attachment) { uploadFile(attachment.file, function(progress) { attachment.setUploadProgress(progress) }, function(attributes) { attachment.setAttributes(attributes) }) }
        function uploadFile(file, progressCallback, successCallback) {
            var formData = new FormData(); formData.append("image", file);
            var xhr = new XMLHttpRequest(); xhr.open("POST", "{{ route('editor.image.upload') }}", true);
            xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
            xhr.upload.addEventListener("progress", function(event) { progressCallback((event.loaded / event.total) * 100) });
            xhr.onload = function() { if (xhr.status === 200) { var response = JSON.parse(xhr.responseText); successCallback({ url: response.url, href: response.url }) } };
            xhr.send(formData);
        }
    </script>
</body>
</html>