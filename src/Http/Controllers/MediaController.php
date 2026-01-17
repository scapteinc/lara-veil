<?php

namespace Scapteinc\LaraVeil\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Scapteinc\LaraVeil\Models\Media;
use Scapteinc\LaraVeil\Services\Vrm\MediaForgeService;

class MediaController extends Controller
{
    /**
     * Upload and process media
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'collection_name' => 'nullable|string',
        ]);

        try {
            $mediaForge = app('media.forge');

            // Upload the file
            $path = $mediaForge
                ->upload($request->file('file'))
                ->run();

            // Create media record
            $media = Media::create([
                'model_type' => $request->input('model_type', 'App\Models\User'),
                'model_id' => $request->input('model_id', 0),
                'collection_name' => $request->input('collection_name', 'default'),
                'file_path' => $path,
                'disk' => 'public',
                'mime_type' => $request->file('file')->getMimeType(),
                'size' => $request->file('file')->getSize(),
            ]);

            return response()->json([
                'message' => 'File uploaded successfully',
                'media' => $media,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Upload failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get media details
     */
    public function show(Media $media)
    {
        return response()->json($media);
    }

    /**
     * Delete media
     */
    public function destroy(Media $media)
    {
        try {
            $mediaForge = app('media.forge');
            $result = $mediaForge->delete($media->file_path, 'all');

            $media->delete();

            return response()->json([
                'message' => 'Media deleted',
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Deletion failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Process media with operations
     */
    public function process(Request $request, Media $media)
    {
        $request->validate([
            'operations' => 'required|array',
        ]);

        try {
            $mediaForge = app('media.forge');

            // Load and apply operations
            $mediaForge->loadFromPath($media->file_path);

            foreach ($request->input('operations', []) as $operation) {
                $method = $operation['type'] ?? null;
                $args = $operation['args'] ?? [];

                if ($method && method_exists($mediaForge, $method)) {
                    $mediaForge->{$method}(...$args);
                }
            }

            $result = $mediaForge->run();

            return response()->json([
                'message' => 'Media processed',
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Processing failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get media preview/thumbnail
     */
    public function preview(Media $media)
    {
        // TODO: Implement preview generation
        return response()->json([
            'message' => 'Preview generated',
            'media' => $media,
        ]);
    }
}
