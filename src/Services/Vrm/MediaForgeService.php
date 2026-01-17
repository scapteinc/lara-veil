<?php

namespace Scapteinc\LaraVeil\Services\Vrm;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GdDriver;
use Intervention\Image\Drivers\ImageickDriver;

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
    protected ?ImageManager $imageManager = null;

    public function __construct(?string $driver = null)
    {
        $this->driver = $driver ?? config('vormia.mediaforge.driver', 'auto');
        $this->initializeImageManager();
    }

    /**
     * Initialize the image manager with appropriate driver
     */
    protected function initializeImageManager(): void
    {
        try {
            $driver = $this->driver === 'auto' ? $this->detectDriver() : $this->driver;

            $this->imageManager = match ($driver) {
                'imagick' => new ImageManager(new ImageickDriver()),
                'gd' => new ImageManager(new GdDriver()),
                default => new ImageManager(new GdDriver()),
            };
        } catch (\Exception $e) {
            Log::warning('Failed to initialize image manager: ' . $e->getMessage());
            $this->imageManager = new ImageManager(new GdDriver());
        }
    }

    /**
     * Detect available image driver
     */
    protected function detectDriver(): string
    {
        if (extension_loaded('imagick')) {
            return 'imagick';
        }
        return 'gd';
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

        // Process uploaded files
        foreach ($this->files as $file) {
            try {
                $result = $this->processFile($file);
                $results[] = $result;
            } catch (\Exception $e) {
                Log::error('Error processing file: ' . $e->getMessage());
            }
        }

        // Process URLs
        foreach ($this->urls as $url) {
            try {
                $result = $this->processUrl($url);
                $results[] = $result;
            } catch (\Exception $e) {
                Log::error('Error processing URL: ' . $e->getMessage());
            }
        }

        // Reset for next batch
        $this->files = [];
        $this->urls = [];
        $this->operations = [];

        return count($results) === 1 ? $results[0] : $results;
    }

    /**
     * Process uploaded file
     */
    protected function processFile(UploadedFile $file): array
    {
        $image = $this->imageManager->read($file->getRealPath());
        $originalPath = $this->uploadPath ? "{$this->uploadPath}/{$file->getClientOriginalName()}" : "media/{$file->getClientOriginalName()}";

        // Store original
        Storage::disk($this->disk)->put($originalPath, file_get_contents($file->getRealPath()));

        $results = ['original' => $originalPath];

        // Apply operations
        foreach ($this->operations as $operation) {
            $operationType = $operation['type'];

            switch ($operationType) {
                case 'resize':
                    $image = $this->applyResize($image, $operation);
                    break;
                case 'compress':
                    $image = $this->applyCompress($image, $operation);
                    break;
                case 'convert':
                    $results = array_merge($results, $this->applyConvert($image, $originalPath, $operation));
                    break;
                case 'thumbnail':
                    $results = array_merge($results, $this->applyThumbnail($image, $originalPath, $operation));
                    break;
                case 'watermark':
                    $image = $this->applyWatermark($image, $operation);
                    break;
                case 'avatar':
                    $image = $this->applyAvatar($image, $operation);
                    break;
                case 'rotate':
                    $image = $this->applyRotate($image, $operation);
                    break;
                case 'flip':
                    $image = $this->applyFlip($image, $operation);
                    break;
                case 'blur':
                    $image = $this->applyBlur($image, $operation);
                    break;
            }
        }

        return $results;
    }

    /**
     * Process URL
     */
    protected function processUrl(string $url): array
    {
        $image = $this->imageManager->read($url);
        $fileName = basename($url);
        $originalPath = $this->uploadPath ? "{$this->uploadPath}/{$fileName}" : "media/{$fileName}";

        // Store original
        Storage::disk($this->disk)->put($originalPath, (string) $image->encode());

        $results = ['original' => $originalPath];

        // Apply operations
        foreach ($this->operations as $operation) {
            $operationType = $operation['type'];

            switch ($operationType) {
                case 'resize':
                    $image = $this->applyResize($image, $operation);
                    break;
                case 'compress':
                    $image = $this->applyCompress($image, $operation);
                    break;
                case 'convert':
                    $results = array_merge($results, $this->applyConvert($image, $originalPath, $operation));
                    break;
                case 'thumbnail':
                    $results = array_merge($results, $this->applyThumbnail($image, $originalPath, $operation));
                    break;
                case 'watermark':
                    $image = $this->applyWatermark($image, $operation);
                    break;
                case 'avatar':
                    $image = $this->applyAvatar($image, $operation);
                    break;
                case 'rotate':
                    $image = $this->applyRotate($image, $operation);
                    break;
                case 'flip':
                    $image = $this->applyFlip($image, $operation);
                    break;
                case 'blur':
                    $image = $this->applyBlur($image, $operation);
                    break;
            }
        }

        return $results;
    }

    /**
     * Apply resize operation
     */
    protected function applyResize($image, array $operation)
    {
        try {
            $width = $operation['width'];
            $height = $operation['height'];
            $keepAspectRatio = $operation['keep_aspect_ratio'] ?? true;
            $fillColor = $operation['fill_color'] ?? '#ffffff';

            if ($keepAspectRatio) {
                // Fit within dimensions maintaining aspect ratio
                $image = $image->scale($width, $height);
                // Center on canvas with fill color
                $image = $image->pad($width, $height, $fillColor);
            } else {
                $image = $image->resize($width, $height);
            }

            Log::info('Image resized to ' . $width . 'x' . $height);
        } catch (\Exception $e) {
            Log::error('Resize failed: ' . $e->getMessage());
        }

        return $image;
    }

    /**
     * Apply compress operation
     */
    protected function applyCompress($image, array $operation)
    {
        try {
            $quality = $operation['quality'] ?? 85;
            $image = $image->toJpeg($quality);
            Log::info('Image compressed to quality ' . $quality);
        } catch (\Exception $e) {
            Log::error('Compress failed: ' . $e->getMessage());
        }

        return $image;
    }

    /**
     * Apply format conversion
     */
    protected function applyConvert($image, string $originalPath, array $operation): array
    {
        $results = [];
        try {
            $format = strtolower($operation['format'] ?? 'webp');
            $quality = $operation['quality'] ?? 85;
            $progressive = $operation['progressive'] ?? false;

            $pathInfo = pathinfo($originalPath);
            $convertedPath = "{$pathInfo['dirname']}/{$pathInfo['filename']}.{$format}";

            // Convert based on format
            $converted = match ($format) {
                'jpg', 'jpeg' => $image->toJpeg($quality),
                'png' => $image->toPng(),
                'webp' => $image->toWebp($quality),
                'gif' => $image->toGif(),
                default => $image->encode(),
            };

            Storage::disk($this->disk)->put($convertedPath, (string) $converted);
            $results['converted'] = $convertedPath;

            Log::info('Image converted to ' . $format);
        } catch (\Exception $e) {
            Log::error('Convert failed: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Apply thumbnail generation
     */
    protected function applyThumbnail($image, string $originalPath, array $operation): array
    {
        $results = [];
        try {
            $sizes = $operation['sizes'] ?? [100, 200, 300];
            $keepAspectRatio = $operation['keep_aspect_ratio'] ?? true;
            $fillColor = $operation['fill_color'] ?? '#ffffff';
            $pathInfo = pathinfo($originalPath);

            foreach ($sizes as $size) {
                $thumbPath = "{$pathInfo['dirname']}/{$pathInfo['filename']}_thumb_{$size}.{$pathInfo['extension']}";

                if ($keepAspectRatio) {
                    $thumb = $image->scale($size, $size)->pad($size, $size, $fillColor);
                } else {
                    $thumb = $image->resize($size, $size);
                }

                Storage::disk($this->disk)->put($thumbPath, (string) $thumb->encode());
                $results["thumbnail_{$size}"] = $thumbPath;
            }

            Log::info('Generated ' . count($sizes) . ' thumbnails');
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Apply watermark
     */
    protected function applyWatermark($image, array $operation)
    {
        try {
            $watermark = $operation['watermark'];
            $type = $operation['watermark_type'] ?? 'image';
            $position = $operation['position'] ?? 'bottom-right';
            $options = $operation['options'] ?? [];

            if ($type === 'image') {
                // Apply image watermark
                $watermarkImage = $this->imageManager->read(storage_path($watermark));
                $opacity = $options['opacity'] ?? 0.5;
                $watermarkImage = $watermarkImage->opacity($opacity);

                // Position watermark
                [$x, $y] = $this->calculatePosition($image, $watermarkImage, $position);
                $image = $image->place($watermarkImage, 'top-left', $x, $y);
            } elseif ($type === 'text') {
                // Apply text watermark
                $text = $options['text'] ?? 'Watermark';
                $fontSize = $options['size'] ?? 40;
                $color = $options['color'] ?? 'ffffff';
                $opacity = $options['opacity'] ?? 0.5;

                $image = $image->text($text, 100, 100, function($font) use ($fontSize, $color) {
                    $font->size($fontSize);
                    $font->color($color);
                });
            }

            Log::info('Watermark applied: ' . $type);
        } catch (\Exception $e) {
            Log::error('Watermark failed: ' . $e->getMessage());
        }

        return $image;
    }

    /**
     * Apply avatar creation (rounded square)
     */
    protected function applyAvatar($image, array $operation)
    {
        try {
            $size = $operation['size'] ?? 200;
            $rounded = $operation['rounded'] ?? true;

            // Resize to square
            $image = $image->resize($size, $size);

            // Create rounded corners if requested
            if ($rounded) {
                $image = $image->drawCircle(
                    $size / 2,
                    $size / 2,
                    $size / 2,
                    function($circle) {
                        $circle->background('#ffffff');
                    }
                );
            }

            Log::info('Avatar created with size ' . $size);
        } catch (\Exception $e) {
            Log::error('Avatar creation failed: ' . $e->getMessage());
        }

        return $image;
    }

    /**
     * Apply rotate operation
     */
    protected function applyRotate($image, array $operation)
    {
        try {
            $angle = $operation['angle'] ?? 0;
            $bgcolor = $operation['bgcolor'] ?? 'ffffff';

            $image = $image->rotate(-$angle, $bgcolor);
            Log::info('Image rotated ' . $angle . ' degrees');
        } catch (\Exception $e) {
            Log::error('Rotate failed: ' . $e->getMessage());
        }

        return $image;
    }

    /**
     * Apply flip operation
     */
    protected function applyFlip($image, array $operation)
    {
        try {
            $mode = $operation['mode'] ?? 'h';

            if ($mode === 'h') {
                $image = $image->flip('h');
            } elseif ($mode === 'v') {
                $image = $image->flip('v');
            } elseif ($mode === 'both') {
                $image = $image->flip('h')->flip('v');
            }

            Log::info('Image flipped: ' . $mode);
        } catch (\Exception $e) {
            Log::error('Flip failed: ' . $e->getMessage());
        }

        return $image;
    }

    /**
     * Apply blur operation
     */
    protected function applyBlur($image, array $operation)
    {
        try {
            $amount = $operation['amount'] ?? 5;
            $image = $image->blur($amount);
            Log::info('Image blurred with amount ' . $amount);
        } catch (\Exception $e) {
            Log::error('Blur failed: ' . $e->getMessage());
        }

        return $image;
    }

    /**
     * Calculate position for watermark
     */
    protected function calculatePosition($image, $watermark, string $position): array
    {
        $imageWidth = $image->width();
        $imageHeight = $image->height();
        $watermarkWidth = $watermark->width();
        $watermarkHeight = $watermark->height();
        $padding = 10;

        return match ($position) {
            'top-left' => [0, 0],
            'top-center' => [($imageWidth - $watermarkWidth) / 2, 0],
            'top-right' => [$imageWidth - $watermarkWidth, 0],
            'center-left' => [0, ($imageHeight - $watermarkHeight) / 2],
            'center' => [($imageWidth - $watermarkWidth) / 2, ($imageHeight - $watermarkHeight) / 2],
            'center-right' => [$imageWidth - $watermarkWidth, ($imageHeight - $watermarkHeight) / 2],
            'bottom-left' => [0, $imageHeight - $watermarkHeight],
            'bottom-center' => [($imageWidth - $watermarkWidth) / 2, $imageHeight - $watermarkHeight],
            'bottom-right' => [$imageWidth - $watermarkWidth - $padding, $imageHeight - $watermarkHeight - $padding],
            default => [($imageWidth - $watermarkWidth) / 2, ($imageHeight - $watermarkHeight) / 2],
        };
    }

    /**
     * Delete media files
     */
    public function delete(string $filePath, string|array $type = 'all'): array
    {
        try {
            $deleted = [];
            $types = is_string($type) ? [$type] : $type;

            // Delete original file
            if ($this->shouldDelete('original', $types)) {
                if (Storage::disk($this->disk)->exists($filePath)) {
                    Storage::disk($this->disk)->delete($filePath);
                    $deleted[] = $filePath;
                }
            }

            // Delete related variants (thumbnails, converted, watermarked, etc.)
            $pathInfo = pathinfo($filePath);
            $pattern = "{$pathInfo['dirname']}/{$pathInfo['filename']}_*";

            $files = Storage::disk($this->disk)->files($pathInfo['dirname']);
            foreach ($files as $file) {
                if (str_starts_with(basename($file), $pathInfo['filename'] . '_')) {
                    $variant = $this->detectVariant(basename($file));

                    if ($this->shouldDelete($variant, $types)) {
                        Storage::disk($this->disk)->delete($file);
                        $deleted[] = $file;
                    }
                }
            }

            Log::info('Deleted ' . count($deleted) . ' media files');

            return [
                'success' => true,
                'deleted_files' => $deleted,
                'count' => count($deleted),
            ];
        } catch (\Exception $e) {
            Log::error('Delete failed: ' . $e->getMessage());

            return [
                'success' => false,
                'deleted_files' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Determine if a variant should be deleted
     */
    protected function shouldDelete(string $variant, array $types): bool
    {
        if (in_array('all', $types)) {
            return true;
        }

        return in_array($variant, $types);
    }

    /**
     * Detect variant type from filename
     */
    protected function detectVariant(string $filename): string
    {
        if (str_contains($filename, '_thumb_')) {
            return 'thumbnail';
        }
        if (str_contains($filename, '.webp')) {
            return 'converted';
        }
        if (str_contains($filename, '_watermark')) {
            return 'watermark';
        }
        if (str_contains($filename, '_avatar')) {
            return 'avatar';
        }
        return 'variant';
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
