<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Scapteinc\LaraVeil\Models\Media;

new class extends Component {
    use WithFileUploads;

    public $media = [];
    public $searchQuery = '';
    public $uploadedFile;
    public $mediaType = 'image';
    public $uploadedCount = 0;

    public function mount()
    {
        $this->loadMedia();
    }

    public function loadMedia()
    {
        $query = Media::query();

        if ($this->searchQuery) {
            $query->where('name', 'like', '%' . $this->searchQuery . '%');
        }

        if ($this->mediaType !== 'all') {
            $query->where('media_type', $this->mediaType);
        }

        $this->media = $query->latest()->get();
    }

    public function updatedSearchQuery()
    {
        $this->loadMedia();
    }

    public function updatedMediaType()
    {
        $this->loadMedia();
    }

    public function upload()
    {
        $this->validate([
            'uploadedFile' => 'required|file|max:5120',
        ]);

        try {
            $mediaForge = app('media.forge');

            // Save uploaded file
            $path = $this->uploadedFile->store('media', 'public');
            $fileName = $this->uploadedFile->getClientOriginalName();
            $mediaType = str_starts_with($this->uploadedFile->getMimeType(), 'image') ? 'image' : 'file';

            // Create media record
            Media::create([
                'name' => $fileName,
                'path' => $path,
                'media_type' => $mediaType,
                'mime_type' => $this->uploadedFile->getMimeType(),
                'file_size' => $this->uploadedFile->getSize(),
            ]);

            $this->uploadedFile = null;
            $this->uploadedCount++;
            $this->dispatch('notify', message: 'File uploaded successfully');
            $this->loadMedia();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Upload failed: ' . $e->getMessage(), type: 'error');
        }
    }

    public function deleteMedia(Media $media)
    {
        try {
            $media->delete();
            $this->dispatch('notify', message: 'Media deleted successfully');
            $this->loadMedia();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Delete failed: ' . $e->getMessage(), type: 'error');
        }
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-6">Media Library</h1>

        <!-- Upload Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Upload New Media</h2>

            <form wire:submit.prevent="upload" class="space-y-4">
                <div>
                    <input type="file" wire:model="uploadedFile" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" />
                    @error('uploadedFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    @if($uploadedFile)
                        Uploading...
                    @else
                        Upload File
                    @endif
                </button>
            </form>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Media</label>
                    <input type="text" wire:model="searchQuery" placeholder="Search media files..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Media Type</label>
                    <select wire:model="mediaType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="all">All Types</option>
                        <option value="image">Images</option>
                        <option value="video">Videos</option>
                        <option value="file">Documents</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Grid -->
    @if($media->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($media as $item)
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
                    <div class="bg-gray-100 h-48 flex items-center justify-center">
                        @if($item->media_type === 'image')
                            <img src="{{ asset('storage/' . $item->path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover" />
                        @else
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="text-sm font-semibold truncate mb-2">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-500 mb-4">{{ number_format($item->file_size / 1024, 2) }} KB</p>

                        <div class="space-y-2">
                            <a href="{{ asset('storage/' . $item->path) }}" target="_blank" class="block px-3 py-1 bg-blue-500 text-white text-xs rounded text-center hover:bg-blue-600">
                                View
                            </a>
                            <button wire:click="deleteMedia({{ $item->id }})" wire:confirm="Are you sure?" class="w-full px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-gray-500 text-lg">No media files found.</p>
        </div>
    @endif
</div>
