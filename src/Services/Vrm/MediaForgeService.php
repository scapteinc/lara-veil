<?php

namespace Scapteinc\LaraVeil\Services\Vrm;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * MediaForgeService - Advanced media processing and management
 * 
 * This service handles:
 * - File uploads (UploadedFile, URLs, local paths)
 * - Image operations (resize, compress, convert, rotate, flip, blur)
 * - Thumbnail generation with multiple sizes
 * - Watermarking (image & text overlays)
 * - Avatar creation with rounded corners
 * - Format conversion (jpg, png, webp, gif)
 * - File deletion with type filtering
 * - Driver support (GD, Imagick)
 * - Database integration (Media model)
 */
class MediaForgeService
{
    protected $files = [];
    protected $urls = [];
    protected $uploadPath = null;
    protected $disk = 'public';
    protected array $operations = [];
    protected string $driver = 'auto';

    public function __construct(?string $driver = null)
    {
        $this->driver = $driver ?? config('vormia.mediaforge.driver', 'auto');
    }

    /**
     * Upload files
     */
    public function upload($files): self
    {
        if ($files instanceof UploadedFile) {
            $this->files[] = $files;
        } elseif (is_array($files)) {
            $this->files = array_merge($this->files, $files);
        }
        return $this;
    }

    /**
     * Upload from URL
     */
    public function uploadFromUrl($urls): self
    {
        if (is_string($urls)) {
            $this->urls[] = $urls;
        } elseif (is_array($urls)) {
            $this->urls = array_merge($this->urls, $urls);
        }
        return $this;
    }

    /**
     * Set upload path
     */
    public function to(?string $path): self
    {
        $this->uploadPath = $path;
        return $this;
    }

    /**
     * Add resize operation
     */
    public function resize(int $width, int $height, bool $keepAspectRatio = true, ?string $fillColor = null, ?bool $override = null): self
    {
        $this->operations[] = [
            'type' => 'resize',
            'width' => $width,
            'height' => $height,
            'keep_aspect_ratio' => $keepAspectRatio,
            'fill_color' => $fillColor,
            'override' => $override ?? config('vormia.mediaforge.auto_override', false),
        ];
        return $this;
    }

    /**
     * Add compress operation
     */
    public function compress(?int $quality = null, ?bool $override = null): self
    {
        $this->operations[] = [
            'type' => 'compress',
            'quality' => $quality ?? config('vormia.mediaforge.default_quality', 85),
            'override' => $override ?? config('vormia.mediaforge.auto_override', false),
        ];
        return $this;
    }

    /**
     * Add format conversion
     */
    public function convert(?string $format = null, ?int $quality = null, ?bool $progressive = false, ?bool $override = null): self
    {
        $this->operations[] = [
            'type' => 'convert',
            'format' => $format ?? config('vormia.mediaforge.default_format', 'webp'),
            'quality' => $quality ?? config('vormia.mediaforge.default_quality', 85),
            'progressive' => $progressive,
            'override' => $override ?? config('vormia.mediaforge.auto_override', false),
        ];
        return $this;
    }

    /**
     * Generate thumbnails
     */
    public function thumbnail(array $sizes, ?bool $keepAspectRatio = null, ?bool $fromOriginal = null, ?string $fillColor = null): self
    {
        $this->operations[] = [
            'type' => 'thumbnail',
            'sizes' => $sizes,
            'keep_aspect_ratio' => $keepAspectRatio ?? config('vormia.mediaforge.thumbnail.keep_aspect_ratio', true),
            'from_original' => $fromOriginal ?? config('vormia.mediaforge.thumbnail.from_original', false),
            'fill_color' => $fillColor,
        ];
        return $this;
    }

    /**
     * Add watermark
     */
    public function watermark(string $watermark, string $type = 'image', string $position = 'bottom-right', array $options = [], ?bool $override = null): self
    {
        $this->operations[] = [
            'type' => 'watermark',
            'watermark' => $watermark,
            'watermark_type' => $type,
            'position' => $position,
            'options' => $options,
            'override' => $override ?? config('vormia.mediaforge.auto_override', false),
        ];
        return $this;
    }

    /**
     * Create avatar
     */
    public function makeAvatar(int $size = 200, bool $rounded = true): self
    {
        $this->operations[] = [
            'type' => 'avatar',
            'size' => $size,
            'rounded' => $rounded,
        ];
        return $this;
    }

    /**
     * Rotate image
     */
    public function rotate(float $angle, string $bgcolor = '#ffffff'): self
    {
        $this->operations[] = [
            'type' => 'rotate',
            'angle' => $angle,
            'bgcolor' => $bgcolor,
        ];
        return $this;
    }

    /**
     * Flip image
     */
    public function flip(string $mode = 'h'): self
    {
        $this->operations[] = [
            'type' => 'flip',
            'mode' => $mode,
        ];
        return $this;
    }

    /**
     * Blur image
     */
    public function blur(int $amount = 5): self
    {
        $this->operations[] = [
            'type' => 'blur',
            'amount' => $amount,
        ];
        return $this;
    }

    /**
     * Set disk
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Execute operations
     */
    public function run()
    {
        if (empty($this->files) && empty($this->urls)) {
            throw new \Exception('No files to process');
        }

        $results = [];

        // TODO: Process files, URLs, and operations
        // Return file paths

        return count($results) === 1 ? $results[0] : $results;
    }

    /**
     * Delete media files
     */
    public function delete(string $filePath, string|array $type = 'all'): array
    {
        // TODO: Implement file deletion with type filtering
        return [
            'success' => true,
            'deleted_files' => [],
        ];
    }

    /**
     * Get available drivers
     */
    public static function getAvailableDrivers(): array
    {
        $drivers = [];
        
        if (extension_loaded('imagick')) {
            $drivers[] = 'imagick';
        }
        
        if (extension_loaded('gd')) {
            $drivers[] = 'gd';
        }
        
        return $drivers;
    }

    /**
     * Check if image processing is available
     */
    public static function isImageProcessingAvailable(): bool
    {
        return class_exists('Intervention\Image\ImageManager');
    }
}
