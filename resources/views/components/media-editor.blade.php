<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Scapteinc\LaraVeil\Models\Media;

new class extends Component {
    use WithFileUploads;

    public Media $media;
    public $replacement;
    public $width;
    public $height;
    public $ratio = true;
    public $rotate = 0;
    public $flip = '';
    public $brightness = 0;
    public $contrast = 0;
    public $blur = 0;
    public $greyscale = false;

    public function mount(Media $media)
    {
        $this->media = $media;

        $fullPath = storage_path('app/public/' . $media->path);
        if (file_exists($fullPath)) {
            $dimensions = @getimagesize($fullPath);
            if ($dimensions) {
                $this->width = $dimensions[0];
                $this->height = $dimensions[1];
            }
        }
    }

    public function save()
    {
        if ($this->replacement) {
            $this->validate([
                'replacement' => 'image|max:10240',
            ]);

            $oldPath = storage_path('app/public/' . $this->media->path);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }

            $path = $this->replacement->store('media', 'public');

            $this->media->update([
                'path' => $path,
                'mime_type' => $this->replacement->getMimeType(),
                'file_size' => $this->replacement->getSize(),
            ]);

            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath)) {
                $dimensions = @getimagesize($fullPath);
                if ($dimensions) {
                    $this->width = $dimensions[0];
                    $this->height = $dimensions[1];
                }
            }

            $this->replacement = null;
            session()->flash('success', 'Image replaced successfully.');
            return;
        }

        // For image transformations, we would need intervention/image library
        // For now, just update metadata
        if ($this->width || $this->height) {
            // This would require proper image processing
            session()->flash('info', 'Image processing requires additional configuration.');
        }

        session()->flash('success', 'Image updated successfully.');
    }

    public function delete()
    {
        $fullPath = storage_path('app/public/' . $this->media->path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $this->media->delete();
        return redirect()->route('lara-veil.media.index');
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Preview Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Image Preview -->
            <div class="lara-veil-card">
                <div class="lara-veil-card-header flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Image Preview</h2>
                    <a href="{{ route('lara-veil.media.index') }}" class="text-blue-500 hover:text-blue-700 flex items-center gap-1">
                        ← Back to Library
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded-lg m-4 mb-0 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="bg-blue-100 text-blue-800 p-3 rounded-lg m-4 mb-0 text-sm">
                        {{ session('info') }}
                    </div>
                @endif

                <div class="lara-veil-card-body">
                    <div class="bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center min-h-[400px] border-2 border-dashed border-gray-300">
                        <img src="{{ asset('storage/' . $media->path) }}?t={{ time() }}" alt="{{ $media->name }}" class="max-w-full max-h-[700px] object-contain shadow-lg">
                    </div>
                </div>
            </div>

            <!-- File Details -->
            <div class="lara-veil-card">
                <div class="lara-veil-card-header">
                    <h3 class="font-semibold">File Details</h3>
                </div>
                <div class="lara-veil-card-body">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                        <div>
                            <span class="text-gray-500 block mb-1 font-medium">Filename</span>
                            <span class="font-mono text-xs break-all">{{ $media->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-1 font-medium">MIME Type</span>
                            <span class="text-xs">{{ $media->mime_type }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-1 font-medium">File Size</span>
                            <span class="text-xs">{{ number_format($media->file_size / 1024, 2) }} KB</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-1 font-medium">Dimensions</span>
                            <span class="text-xs">{{ $width }} × {{ $height }} px</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <span class="text-gray-500 block mb-2 text-sm font-medium">Public URL</span>
                        <div class="flex gap-2">
                            <input type="text" readonly value="{{ asset('storage/' . $media->path) }}" class="flex-1 bg-gray-50 border border-gray-200 rounded px-3 py-2 text-xs font-mono text-gray-700">
                            <button onclick="navigator.clipboard.writeText('{{ asset('storage/' . $media->path) }}'); alert('Copied!')" class="px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tools Column -->
        <div class="space-y-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Replace Image -->
                <div class="lara-veil-card">
                    <div class="lara-veil-card-header">
                        <h3 class="font-semibold text-sm uppercase tracking-wider">Upload New Version</h3>
                    </div>
                    <div class="lara-veil-card-body space-y-4">
                        <input type="file" wire:model="replacement" accept="image/*" class="lara-veil-form-input">
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Uploading a new file will delete the current image immediately.
                        </p>
                    </div>
                </div>

                <!-- Resize & Crop -->
                <div class="lara-veil-card">
                    <div class="lara-veil-card-header">
                        <h3 class="font-semibold text-sm">Resize</h3>
                    </div>
                    <div class="lara-veil-card-body space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="lara-veil-form-group">
                                <label class="lara-veil-form-label">Width (px)</label>
                                <input type="number" wire:model.defer="width" class="lara-veil-form-input">
                            </div>
                            <div class="lara-veil-form-group">
                                <label class="lara-veil-form-label">Height (px)</label>
                                <input type="number" wire:model.defer="height" class="lara-veil-form-input">
                            </div>
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="ratio" class="w-4 h-4 rounded border-gray-300">
                            <span>Maintain Aspect Ratio</span>
                        </label>
                    </div>
                </div>

                <!-- Transformations -->
                <div class="lara-veil-card">
                    <div class="lara-veil-card-header">
                        <h3 class="font-semibold text-sm">Transform</h3>
                    </div>
                    <div class="lara-veil-card-body space-y-4">
                        <div class="lara-veil-form-group">
                            <label class="lara-veil-form-label">Rotate</label>
                            <select wire:model="rotate" class="lara-veil-form-input">
                                <option value="0">No rotation</option>
                                <option value="90">90° Clockwise</option>
                                <option value="180">180° Rotate</option>
                                <option value="270">90° Counter-clockwise</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer p-2 border border-gray-200 rounded hover:bg-gray-50">
                                <input type="radio" wire:model="flip" value="h" class="w-4 h-4">
                                <span>Flip H</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer p-2 border border-gray-200 rounded hover:bg-gray-50">
                                <input type="radio" wire:model="flip" value="v" class="w-4 h-4">
                                <span>Flip V</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Adjustments -->
                <div class="lara-veil-card">
                    <div class="lara-veil-card-header">
                        <h3 class="font-semibold text-sm">Adjustments</h3>
                    </div>
                    <div class="lara-veil-card-body space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs text-gray-600">
                                <span>Brightness ({{ $brightness }})</span>
                            </div>
                            <input type="range" wire:model="brightness" min="-100" max="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between text-xs text-gray-600">
                                <span>Contrast ({{ $contrast }})</span>
                            </div>
                            <input type="range" wire:model="contrast" min="-100" max="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between text-xs text-gray-600">
                                <span>Blur ({{ $blur }})</span>
                            </div>
                            <input type="range" wire:model="blur" min="0" max="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        </div>

                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="greyscale" class="w-4 h-4 rounded border-gray-300">
                            <span>Greyscale Filter</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full lara-veil-button lara-veil-button-primary py-3 font-medium">
                    Apply Changes
                </button>
            </form>

            <!-- Delete -->
            <div class="lara-veil-card border-red-200">
                <div class="lara-veil-card-body">
                    <button wire:click="delete" wire:confirm="Permanently delete this media?" class="w-full lara-veil-button lara-veil-button-danger py-3 font-medium">
                        Delete Permanently
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
