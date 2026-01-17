<?php

return [
    /**
     * MediaForge Configuration (Vormia)
     */
    'mediaforge' => [
        /**
         * Image processing driver
         * Options: 'auto', 'gd', 'imagick'
         * 'auto' will use Imagick if available, otherwise GD
         */
        'driver' => env('MEDIA_DRIVER', 'auto'),

        /**
         * Default image quality (1-100)
         */
        'default_quality' => env('MEDIA_QUALITY', 85),

        /**
         * Default output format
         * Options: 'jpg', 'jpeg', 'png', 'webp', 'gif'
         */
        'default_format' => env('MEDIA_FORMAT', 'webp'),

        /**
         * Auto-override original files
         * If false, processed images are saved separately
         */
        'auto_override' => env('MEDIA_AUTO_OVERRIDE', false),

        /**
         * Preserve original uploaded files
         * If false, original files are deleted after processing
         */
        'preserve_originals' => env('MEDIA_PRESERVE_ORIGINALS', true),

        /**
         * Thumbnail generation settings
         */
        'thumbnail' => [
            /**
             * Keep aspect ratio for thumbnails
             */
            'keep_aspect_ratio' => env('MEDIA_THUMB_ASPECT_RATIO', true),

            /**
             * Generate thumbnails from original image
             * If false, generate from processed image
             */
            'from_original' => env('MEDIA_THUMB_FROM_ORIGINAL', false),
        ],

        /**
         * Storage disk for uploads
         */
        'disk' => env('MEDIA_DISK', 'public'),

        /**
         * Storage visibility (public or private)
         */
        'visibility' => env('MEDIA_VISIBILITY', 'public'),

        /**
         * Use date-based folder structure (Y/m/d)
         */
        'use_date_folders' => env('MEDIA_DATE_FOLDERS', true),

        /**
         * Randomize file names
         */
        'randomize_filename' => env('MEDIA_RANDOMIZE_FILENAME', true),

        /**
         * Allowed file extensions
         */
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp',
            'pdf', 'doc', 'docx', 'xls', 'xlsx',
            'zip', 'mp4', 'webm', 'mov',
        ],

        /**
         * Maximum file size in bytes (0 = unlimited)
         */
        'max_size' => env('MEDIA_MAX_SIZE', 0),

        /**
         * Cleanup settings
         */
        'cleanup' => [
            /**
             * Delete orphaned files
             */
            'delete_orphaned' => true,

            /**
             * Delete backup files
             */
            'delete_backups' => true,
        ],
    ],
];
