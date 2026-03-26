<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gallery') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload New
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Tabs --}}
            <div class="bg-white/70 backdrop-blur-md border border-white/60 dark:bg-gray-800/70 dark:border-gray-700/50 overflow-hidden shadow-lg shadow-indigo-100/40 dark:shadow-none sm:rounded-2xl animate-fade-in-up" style="animation-delay: 0.1s; opacity: 0;">
                <div class="px-6 py-3 flex items-center gap-1 border-b border-white/40 dark:border-gray-700/50">
                    <span class="text-sm text-gray-500 mr-3">Filter:</span>
                    <a href="{{ route('images.gallery') }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ !request('filter') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        All
                    </a>
                    <a href="{{ route('images.gallery', ['filter' => 'unique']) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('filter') === 'unique' ? 'bg-emerald-100 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        Unique
                    </a>
                    <a href="{{ route('images.gallery', ['filter' => 'duplicates']) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('filter') === 'duplicates' ? 'bg-amber-100 text-amber-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        Duplicates
                    </a>

                    <span class="ml-auto text-xs text-gray-400">
                        {{ $images->total() }} {{ Str::plural('image', $images->total()) }}
                    </span>
                </div>
            </div>

            {{-- Image Grid --}}
            @if ($images->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 animate-fade-in-up" style="animation-delay: 0.2s; opacity: 0;">
                    @foreach ($images as $image)
                        <a href="{{ route('images.show', $image) }}"
                           class="group relative aspect-square overflow-hidden rounded-xl bg-gray-100 shadow-sm hover:shadow-lg transition-all duration-300">
                            <img src="{{ $image->url }}" alt="{{ $image->original_filename }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">

                            {{-- Duplicate badge --}}
                            @if ($image->is_duplicate)
                                <span class="absolute top-2 right-2 bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">
                                    Duplicate
                                </span>
                            @endif

                            {{-- Hover overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="absolute bottom-0 left-0 right-0 p-3">
                                    <p class="text-white text-sm font-medium truncate">{{ $image->original_filename }}</p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-white/70 text-xs">{{ $image->formatted_file_size }}</span>
                                        @if (isset($image->metadata['dimensions']))
                                            <span class="text-white/70 text-xs">{{ $image->metadata['dimensions'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $images->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white/70 backdrop-blur-md border border-white/60 dark:bg-gray-800/70 dark:border-gray-700/50 overflow-hidden shadow-lg shadow-indigo-100/40 dark:shadow-none sm:rounded-2xl mt-8 animate-fade-in-up" style="animation-delay: 0.2s; opacity: 0;">
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-float">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @if (request('filter'))
                            <h3 class="text-gray-600 font-medium">No {{ request('filter') === 'duplicates' ? 'duplicate' : 'unique' }} images found</h3>
                            <p class="text-sm text-gray-400 mt-1">
                                <a href="{{ route('images.gallery') }}" class="text-indigo-600 hover:text-indigo-700">View all images</a>
                            </p>
                        @else
                            <h3 class="text-gray-600 font-medium">No images uploaded yet</h3>
                            <p class="text-sm text-gray-400 mt-1">
                                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-700">Upload your first image</a>
                            </p>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
