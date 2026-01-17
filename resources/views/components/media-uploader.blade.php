<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $file;
    public $uploading = false;
    public $progress = 0;

    public function save()
    {
        $this->validate([
            'file' => 'required|file|max:10240',
        ]);

        // Store file
        $path = $this->file->store('media', 'public');

        // Create media record
        \Scapteinc\LaraVeil\Models\Media::create([
            'name' => $this->file->getClientOriginalName(),
            'path' => $path,
            'media_type' => str_starts_with($this->file->getMimeType(), 'image') ? 'image' : 'file',
            'mime_type' => $this->file->getMimeType(),
            'file_size' => $this->file->getSize(),
        ]);

        session()->flash('success', 'File uploaded successfully.');
        return redirect()->route('lara-veil.media.index');
    }
}; ?>

<div class="container mx-auto px-4 py-8 max-w-xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Upload Media</h1>
        <a href="{{ route('lara-veil.media.index') }}" class="text-blue-500 hover:text-blue-700 flex items-center gap-1">
            ← Back to Library
        </a>
    </div>

    <div class="lara-veil-card">
        <div class="lara-veil-card-body">
            <form wire:submit="save" class="space-y-6">
                <div
                    x-data="{
                        uploading: false,
                        progress: 0,
                        init() {
                            this.$watch('$wire.uploading', value => {
                                this.uploading = value;
                            });
                            this.$watch('$wire.progress', value => {
                                this.progress = value;
                            });
                        }
                    }"
                >
                    <div class="lara-veil-form-group">
                        <label class="lara-veil-form-label">Select File</label>
                        <input
                            type="file"
                            wire:model="file"
                            required
                            class="lara-veil-form-input"
                        >
                    </div>

                    <div x-show="uploading" class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-2 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Uploading... <span x-text="progress + '%'"></span></p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('lara-veil.media.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 lara-veil-button lara-veil-button-primary">
                        Upload Media
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
