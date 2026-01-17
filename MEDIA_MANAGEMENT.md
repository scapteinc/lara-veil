# Media Management Implementation Guide

## Overview

The Lara-Veil package now includes a complete, production-ready media management system with an advanced admin interface powered by Laravel Volt components. This guide covers the implementation, architecture, and features.

## Database Schema

### Media Table Migration

Location: `src/Database/Migrations/2024_01_17_000003_create_media_table.php`

```sql
CREATE TABLE media (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                    -- Original filename
    path VARCHAR(500) NOT NULL,                    -- Storage path (relative)
    media_type VARCHAR(255) DEFAULT 'file',        -- 'image', 'audio', 'video', 'document', 'file'
    mime_type VARCHAR(255) NULLABLE,               -- 'image/jpeg', 'application/pdf', etc.
    file_size BIGINT UNSIGNED NULLABLE,            -- File size in bytes
    width UNSIGNED INTEGER NULLABLE,               -- Image width in pixels
    height UNSIGNED INTEGER NULLABLE,              -- Image height in pixels
    disk VARCHAR(255) DEFAULT 'public',            -- Storage disk name
    model_type VARCHAR(255) NULLABLE,              -- Polymorphic: Model being referenced
    model_id BIGINT UNSIGNED NULLABLE,             -- Polymorphic: Model ID
    collection_name VARCHAR(255) NULLABLE,        -- Collection grouping
    metadata JSON NULLABLE,                        -- Custom metadata storage
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX (model_type, model_id, collection_name),
    INDEX (collection_name),
    INDEX (media_type)
);
```

## Models

### Media Model

Location: `src/Models/Media.php`

**Fillable Attributes:**
```php
[
    'name', 'path', 'media_type', 'mime_type', 'file_size',
    'width', 'height', 'disk', 'model_type', 'model_id',
    'collection_name', 'metadata'
]
```

**Key Methods:**
- `model()` - Polymorphic relationship to owning model
- `getPublicUrlAttribute()` - Returns asset URL for the media
- `isImage()` - Boolean check if media is image type
- `getFormattedSizeAttribute()` - Returns human-readable file size

**Casts:**
- `metadata` - JSON array

## Routes

### Media Management Routes

Base path: `/admin/media`

| Method | Route | Controller | Name | Purpose |
|--------|-------|-----------|------|---------|
| GET | `/` | index | `lara-veil.media.index` | List all media (Gallery grid) |
| GET | `/create` | create | `lara-veil.media.create` | Show upload form |
| GET | `/upload` | uploadForm | `lara-veil.media.upload` | Alias for upload form |
| POST | `/` | store | `lara-veil.media.store` | Process file upload |
| GET | `/{media}` | show | `lara-veil.media.show` | View media details |
| GET | `/{media}/edit` | edit | `lara-veil.media.edit` | Edit media (transformations) |
| PUT | `/{media}` | update | `lara-veil.media.update` | Save media changes |
| DELETE | `/{media}` | destroy | `lara-veil.media.destroy` | Delete media |

All routes are protected by `auth` middleware.

## Controllers

### MediaManagementController

Location: `src/Http/Controllers/Admin/MediaManagementController.php`

**Key Methods:**

#### index()
- Returns gallery grid view
- Handles pagination and filtering (delegated to Volt component)
- Route: `GET /admin/media`

#### create()
- Shows upload form
- Route: `GET /admin/media/create`

#### store()
- Validates file upload (max 10MB)
- Stores file to `storage/app/public/media/`
- Creates Media record with:
  - Original filename
  - Storage path
  - MIME type detection
  - Image dimension detection
  - Media type categorization (image/file)
- Redirects to gallery on success
- Route: `POST /admin/media`

#### edit(Media $media)
- Returns media editor view
- Provides image for transformation/replacement
- Route: `GET /admin/media/{id}/edit`

#### update(Request $request, Media $media)
- Updates metadata/name
- Route: `PUT /admin/media/{id}`

#### destroy(Media $media)
- Deletes file from storage
- Removes database record
- Route: `DELETE /admin/media/{id}`

## Volt Components

### 1. media-gallery-grid.volt

**Location:** `resources/views/components/media-gallery-grid.volt`

**Purpose:** Gallery view with pagination and media management

**Livewire Traits:**
- `WithPagination` - Handles pagination (24 per page)

**Properties:**
```php
public $items = [];
public $duplicateCount = [];
```

**Public Methods:**

`mount()`
- Initializes component
- Loads media items

`loadItems()`
- Fetches paginated media from database
- Queries: `Media::paginate(24)`

`duplicate($mediaId)`
- Creates copy of media file
- Auto-detects duplicate count
- Generates unique filename
- Copies physical file
- Creates new Media record
- Flashes success message

`deleteMedia($mediaId)`
- Deletes physical file from storage
- Removes Media record
- Flashes success message

**Template Features:**
- Responsive grid (2 cols mobile, 4 tablet, 6 desktop)
- Image previews with file type icons
- Hover overlays with action buttons
- Session-based notifications
- Empty state with CTA
- Pagination links

