<?php

use Livewire\Volt\Component;
use Scapteinc\LaraVeil\Models\Theme;

new class extends Component {
    public $themes = [];

    public function mount()
    {
        $this->themes = Theme::all();
    }

    public function activate(Theme $theme)
    {
        Theme::query()->update(['is_active' => false]);
        $theme->update(['is_active' => true]);
        app('theme.manager')->setActive($theme->slug);
        $this->mount();
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Themes Management</h1>

    @if($themes)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($themes as $theme)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $theme->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $theme->slug }}</p>

                        <div class="mb-4">
                            @if($theme->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">Inactive</span>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <a href="{{ route('lara-veil.themes.show', $theme) }}" class="block px-4 py-2 bg-blue-500 text-white rounded text-center hover:bg-blue-600 text-sm">
                                Details
                            </a>
                            @if(!$theme->is_active)
                                <button wire:click="activate({{ $theme->id }})" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm">
                                    Activate
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">No themes found.</p>
        </div>
    @endif
</div>
