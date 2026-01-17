<x-layouts::app :title="'Theme Settings'">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Theme Settings</h1>
            <a href="{{ route('lara-veil.themes.index') }}" class="text-blue-500 hover:text-blue-700 flex items-center gap-1">
                ← Back to Themes
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="lara-veil-card">
                    <div class="lara-veil-card-body space-y-4">
                        @if($theme->thumbnail_path)
                            <img src="{{ $theme->thumbnail_url }}" alt="{{ $theme->name }}" class="w-full rounded-lg">
                        @else
                            <div class="w-full h-40 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.5a2 2 0 00-1 .267L7 21"/>
                                </svg>
                            </div>
                        @endif

                        <div class="text-center">
                            <h2 class="text-xl font-bold">{{ $theme->name }}</h2>
                            <p class="text-xs text-gray-500">{{ $theme->slug }}</p>
                        </div>

                        <div class="border-t pt-4 space-y-2 text-sm">
                            @if($theme->version)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Version</span>
                                    <span class="font-mono">{{ $theme->version }}</span>
                                </div>
                            @endif
                            @if($theme->author)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Author</span>
                                    <span>{{ $theme->author }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status</span>
                                <span class="font-semibold" style="color: {{ $theme->is_active ? '#16a34a' : '#6b7280' }}">
                                    {{ $theme->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="lg:col-span-2 space-y-6">
                @if($theme->description)
                    <div class="lara-veil-card">
                        <div class="lara-veil-card-header">
                            <h3 class="font-semibold">Description</h3>
                        </div>
                        <div class="lara-veil-card-body">
                            <p class="text-gray-700">{{ $theme->description }}</p>
                        </div>
                    </div>
                @endif

                <div class="lara-veil-card">
                    <div class="lara-veil-card-header">
                        <h3 class="font-semibold">Theme Settings</h3>
                    </div>
                    <div class="lara-veil-card-body">
                        <form method="POST" action="{{ route('lara-veil.themes.settings.update', $theme) }}" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="lara-veil-form-group">
                                <label class="lara-veil-form-label">Custom Settings (JSON)</label>
                                <textarea name="metadata" class="lara-veil-form-input font-mono text-sm" rows="10">{{ json_encode($theme->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</textarea>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="px-6 py-2 lara-veil-button lara-veil-button-primary">
                                    Save Settings
                                </button>
                                <a href="{{ route('lara-veil.themes.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                @if($theme->children->count() > 0)
                    <div class="lara-veil-card">
                        <div class="lara-veil-card-header">
                            <h3 class="font-semibold">Child Themes ({{ $theme->children->count() }})</h3>
                        </div>
                        <div class="lara-veil-card-body">
                            <div class="space-y-3">
                                @foreach($theme->children as $child)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded">
                                        <div>
                                            <p class="font-medium">{{ $child->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $child->slug }}</p>
                                        </div>
                                        <a href="{{ route('lara-veil.themes.show', $child) }}" class="text-blue-500 hover:text-blue-700 text-sm">
                                            View
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app>
