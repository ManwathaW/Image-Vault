<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Image Details
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('warning'))
                <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-lg shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('warning') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Image Preview --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="relative rounded-lg overflow-hidden bg-gray-50">
                            <img src="{{ $image->url }}" alt="{{ $image->original_filename }}"
                                 class="w-full h-auto max-h-[600px] object-contain mx-auto">

                            @if ($image->is_duplicate)
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center gap-1.5 bg-amber-500 text-white text-sm font-bold px-3 py-1.5 rounded-full shadow-lg">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Duplicate
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Delete Button with Alpine Modal --}}
                        <div class="mt-3 flex justify-end" x-data>
                            <button x-on:click.prevent="$dispatch('open-modal', 'confirm-image-deletion')" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Image
                            </button>

                            <x-modal name="confirm-image-deletion" focusable>
                                <form method="POST" action="{{ route('images.destroy', $image) }}" class="p-6">
                                    @csrf
                                    @method('DELETE')

                                    <h2 class="text-lg font-medium text-gray-900">
                                        Are you sure you want to delete this image?
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-600">
                                        This action cannot be undone and will permanently remove the file from our servers.
                                    </p>

                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                            Cancel
                                        </button>

                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Delete Image
                                        </button>
                                    </div>
                                </form>
                            </x-modal>
                        </div>
                    </div>
                </div>

                {{-- Metadata Panel --}}
                <div class="space-y-6">

                    {{-- File Information --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                File Information
                            </h3>

                            <dl class="divide-y divide-gray-100">
                                <div class="py-3 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Filename</dt>
                                    <dd class="text-sm text-gray-900 col-span-2 break-all">{{ $image->original_filename }}</dd>
                                </div>
                                <div class="py-3 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">File Size</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $image->formatted_file_size }}</dd>
                                </div>
                                @if (isset($image->metadata['dimensions']))
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">Dimensions</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">{{ $image->metadata['dimensions'] }}</dd>
                                    </div>
                                @endif
                                @if (isset($image->metadata['mime_type']))
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">MIME Type</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">{{ $image->metadata['mime_type'] }}</dd>
                                    </div>
                                @endif
                                <div class="py-3 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $image->created_at->format('M d, Y \a\t h:i A') }}</dd>
                                </div>
                                <div class="py-3 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">SHA-256 Hash</dt>
                                    <dd class="text-sm text-gray-900 col-span-2 font-mono text-xs break-all">{{ $image->file_hash }}</dd>
                                </div>
                                <div class="py-3 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm col-span-2">
                                        @if ($image->is_duplicate)
                                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z" clip-rule="evenodd"/>
                                                </svg>
                                                Duplicate
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Unique
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- EXIF / Camera Data --}}
                    @php
                        $exifFields = [
                            'camera_make' => 'Camera Make',
                            'camera_model' => 'Camera Model',
                            'software' => 'Software',
                            'aperture' => 'Aperture',
                            'exposure_time' => 'Exposure Time',
                            'iso' => 'ISO',
                            'focal_length' => 'Focal Length',
                            'flash' => 'Flash',
                            'orientation' => 'Orientation',
                            'date_taken' => 'Date Taken',
                        ];
                        $hasExif = false;
                        foreach ($exifFields as $key => $label) {
                            if (isset($image->metadata[$key])) {
                                $hasExif = true;
                                break;
                            }
                        }
                    @endphp

                    @if ($hasExif)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Camera & EXIF Data
                                </h3>

                                <dl class="divide-y divide-gray-100">
                                    @foreach ($exifFields as $key => $label)
                                        @if (isset($image->metadata[$key]))
                                            <div class="py-3 grid grid-cols-3 gap-4">
                                                <dt class="text-sm font-medium text-gray-500">{{ $label }}</dt>
                                                <dd class="text-sm text-gray-900 col-span-2">{{ $image->metadata[$key] }}</dd>
                                            </div>
                                        @endif
                                    @endforeach
                                </dl>
                            </div>
                        </div>
                    @else
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Camera & EXIF Data
                                </h3>
                                <p class="text-sm text-gray-500">No EXIF data available for this image. EXIF metadata is typically found in JPEG images taken with digital cameras.</p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
