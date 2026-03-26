<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div id="flash-success" class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('warning'))
                <div id="flash-warning" class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-lg shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('warning') }}</p>
                </div>
            @endif

            {{-- Upload Card --}}
            <div class="bg-white/70 backdrop-blur-md border border-white/60 dark:bg-gray-800/70 dark:border-gray-700/50 overflow-hidden shadow-lg shadow-indigo-100/40 dark:shadow-none sm:rounded-2xl animate-fade-in-up" style="animation-delay: 0.1s; opacity: 0;">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Upload Image</h3>
                    <p class="text-sm text-gray-500 mb-4">Accepted formats: JPEG, PNG, WebP — Max size: 5MB</p>

                    <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                        @csrf

                        {{-- Drag & Drop Area --}}
                        <div id="drop-zone"
                             class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50/30 cursor-pointer"
                             onclick="document.getElementById('image-input').click()">

                            <div id="drop-zone-content" class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">
                                        <span class="text-indigo-600 hover:text-indigo-700">Click to browse</span> or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">JPEG, PNG, or WebP up to 5MB</p>
                                </div>
                            </div>

                            {{-- File preview --}}
                            <div id="file-preview" class="hidden flex items-center gap-4">
                                <img id="preview-image" src="" alt="Preview" class="w-16 h-16 object-cover rounded-lg shadow-sm">
                                <div class="text-left">
                                    <p id="file-name" class="text-sm font-medium text-gray-800"></p>
                                    <p id="file-size" class="text-xs text-gray-500"></p>
                                </div>
                                <button type="button" id="clear-file" class="ml-auto text-gray-400 hover:text-red-500 transition-colors" onclick="event.stopPropagation(); clearFile();">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>

                            <input type="file" name="image" id="image-input" class="hidden" accept="image/jpeg,image/png,image/webp">
                        </div>

                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="mt-4 flex justify-end">
                            <button type="submit" id="upload-btn"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                                    disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Upload & Analyze
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Recent Uploads --}}
            @if ($images->count() > 0)
                <div class="bg-white/70 backdrop-blur-md border border-white/60 dark:bg-gray-800/70 dark:border-gray-700/50 overflow-hidden shadow-lg shadow-indigo-100/40 dark:shadow-none sm:rounded-2xl mt-8 animate-fade-in-up" style="animation-delay: 0.2s; opacity: 0;">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Uploads</h3>
                            <a href="{{ route('images.gallery') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                View Gallery →
                            </a>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach ($images as $image)
                                <a href="{{ route('images.show', $image) }}" class="group relative block aspect-square overflow-hidden rounded-lg bg-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <img src="{{ $image->url }}" alt="{{ $image->original_filename }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                                    {{-- Duplicate badge --}}
                                    @if ($image->is_duplicate)
                                        <span class="absolute top-2 right-2 bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow">
                                            Duplicate
                                        </span>
                                    @endif

                                    {{-- Overlay on hover --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <div class="absolute bottom-0 left-0 right-0 p-3">
                                            <p class="text-white text-xs font-medium truncate">{{ $image->original_filename }}</p>
                                            <p class="text-white/70 text-xs">{{ $image->formatted_file_size }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white/70 backdrop-blur-md border border-white/60 dark:bg-gray-800/70 dark:border-gray-700/50 overflow-hidden shadow-lg shadow-indigo-100/40 dark:shadow-none sm:rounded-2xl mt-8 animate-fade-in-up" style="animation-delay: 0.2s; opacity: 0;">
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-float">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-gray-600 font-medium">No images uploaded yet</h3>
                        <p class="text-sm text-gray-400 mt-1">Upload your first image to get started</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- JavaScript for drag-and-drop & file preview --}}
    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('image-input');
        const dropContent = document.getElementById('drop-zone-content');
        const filePreview = document.getElementById('file-preview');
        const previewImage = document.getElementById('preview-image');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const uploadBtn = document.getElementById('upload-btn');

        // Drag & drop events
        ['dragenter', 'dragover'].forEach(event => {
            dropZone.addEventListener(event, (e) => {
                e.preventDefault();
                dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
            });
        });

        ['dragleave', 'drop'].forEach(event => {
            dropZone.addEventListener(event, (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
            });
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showPreview(files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                showPreview(fileInput.files[0]);
            }
        });

        function showPreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                fileName.textContent = file.name;
                fileSize.textContent = formatBytes(file.size);
                dropContent.classList.add('hidden');
                filePreview.classList.remove('hidden');
                filePreview.classList.add('flex');
                uploadBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }

        function clearFile() {
            fileInput.value = '';
            dropContent.classList.remove('hidden');
            filePreview.classList.add('hidden');
            filePreview.classList.remove('flex');
            uploadBtn.disabled = true;
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Auto-hide flash messages
        setTimeout(() => {
            ['flash-success', 'flash-warning'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.transition = 'opacity 0.5s', el.style.opacity = '0', setTimeout(() => el.remove(), 500);
            });
        }, 6000);
    </script>
</x-app-layout>
