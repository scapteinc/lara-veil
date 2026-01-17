<x-layouts::app :title="'Media Library'">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Media Library</h1>
            <a href="{{ route('lara-veil.media.upload') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Upload File
            </a>
        </div>

    @if($media->count())
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File Path</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Collection</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($media as $file)
                        <tr>
                            <td class="px-6 py-4 text-sm">{{ basename($file->file_path) }}</td>
                            <td class="px-6 py-4 text-sm">{{ $file->mime_type }}</td>
                            <td class="px-6 py-4 text-sm">{{ number_format($file->size / 1024, 2) }} KB</td>
                            <td class="px-6 py-4 text-sm">{{ $file->collection_name }}</td>
                            <td class="px-6 py-4 space-x-2">
                                <a href="{{ route('lara-veil.media.show', $file) }}" class="text-blue-500 hover:text-blue-700">View</a>
                                <form method="POST" action="{{ route('lara-veil.media.destroy', $file) }}" class="inline" onsubmit="return confirm('Are you sure?')">
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

        <div class="mt-4">
            {{ $media->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500 mb-4">No media files found.</p>
            <a href="{{ route('lara-veil.media.upload') }}" class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Upload File
            </a>
        </div>
    @endif
</x-layouts::app>
