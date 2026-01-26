@props(['categories'])

<div
    x-show="createModalOpen"
    x-cloak
    class="fixed inset-0 z-[100] overflow-hidden" 
    role="dialog"
    aria-modal="true"
    x-data="{
        step: 1,
        selectedCategory: null,
        title: '',
        images: [],
        previews: [],
        isLoading: false, 
        
        resetForm() {
            this.title = '';
            this.selectedCategory = null;
            this.images = [];
            this.previews = [];
            this.isLoading = false;
            // Safely reset file input if it exists
            const fileInput = document.getElementById('file-upload');
            if(fileInput) fileInput.value = ''; 
        },
        
        handleFileSelect(e) {
            const files = Array.from(e.target.files);
            this.images = files;
            this.previews = [];
            
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => { this.previews.push(e.target.result); };
                reader.readAsDataURL(file);
            });
        },

        removeImage(index) {
            this.previews.splice(index, 1);
            if(this.previews.length === 0) {
                this.images = [];
                const fileInput = document.getElementById('file-upload');
                if(fileInput) fileInput.value = '';
            }
        },

        closeModal() {
            this.createModalOpen = false;
            setTimeout(() => this.resetForm(), 300);
        },

        async submitForm() {
            if (this.isLoading) return;
            this.isLoading = true;

            const form = this.$refs.form;
            const formData = new FormData(form);

            try {
                const response = await fetch('{{ route('question.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    this.closeModal();
                    
                    const flashBox = document.getElementById('js-flash-message');
                    const flashText = document.getElementById('js-flash-text');
                    const flashIcon = flashBox ? flashBox.querySelector('i') : null;
                    
                    if (flashBox && flashText) {
                        flashText.innerText = data.message || 'Post submitted successfully!';
                        flashBox.style.display = 'flex';
                        window.scrollTo({ top: 0, behavior: 'smooth' });

                        // Reset base classes
                        flashBox.className = 'p-4 rounded-lg border mb-6 shadow-sm flex items-center'; 
                        
                        if (data.status === 'pending_review') {
                            flashBox.classList.add('bg-yellow-50', 'text-yellow-800', 'border-yellow-200');
                            if(flashIcon) flashIcon.className = 'bx bx-info-circle text-xl mr-2';
                            setTimeout(() => { window.location.reload(); }, 4000);
                        } else {
                            flashBox.classList.add('bg-green-50', 'text-green-700', 'border-green-200');
                            if(flashIcon) flashIcon.className = 'bx bx-check-circle text-xl mr-2';
                            setTimeout(() => { window.location.reload(); }, 500);
                        }
                    } else {
                         window.location.reload();
                    }

                } else {
                    alert('Error: ' + (data.message || 'Something went wrong.'));
                    this.isLoading = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
                this.isLoading = false;
            }
        }
    }"
>
    {{-- Backdrop --}}
    <div
        x-show="createModalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
        @click="closeModal"
    ></div>

    {{-- Modal Panel Container --}}
    <div
        x-show="createModalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="fixed inset-0 z-[110] flex items-end sm:items-center justify-center sm:p-4 pointer-events-none"
    >
        {{-- Actual Modal Box --}}
        {{-- MOBILE FIX: Use h-[85vh] or h-full to prevent keyboard issues. Added 'flex flex-col' for sticky footer. --}}
        <div class="pointer-events-auto w-full h-[90vh] sm:h-auto sm:max-h-[90vh] bg-white sm:rounded-xl rounded-t-2xl shadow-2xl flex flex-col max-w-2xl relative">
            
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Ask a Question</h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition rounded-full p-1 hover:bg-gray-100">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            {{-- Scrollable Form Area --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
                <form x-ref="form" enctype="multipart/form-data" @submit.prevent="submitForm">
                    
                    {{-- 1. Category Pills --}}
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Select Category</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($categories as $cat)
                                <label 
                                    class="cursor-pointer px-4 py-2 rounded-full text-sm font-medium border transition select-none"
                                    :class="selectedCategory == {{ $cat->id }} ? 'bg-maroon-700 text-white border-maroon-700 shadow-sm' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-white hover:border-maroon-300'"
                                >
                                    <input type="radio" name="category_id" value="{{ $cat->id }}" class="hidden" x-model="selectedCategory">
                                    {{ $cat->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. Title Input --}}
                    <div class="mb-6">
                         <input 
                            type="text" 
                            name="title" 
                            x-model="title"
                            placeholder="Title of your question" 
                            required 
                            class="w-full text-xl font-bold text-gray-900 placeholder-gray-300 border-0 border-b-2 border-gray-100 focus:border-maroon-700 focus:ring-0 px-0 py-2 transition bg-transparent"
                        >
                    </div>

                    {{-- 3. Trix Editor (Wrapper for Mobile CSS) --}}
                    <div class="mb-6 trix-wrapper">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Details</label>
                        <x-trix-editor name="content" placeholder="Add details, or images to support your question..." />
                    </div>

                    {{-- 4. Image Upload Area --}}
                    <div class="mb-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Attachments</label>
                        
                        <div class="relative group">
                            <input 
                                type="file" 
                                name="images[]" 
                                id="file-upload"
                                multiple 
                                accept="image/*"
                                @change="handleFileSelect"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            >
                            <div class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center transition group-hover:border-maroon-400 group-hover:bg-gray-50">
                                <i class='bx bx-images text-3xl text-gray-300 mb-2 group-hover:text-maroon-600'></i>
                                <p class="text-sm text-gray-500 font-light"><span class="text-maroon-700 font-medium">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 5MB</p>
                            </div>
                        </div>

                        {{-- Previews --}}
                        <div class="mt-4 grid grid-cols-4 gap-4" x-show="previews.length > 0">
                            <template x-for="(src, index) in previews" :key="index">
                                <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200 group">
                                    <img :src="src" class="w-full h-full object-cover">
                                    <button type="button" @click="removeImage(index)" class="absolute top-1 right-1 bg-black/50 text-white rounded-full p-1 hover:bg-red-500 transition">
                                        <i class='bx bx-x'></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer / Actions (Sticky) --}}
            {{-- MODIFIED: Added 'pt-4 pb-8 sm:py-4' for better bottom spacing on mobile --}}
            <div class="border-t border-gray-100 px-6 pt-4 pb-8 sm:py-4 flex items-center justify-between bg-gray-50 sm:rounded-b-xl rounded-none flex-shrink-0">
                <span class="text-xs text-gray-400 hidden sm:inline" x-text="selectedCategory ? 'Posting to selected topic' : 'Select a topic...'"></span>
                <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                    <button @click="closeModal" class="px-4 py-2 text-gray-500 text-sm font-medium hover:text-gray-700 transition">Cancel</button>
                    <button 
                        @click="submitForm" 
                        :disabled="!title || !selectedCategory || isLoading"
                        class="px-6 py-2 rounded-lg text-white font-medium text-sm shadow-md transition flex items-center justify-center w-full sm:w-auto"
                        :class="(!title || !selectedCategory || isLoading) ? 'bg-gray-300 cursor-not-allowed' : 'bg-maroon-700 hover:bg-maroon-800 hover:scale-105'"
                    >
                        <span x-text="isLoading ? 'Posting...' : 'Post Question'"></span>
                        <i class='bx' :class="isLoading ? 'bx-loader-alt animate-spin ml-2' : 'bx-send ml-2'"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>