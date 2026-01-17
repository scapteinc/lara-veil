<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Scapteinc\LaraVeil\Models\Media;
use Scapteinc\LaraVeil\Services\Vrm\MediaForgeService;

new class extends Component {
    use WithPagination;

    public function duplicate($id)
    {
        $media = Media::findOrFail($id);
        $oldPath = storage_path('app/public/' . $media->path);

        if (!file_exists($oldPath)) {
            session()->flash('error', 'Original file not found.');
            return;
        }

        $pathInfo = pathinfo($oldPath);
        $newFilename = $pathInfo['filename'] . '-copy.' . ($pathInfo['extension'] ?? '');
        $newDir = dirname($media->path);
        $newRelativePath = ($newDir === '.' ? '' : $newDir . '/') . $newFilename;
        $newFullPath = storage_path('app/public/' . $newRelativePath);

        // Ensure we don't overwrite if copy already exists
        $counter = 1;
        while (file_exists($newFullPath)) {
            $newFilename = $pathInfo['filename'] . '-copy-' . $counter . '.' . ($pathInfo['extension'] ?? '');
            $newRelativePath = ($newDir === '.' ? '' : $newDir . '/') . $newFilename;
            $newFullPath = storage_path('app/public/' . $newRelativePath);
            $counter++;
        }

        if (copy($oldFullPath, $newFullPath)) {
            $newMedia = $media->replicate();
            $newMedia->path = $newRelativePath;
            $newMedia->name = $media->name . ' (Copy)';
            $newMedia->save();

            session()->flash('success', 'Media duplicated successfully.');
        } else {
            session()->flash('error', 'Failed to duplicate file.');
        }
    }

    public function delete($id)
    {
        $media = Media::findOrFail($id);
        $fullPath = storage_path('app/public/' . $media->path);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $media->delete();
        session()->flash('success', 'Media deleted successfully.');
    }

    public function with()
    {
        return [
            'mediaItems' => Media::latest()->paginate(24),
        ];
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Media Library</h1>
        <a href="{{ route('lara-veil.media.upload') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Add New Media
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-800 hover:text-green-900">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6 flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-800 hover:text-red-900">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($mediaItems as $item)
            <div class="relative group bg-white rounded-lg shadow overflow-hidden aspect-square border border-gray-200 hover:shadow-lg transition">
                @if(str_starts_with($item->mime_type, 'image/'))
                    <img src="{{ asset('storage/' . $item->path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-500 text-xs font-bold">
                        {{ strtoupper(pathinfo($item->path, PATHINFO_EXTENSION)) }}
                    </div>
                @endif

                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <a href="{{ route('lara-veil.media.edit', $item) }}" class="p-2 bg-white text-gray-900 rounded hover:bg-gray-100" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>

                    <button wire:click="duplicate({{ $item->id }})" wire:confirm="Duplicate this file?" class="p-2 bg-white text-gray-900 rounded hover:bg-gray-100" title="Duplicate">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </button>

                    <button wire:click="delete({{ $item->id }})" wire:confirm="Delete this file?" class="p-2 bg-red-500 text-white rounded hover:bg-red-600" title="Delete">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">No media found</h3>
                <p class="mt-1 text-sm text-gray-500">Upload images to get started.</p>
                <a href="{{ route('lara-veil.media.upload') }}" class="mt-6 inline-block px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Upload First Image
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $mediaItems->links() }}
    </div>
</div>
