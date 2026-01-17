<x-layouts::app :title="'Plugins'">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Plugins</h1>
            <a href="{{ route('lara-veil.plugins.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Add Plugin
            </a>
        </div>

        @if($plugins->count())
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Version</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($plugins as $plugin)
                            <tr>
                                <td class="px-6 py-4">{{ $plugin->name }}</td>
                                <td class="px-6 py-4">{{ $plugin->version }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $plugin->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($plugin->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2">
                                    <a href="{{ route('lara-veil.plugins.edit', $plugin) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                    @if($plugin->status !== 'active')
                                        <form method="POST" action="{{ route('lara-veil.plugins.activate', $plugin) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-500 hover:text-green-700">Activate</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('lara-veil.plugins.deactivate', $plugin) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-500 hover:text-red-700">Deactivate</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('lara-veil.plugins.destroy', $plugin) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-500 mb-4">No plugins found.</p>
                <a href="{{ route('lara-veil.plugins.create') }}" class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Create First Plugin
                </a>
            </div>
        @endif
    </div>
</x-layouts::app>
