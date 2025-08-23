<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Media;

class MediaController extends Controller
{
   public function index()
    {
        try {
            $media = Media::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Media retrieved successfully',
                'data' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload multiple images
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240' // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $uploadedMedia = [];
            $images = $request->file('images');

            foreach ($images as $image) {
                // Generate unique filename
                $originalName = $image->getClientOriginalName();
                $extension = $image->getClientOriginalExtension();
                $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '_' . Str::random(6) . '.' . $extension;
                
                // Store image in public/media folder
                $path = $image->storeAs('media', $filename, 'public');
                
                // Get image dimensions
                $dimensions = getimagesize($image->getPathname());
                $width = $dimensions[0] ?? null;
                $height = $dimensions[1] ?? null;
                
                // Save to database
                $media = Media::create([
                    'name' => $originalName,
                    'filename' => $filename,
                    'path' => '/storage/' . $path,
                    'size' => $image->getSize(),
                    'type' => $image->getMimeType(),
                    'extension' => $extension,
                    'width' => $width,
                    'height' => $height,
                    'alt_text' => null,
                    'description' => null
                ]);

                $uploadedMedia[] = $media;
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedMedia) . ' images uploaded successfully',
                'data' => $uploadedMedia
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single media by ID
     */
    public function show($id)
    {
        try {
            $media = Media::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Media retrieved successfully',
                'data' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update media metadata
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $media = Media::findOrFail($id);
            
            $media->update([
                'alt_text' => $request->alt_text,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Media updated successfully',
                'data' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete media file
     */
    public function destroy($id)
    {
        try {
            $media = Media::findOrFail($id);
            
            // Delete file from storage
            $filePath = str_replace('/storage/', '', $media->path);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            
            // Delete from database
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get media by type (images, videos, documents, etc.)
     */
    public function getByType($type)
    {
        try {
            $typeMap = [
                'images' => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'],
                'videos' => ['video/mp4', 'video/avi', 'video/mov', 'video/wmv'],
                'documents' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
            ];

            if (!isset($typeMap[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid media type'
                ], 400);
            }

            $media = Media::whereIn('type', $typeMap[$type])
                         ->orderBy('created_at', 'desc')
                         ->get();

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' retrieved successfully',
                'data' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search media
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $type = $request->get('type', null);
            
            $media = Media::where('name', 'like', "%{$query}%")
                         ->orWhere('alt_text', 'like', "%{$query}%")
                         ->orWhere('description', 'like', "%{$query}%");
            
            if ($type) {
                $media->where('type', 'like', "%{$type}%");
            }
            
            $results = $media->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Search completed',
                'data' => $results,
                'count' => $results->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get storage statistics
     */
    public function getStats()
    {
        try {
            $totalFiles = Media::count();
            $totalSize = Media::sum('size');
            $imageCount = Media::where('type', 'like', 'image/%')->count();
            $recentUploads = Media::where('created_at', '>=', now()->subDays(7))->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_files' => $totalFiles,
                    'total_size' => $totalSize,
                    'total_size_formatted' => $this->formatBytes($totalSize),
                    'image_count' => $imageCount,
                    'recent_uploads' => $recentUploads
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
