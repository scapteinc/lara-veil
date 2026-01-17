<x-layouts::app :title="'Create Plugin'">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <a href="{{ route('lara-veil.plugins.index') }}" class="text-blue-500 hover:text-blue-700">&larr; Back to Plugins</a>
        </div>

    <div class="bg-white rounded-lg shadow p-8 max-w-2xl">
        <h1 class="text-3xl font-bold mb-6">Create Plugin</h1>

        <form method="POST" action="{{ route('lara-veil.plugins.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Plugin Name</label>
                <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('name') }}">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="namespace" class="block text-sm font-medium text-gray-700 mb-1">Namespace</label>
                <input type="text" id="namespace" name="namespace" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('namespace') }}">
                @error('namespace')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="version" class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                <input type="text" id="version" name="version" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('version', '1.0.0') }}">
                @error('version')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Create Plugin
                </button>
                <a href="{{ route('lara-veil.plugins.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-layouts::app>
