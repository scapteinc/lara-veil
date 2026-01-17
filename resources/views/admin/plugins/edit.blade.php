<x-layouts::app :title="'Edit Plugin Settings'">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Plugin Settings</h1>
            <a href="{{ route('lara-veil.plugins.index') }}" class="text-blue-500 hover:text-blue-700 flex items-center gap-1">
                ← Back to Plugins
            </a>
        </div>

        <div class="max-w-2xl">
            <div class="lara-veil-card">
                <div class="lara-veil-card-header">
                    <h2 class="font-semibold">{{ $plugin->name }}</h2>
                </div>
                <div class="lara-veil-card-body space-y-6">
                    <div>
                        <label class="lara-veil-form-label">Namespace</label>
                        <p class="text-sm text-gray-600 font-mono">{{ $plugin->namespace }}</p>
                    </div>

                    <div>
                        <label class="lara-veil-form-label">Version</label>
                        <p class="text-sm text-gray-600">{{ $plugin->version }}</p>
                    </div>

                    @if($plugin->author)
                        <div>
                            <label class="lara-veil-form-label">Author</label>
                            <p class="text-sm text-gray-600">{{ $plugin->author }}</p>
                        </div>
                    @endif

                    @if($plugin->description)
                        <div>
                            <label class="lara-veil-form-label">Description</label>
                            <p class="text-sm text-gray-600">{{ $plugin->description }}</p>
                        </div>
                    @endif

                    <div class="border-t pt-6">
                        <h3 class="font-semibold text-lg mb-4">Settings</h3>
                        <p class="text-sm text-gray-600 mb-4">Plugin settings can be configured here. Customize settings as needed.</p>

                        <form method="POST" action="{{ route('lara-veil.plugins.settings.update', $plugin) }}" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="lara-veil-form-group">
                                <label class="lara-veil-form-label">Custom Settings (JSON)</label>
                                <textarea name="metadata" class="lara-veil-form-input font-mono text-sm" rows="10">{{ json_encode($plugin->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</textarea>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="px-6 py-2 lara-veil-button lara-veil-button-primary">
                                    Save Settings
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
    </div>
</x-layouts::app>
