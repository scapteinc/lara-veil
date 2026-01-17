@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Themes</h1>
        <a href="{{ route('lara-veil.system.info') }}" class="text-blue-500 hover:text-blue-700">System Info</a>
    </div>

    @if($themes->count())
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
                            <a href="{{ route('lara-veil.themes.show', $theme) }}" class="block px-4 py-2 bg-blue-500 text-white rounded text-center hover:bg-blue-600">
                                Details
                            </a>
                            @if(!$theme->is_active)
                                <form method="POST" action="{{ route('lara-veil.themes.activate', $theme) }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                        Activate
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('lara-veil.themes.customize', $theme) }}" class="block px-4 py-2 bg-purple-500 text-white rounded text-center hover:bg-purple-600">
                                Customize
                            </a>
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
@endsection
