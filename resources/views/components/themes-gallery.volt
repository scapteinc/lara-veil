<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Scapteinc\LaraVeil\Models\Theme;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'themes' => Theme::with('children')->whereNull('parent_id')->latest()->paginate(12),
        ];
    }

    public function activate($id)
    {
        // Deactivate all themes
        Theme::update(['is_active' => false]);

        // Activate selected theme
        $theme = Theme::findOrFail($id);
        $theme->update(['is_active' => true]);

        session()->flash('success', "{$theme->name} activated successfully.");
    }

    public function delete($id)
    {
        $theme = Theme::findOrFail($id);
        $themeName = $theme->name;

        // Delete child themes
        $theme->children()->delete();

        // Delete theme
        $theme->delete();

        session()->flash('success', "{$themeName} deleted successfully.");
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Themes</h1>
        <a href="{{ route('lara-veil.themes.index') }}?action=install" class="lara-veil-button lara-veil-button-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Install Theme
        </a>
    </div>

    @if(session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($themes as $theme)
            <div class="lara-veil-card overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Thumbnail -->
                <div class="relative h-40 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                    @if($theme->thumbnail_path)
                        <img src="{{ $theme->thumbnail_url }}" alt="{{ $theme->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.5a2 2 0 00-1 .267L7 21"/>
                            </svg>
                        </div>
                    @endif

                    @if($theme->is_active)
                        <div class="absolute top-2 right-2 px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Active
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="lara-veil-card-body space-y-4">
                    <!-- Header -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $theme->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $theme->slug }}</p>
                    </div>

                    <!-- Description -->
                    @if($theme->description)
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $theme->description }}</p>
                    @endif

                    <!-- Metadata -->
                    <div class="text-xs text-gray-500 space-y-1 border-t border-gray-200 pt-3">
                        @if($theme->version)
                            <div class="flex justify-between">
                                <span>Version:</span>
                                <span class="font-mono">{{ $theme->version }}</span>
                            </div>
                        @endif
                        @if($theme->author)
                            <div class="flex justify-between">
                                <span>Author:</span>
                                <span>{{ $theme->author }}</span>
                            </div>
                        @endif
                        @if($theme->children->count() > 0)
                            <div class="flex justify-between">
                                <span>Child Themes:</span>
                                <span>{{ $theme->children->count() }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>Installed:</span>
                            <span>{{ $theme->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-200 pt-4 flex gap-2">
                        <a href="{{ route('lara-veil.themes.show', $theme) }}" class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-xs font-medium transition">
                            Settings
                        </a>
                        @if(!$theme->is_active)
                            <button wire:click="activate({{ $theme->id }})" class="flex-1 px-3 py-2 bg-green-50 text-green-600 rounded hover:bg-green-100 text-xs font-medium transition">
                                Activate
                            </button>
                        @else
                            <button disabled class="flex-1 px-3 py-2 bg-gray-100 text-gray-400 rounded text-xs font-medium cursor-not-allowed">
                                Active
                            </button>
                        @endif
                        <button wire:click="delete({{ $theme->id }})" wire:confirm="Delete {{ $theme->name }}?" class="px-3 py-2 bg-red-50 text-red-600 rounded hover:bg-red-100 text-xs font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 text-center">
                <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.5a2 2 0 00-1 .267L7 21"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">No themes installed</h3>
                <p class="text-gray-600 mb-4">Get started by installing your first theme</p>
                <a href="{{ route('lara-veil.themes.index') }}?action=install" class="inline-block px-6 py-2 lara-veil-button lara-veil-button-primary">
                    Install Theme
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $themes->links() }}
    </div>
</div>
