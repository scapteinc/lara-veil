<x-layouts::app :title="'Create Plugin'">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Install Plugin</h1>
            <a href="{{ route('lara-veil.plugins.index') }}" class="text-blue-500 hover:text-blue-700 flex items-center gap-1">
                ← Back to Plugins
            </a>
        </div>

        <div class="max-w-2xl">
            <div class="lara-veil-card">
                <div class="lara-veil-card-header">
                    <h2 class="font-semibold">Install New Plugin</h2>
                </div>
                <div class="lara-veil-card-body">
                    <form method="POST" action="{{ route('lara-veil.plugins.store') }}" class="space-y-6">
                        @csrf

                        <div class="lara-veil-form-group">
                            <label class="lara-veil-form-label">Plugin Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="lara-veil-form-input" placeholder="e.g., User Addon" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lara-veil-form-group">
                            <label class="lara-veil-form-label">Namespace *</label>
                            <input type="text" name="namespace" value="{{ old('namespace') }}" class="lara-veil-form-input" placeholder="e.g., MyCompany\UserAddon" required>
                            @error('namespace')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lara-veil-form-group">
                            <label class="lara-veil-form-label">Version *</label>
                            <input type="text" name="version" value="{{ old('version', '1.0.0') }}" class="lara-veil-form-input" placeholder="1.0.0" required>
                            @error('version')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lara-veil-form-group">
                            <label class="lara-veil-form-label">Author</label>
                            <input type="text" name="author" value="{{ old('author') }}" class="lara-veil-form-input" placeholder="Author name">
                        </div>

                        <div class="lara-veil-form-group">
                            <label class="lara-veil-form-label">Description</label>
                            <textarea name="description" class="lara-veil-form-input" rows="4" placeholder="Plugin description">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="px-6 py-2 lara-veil-button lara-veil-button-primary">
                                Install Plugin
                            </button>
                            <a href="{{ route('lara-veil.plugins.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