**Actions:**
```blade
<a href="{{ route('lara-veil.media.edit', $item->id) }}">Edit</a>
<button wire:click="duplicate({{ $item->id }})">Duplicate</button>
<button wire:click="deleteMedia({{ $item->id }})" wire:confirm="...">Delete</button>
```

### 2. media-editor.volt

**Location:** `resources/views/components/media-editor.volt`

**Purpose:** Advanced media editing with image transformations

**Livewire Traits:**
- `WithFileUploads` - Handles file uploads

**Properties:**
```php
public Media $media;
public $replacement;          // File upload for replacement
public $width;               // Image width
public $height;              // Image height
public $ratio = true;        // Maintain aspect ratio
public $rotate = 0;          // Rotation angle (0/90/180/270)
public $flip = '';           // Flip direction (h/v)
public $brightness = 0;      // Brightness adjustment (-100 to +100)
public $contrast = 0;        // Contrast adjustment (-100 to +100)
public $blur = 0;            // Blur amount (0-100)
public $greyscale = false;   // Greyscale filter toggle
```

**Public Methods:**

`mount(Media $media)`
- Initializes with media instance
- Extracts image dimensions using `getimagesize()`
- Stores width/height for display

`save()`
- Validates replacement file if provided
- Handles image replacement workflow:
  - Deletes old file
  - Stores new file
  - Updates Media record
  - Re-extracts dimensions
- Currently logs transformation requests (full integration requires Intervention\Image)
- Flashes success message

`delete()`
- Deletes physical file
- Removes Media record
- Redirects to gallery index

**Template Sections:**

1. **Image Preview**
   - Full-size display with refresh timestamp
   - Aspect ratio scaling
   - Shadow effect

2. **File Details**
   - Filename display
   - MIME type
   - File size (formatted)
   - Dimensions (width × height)
   - Public URL with copy button

3. **Upload New Version**
   - File input for replacement
   - Warning message about immediate deletion
   - Automatic old file cleanup

4. **Resize Controls**
   - Width input
   - Height input
   - Aspect ratio toggle
   - Updates dimensions on change

5. **Transform Section**
   - Rotate dropdown (0/90/180/270°)
   - Flip radio buttons (horizontal/vertical)

6. **Adjustments Panel**
   - Brightness slider (-100 to +100)
   - Contrast slider (-100 to +100)
   - Blur slider (0-100)
   - Greyscale checkbox
   - Live value display for sliders

7. **Delete Section**
   - Permanent delete button (red variant)
   - Confirmation dialog
   - Danger styling

**Layout:**
- 3-column on desktop (preview 2 cols + tools 1 col)
- 2-column on tablet
- Single column on mobile

### 3. media-uploader.volt

**Location:** `resources/views/components/media-uploader.volt`

**Purpose:** File upload interface

**Livewire Traits:**
- `WithFileUploads` - Handles file uploads with progress

**Properties:**
```php
public $file;
public $uploading = false;
public $progress = 0;
```

**Public Methods:**

`save()`
- Validates file (required, max 10MB)
- Stores to `storage/app/public/media/`
- Creates Media record:
  - Original filename
  - MIME type detection
  - File size
  - Image dimensions (if image)
- Redirects to gallery index
- Flashes success message

**Template Features:**
- Simple file input
- Drag-and-drop support
- Upload progress bar (Alpine.js integration)
- Loading state management
- Back to library button
- Cancel option
- Error display

**Progress Tracking:**
- Uses Alpine.js for reactive progress updates
- Shows upload percentage
- "Uploading..." message with percentage

## Admin Views

All views located in `resources/views/admin/media/`

### index.blade.php
```blade
<x-layouts::app :title="'Media Library'">
    <livewire:media-gallery-grid />
</x-layouts::app>
```
Renders the media gallery grid with pagination.

### create.blade.php
```blade
<x-layouts::app :title="'Create Media'">
    <livewire:media-uploader />
</x-layouts::app>
```
Renders the file upload form.

### edit.blade.php
```blade
<x-layouts::app :title="'Edit Media'">
    <livewire:media-editor :$media />
</x-layouts::app>
```
Renders the media editor with transformations.

### upload.blade.php
Alias for create.blade.php for backward compatibility.

### show.blade.php
```blade
<x-layouts::app :title="'View Media'">
    <livewire:media-editor :$media />
</x-layouts::app>
```
Same as edit, shows media details and editing options.

## Styling

All components use Tailwind CSS 4.0 with custom Lara-Veil component classes:

**Key Classes:**
- `lara-veil-card` - Card container
- `lara-veil-card-header` - Card header with padding
- `lara-veil-card-body` - Card body content
- `lara-veil-button` - Base button style
- `lara-veil-button-primary` - Primary action button (blue)
- `lara-veil-button-danger` - Danger action button (red)
- `lara-veil-form-input` - Input field styling
- `lara-veil-form-label` - Label styling
- `lara-veil-form-group` - Form field wrapper
- `lara-veil-media-card` - Media thumbnail card

## Features

### Gallery View
✅ Responsive grid layout
✅ Thumbnail previews
✅ File type icons
✅ Hover action buttons
✅ Pagination (24 per page)
✅ Duplicate functionality
✅ Delete with confirmation
✅ Session flash messages
✅ Empty state
✅ Edit access

