@props(['name', 'placeholder' => 'Write something...', 'value' => '', 'maxImages' => 0])

<div 
    x-data="{ 
        content: '', 
        showHelp: false,
        maxImages: {{ $maxImages }},
        quill: null,

        init() {
            this.content = this.$refs.input.value;

            // DYNAMIC TOOLBAR: Only add 'image' if maxImages > 0
            let toolbarOptions = [
                [{ 'header': [1, 2, 3, false] }], 
                ['bold', 'italic', 'underline', 'strike'], 
                ['link', 'blockquote', 'code-block'], 
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean'] 
            ];

            // Add Image button specifically if allowed
            if (this.maxImages > 0) {
                // Insert 'image' after 'code-block' (index 2)
                toolbarOptions[2].push('image');
            }

            // [FIX] Assign to component state 'this.quill' instead of a local variable
            this.quill = new Quill(this.$refs.editor, {
                theme: 'snow',
                placeholder: '{{ $placeholder }}',
                modules: {
                    toolbar: {
                        container: toolbarOptions,
                        handlers: {
                            image: () => { this.handleImageClick(); }
                        }
                    }
                }
            });

            if (this.content) { 
                this.quill.root.innerHTML = this.content; 
            }

            this.quill.on('text-change', () => {
                if (this.quill.getText().trim().length === 0) {
                    this.$refs.input.value = ''; 
                } else {
                    this.$refs.input.value = this.quill.root.innerHTML;
                }
            });

            // Tooltips
            setTimeout(() => {
                const tooltipMap = {
                    'ql-bold': 'Bold', 'ql-italic': 'Italic', 'ql-underline': 'Underline',
                    'ql-strike': 'Strikethrough', 'ql-link': 'Insert Link',
                    'ql-blockquote': 'Quote', 'ql-code-block': 'Code Block',
                    'ql-image': 'Upload Image', 'ql-list': 'List', 
                    'ql-clean': 'Clear Formatting', 'ql-header': 'Text Size'
                };
                
                // [FIX] Use this.quill to find the toolbar for THIS specific editor
                const toolbar = this.quill.getModule('toolbar').container;
                Object.keys(tooltipMap).forEach(className => {
                    const buttons = toolbar.querySelectorAll('.' + className);
                    buttons.forEach(btn => btn.setAttribute('title', tooltipMap[className]));
                });
            }, 500);

            // --- IMAGE LOGIC ---
            this.handleImageClick = function() {
                // [FIX] Use this.quill directly. No more confusion between editors.
                const imgCount = this.quill.root.querySelectorAll('img').length;
                
                if (imgCount >= this.maxImages) {
                    alert('Limit reached: You can only upload ' + this.maxImages + ' image(s) here.');
                    return;
                }

                this.selectLocalImage(this.quill);
            };

            this.selectLocalImage = function(editor) {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = () => {
                    const file = input.files[0];
                    if (/^image\//.test(file.type)) {
                        this.saveToServer(file, editor);
                    } else {
                        alert('You can only upload images.');
                    }
                };
            };

            this.saveToServer = function(file, editor) {
                const fd = new FormData();
                fd.append('image', file);

                const tokenTag = document.querySelector('meta[name=\'csrf-token\']');
                if (!tokenTag) {
                    alert('Error: CSRF Token missing. Please refresh.');
                    return;
                }
                const token = tokenTag.getAttribute('content');

                fetch('{{ route('editor.image.upload') }}', {
                    method: 'POST',
                    body: fd,
                    headers: { 
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json' 
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => { 
                            throw new Error(data.message || 'Upload failed');
                        }).catch(e => {
                            throw new Error('Server Error ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.url) {
                        const range = editor.getSelection(true);
                        editor.insertEmbed(range.index, 'image', result.url);
                        editor.setSelection(range.index + 1);
                    }
                })
                .catch(error => {
                    console.error('Upload Error:', error);
                    alert('Upload Failed: ' + error.message); 
                });
            };
        }
    }"
    class="w-full bg-white rounded-lg border {{ $errors->has($name) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300' }} overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 relative"
    wire:ignore
>
    @once
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <style>
            .ql-toolbar.ql-snow { border: none; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb; padding: 8px; padding-right: 40px; }
            .ql-container.ql-snow { border: none; font-family: inherit; font-size: 0.95rem; color: #374151; }
            .ql-editor { padding: 1rem; min-height: 150px; }
            .ql-editor img { 
                max-width: 100%; height: auto; border-radius: 8px; 
                margin: 10px 0; display: inline-block;
            }
        </style>
    @endonce

    <button type="button" @click="showHelp = !showHelp" class="absolute top-2 right-2 text-gray-400 hover:text-maroon-700 p-1 rounded-full hover:bg-gray-200 transition z-10" title="Toolbar Guide">
        <i class='bx bx-help-circle text-xl'></i>
    </button>

    <div x-show="showHelp" x-cloak style="display: none;" 
         class="absolute inset-0 z-50 bg-white/95 backdrop-blur-sm p-4 flex flex-col items-center justify-center text-center transition-opacity" 
         x-transition.opacity>
        <h4 class="text-maroon-700 font-bold mb-4 text-sm uppercase tracking-widest">Toolbar Guide</h4>
        <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-left text-sm text-gray-600">
            <div class="flex items-center"><i class='bx bx-bold text-lg mr-2 text-gray-400'></i> Bold (Ctrl+B)</div>
            <div class="flex items-center"><i class='bx bx-italic text-lg mr-2 text-gray-400'></i> Italic (Ctrl+I)</div>
            <div class="flex items-center"><i class='bx bx-code-alt text-lg mr-2 text-gray-400'></i> Code Block</div>
            
            {{-- Show Image Help only if enabled --}}
            @if($maxImages > 0)
                <div class="flex items-center"><i class='bx bx-image text-lg mr-2 text-gray-400'></i> Upload Image (Max {{ $maxImages }})</div>
            @endif

            <div class="flex items-center"><i class='bx bx-list-ol text-lg mr-2 text-gray-400'></i> Numbered List</div>
            <div class="flex items-center"><i class='bx bx-list-ul text-lg mr-2 text-gray-400'></i> Bullet List</div>
        </div>
        <button type="button" @click="showHelp = false" class="mt-6 px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full text-xs font-bold uppercase tracking-wide transition">Got it</button>
    </div>

    <input x-ref="input" type="hidden" name="{{ $name }}" value="{{ old($name, $value) }}">
    <div x-ref="editor" class="prose prose-sm max-w-none"></div>
</div>