### Media Editor
✅ Image preview
✅ File details display
✅ Public URL copying
✅ Image replacement
✅ Resize controls
✅ Rotation
✅ Flip controls
✅ Brightness adjustment
✅ Contrast adjustment
✅ Blur effect
✅ Greyscale toggle
✅ Delete functionality

### Upload Form
✅ File validation
✅ Upload progress
✅ Error handling
✅ Back navigation
✅ Type detection
✅ Dimension extraction

## API Integration

Full REST API support:

```bash
# List media
GET /api/system/media

# Upload media
POST /api/system/media

# Get media
GET /api/system/media/{id}

# Update media
PUT /api/system/media/{id}

# Delete media
DELETE /api/system/media/{id}
```

## Console Commands

### media:cleanup
Removes orphaned files without database records.

### media:prune
Deletes media older than specified days (default: 30).

### media:info
Displays media library statistics and recent uploads.

### media:diagnose
System health check with 7-point diagnostic report.

## Best Practices

### File Organization
- Store media in `storage/app/public/media/` (symlinked)
- Access via `asset('storage/...')` routes
- Use `disk: 'public'` for cloud migration compatibility

### Database Relationships
```php
// Define in models
use Scapteinc\LaraVeil\Traits\HasMedia;

class Post extends Model {
    use HasMedia;
}

// Attach media
$post->media()->attach($media->id, ['collection' => 'featured']);
```

### Metadata Storage
```php
$media->update([
    'metadata' => [
        'alt_text' => 'Description',
        'caption' => 'Image caption',
        'credits' => 'Photographer name',
        'tags' => ['tag1', 'tag2']
    ]
]);
```

### File Validation
```php
// In forms/requests
'file' => 'required|file|max:10240|mimes:jpeg,png,gif,pdf'
```

## Future Enhancements

Potential improvements for v2.0:

### Image Processing
- Integration with Intervention\Image for transformations
- Real-time image preview with filters
- Automatic WebP conversion
- Image compression on upload
- Bulk format conversion

### Gallery Features
- Search and filtering
- Bulk selection and actions
- Drag-and-drop reordering
- Tagging system
- Collections/albums
- Favorites

### Admin Features
- Usage statistics
- Storage quota management
- Automatic cleanup policies
- Image optimization scheduling
- CDN integration

### API Features
- Signed URLs
- Rate limiting
- Access control lists
- Webhook support
- Import/export

## Troubleshooting

### Images Not Displaying
1. Check `storage/app/public/` directory exists
2. Verify symlink: `php artisan storage:link`
3. Confirm file permissions: `755` for directories, `644` for files
4. Check `APP_URL` in `.env`

### Upload Failures
1. Verify `storage/app/public/media/` is writable
2. Check PHP `upload_max_filesize` and `post_max_size`
3. Ensure Livewire file upload is configured
4. Check available disk space

### Dimension Detection Issues
1. Ensure `php-gd` or `php-imagick` is installed
2. Check image file integrity
3. Verify MIME type is correct

### Permission Errors
1. Run: `php artisan storage:link`
2. Set permissions: `sudo chown -R www-data:www-data storage/`
3. Check web server user matches

## Performance Optimization

### Database
```php
// Use eager loading
$media = Media::with('model')->paginate();

// Index frequently queried columns
$table->index('media_type');
$table->index('collection_name');
```

### File Storage
```php
// Use public disk for web access
'disks' => [
    'public' => [
        'driver' => 'local',
        'path' => 'storage/app/public',
        'url' => env('APP_URL') . '/storage',
    ]
]
```

### Caching
```php
// Cache media queries
$media = Cache::remember('media.all', 3600, function() {
    return Media::all();
});
```

## Security Considerations

### File Validation
- Validate MIME types server-side
- Scan uploads with virus scanner (ClamAV)
- Restrict executable file types
- Implement file size limits

### Access Control
```php
// Protect routes with policy
Route::middleware('auth:api')->group(function() {
    // API routes
});

// Use authorization policy
abort_unless(auth()->user()->can('manage-media'), 403);
```

### Storage
- Store outside webroot when possible
- Use signed URLs for downloads
- Implement download rate limiting
- Log file access

## Reference

### Database Columns
- `name` - Original filename for display
- `path` - Relative storage path for asset() calls
- `media_type` - Category ('image', 'file', etc.)
- `mime_type` - MIME type for client hints
- `file_size` - Bytes for quota tracking
- `width/height` - Pixels for display
- `disk` - Storage disk for multi-disk support
- `model_type/id` - Polymorphic relationships
- `collection_name` - Organization grouping
- `metadata` - JSON for custom attributes

### Volt Component Lifecycle
1. `mount()` - Initialize component
2. `render()` - Render template
3. `wire:click/wire:model` - User interactions
4. Component re-renders on state change

### File Storage
- Default: `storage/app/public/media/{filename}`
- URL: `asset('storage/media/{filename}')`
- Symlink required: `php artisan storage:link`
- Disk: 'public' (configured in filesystems.php)
