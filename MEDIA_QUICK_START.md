# Media Management Quick Start

## Installation & Setup

### 1. Publish Package Assets

```bash
php artisan vendor:publish --tag=lara-veil-assets
```

### 2. Run Migrations

```bash
php artisan migrate
```

This creates the `media` table with all required columns.

### 3. Create Storage Symlink

```bash
php artisan storage:link
```

This creates a symlink from `public/storage` to `storage/app/public`, allowing web access to uploaded files.

## Basic Usage

### Access Admin Panel

Navigate to: `http://yourapp.local/admin/media`

All features require authentication (protected by `auth` middleware).

### Upload Files

1. Click **Upload Media** button
2. Select file from computer (max 10MB)
3. Monitor upload progress
4. Click **Upload Media** to confirm

**Supported Features:**
- Automatic MIME type detection
- Image dimension extraction
- File size calculation
- Type categorization (image vs file)

### Browse Gallery

The gallery displays all uploaded media in a responsive grid:

**Desktop:** 6 columns
**Tablet:** 4 columns  
**Mobile:** 2 columns

**Pagination:** 24 items per page

### Edit Media

Click **Edit** on any media item to access the editor:

**File Details:**
- View original filename
- Check MIME type
- See file size (formatted)
- View image dimensions
- Copy public URL

**Image Replacement:**
- Upload new version
- Old file automatically deleted
- Dimensions re-extracted

**Image Transformations:**
- **Rotate:** 0°, 90°, 180°, 270°
- **Flip:** Horizontal or Vertical
- **Brightness:** -100 to +100
- **Contrast:** -100 to +100
- **Blur:** 0-100
- **Greyscale:** Toggle filter on/off

### Duplicate Media

Click **Duplicate** on media item:
- Creates copy of file
- Auto-detects duplicate count
- Generates unique filename
- Preserves all metadata
- Useful for versioning

### Delete Media

Click **Delete** on media item:
1. Confirm deletion in dialog
2. File removed from storage
3. Database record deleted
4. Cannot be undone

## CLI Management

### List All Media

```bash
php artisan media:info
```

Shows:
- Total number of files
- Total storage used
- Recent uploads
- File type breakdown

### Cleanup Orphaned Files

```bash
php artisan media:cleanup
```

Removes files in storage with no database record.

### Prune Old Media

```bash
php artisan media:prune --days=30
```

Deletes media older than specified days.

### System Diagnostics

```bash
php artisan media:diagnose
```

Checks:
- Storage directory writability
- File permissions
- Symlink status
- Disk space
- Database connectivity
- Image library (GD/Imagick)
- Path configuration

## Database Operations

### Direct Model Usage

```php
use Scapteinc\LaraVeil\Models\Media;

// Get all media
$all = Media::all();

// Find specific media
$media = Media::find($id);

// Get all images
$images = Media::where('media_type', 'image')->get();

// Get latest uploads
$recent = Media::latest()->take(10)->get();

// Filter by MIME type
$pdfs = Media::where('mime_type', 'application/pdf')->get();
```

### Create Media Programmatically

```php
$media = Media::create([
    'name' => 'photo.jpg',
    'path' => 'media/photo.jpg',
    'media_type' => 'image',
    'mime_type' => 'image/jpeg',
    'file_size' => 102400,
    'width' => 1920,
    'height' => 1080,
]);
```

### Update Media

```php
$media->update([
    'name' => 'updated-name.jpg',
    'metadata' => [
        'alt' => 'Alternative text',
        'tags' => ['photo', 'landscape']
    ]
]);
```

### Delete Media

```php
// Delete physical file
Storage::disk('public')->delete($media->path);

// Delete database record
$media->delete();
```

## Relationships

### Polymorphic Relationships

Attach media to any model:

```php
// In your model
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model {
    public function media() {
        return $this->morphMany(Media::class, 'model');
    }
}

// Attach media
$post->media()->create([
    'name' => 'featured.jpg',
    'path' => 'media/featured.jpg',
    'media_type' => 'image',
    'collection_name' => 'featured-images',
]);

// Retrieve related media
$featured = $post->media()
    ->where('collection_name', 'featured-images')
    ->first();
```

### Collections

Organize media into collections:

```php
// Create collection groups
$post->media()->create([
    'name' => 'image1.jpg',
    'collection_name' => 'gallery',
    // ...
]);

// Query by collection
$gallery = $post->media()
    ->where('collection_name', 'gallery')
    ->get();
```

## File Access

### Public URLs

```php
// In template
{{ asset('storage/' . $media->path) }}

// Via model attribute
{{ $media->public_url }}

// Copy from UI
Click "Copy" in File Details section
```

### Download Files

```php
// Direct download
return response()->download(storage_path('app/' . $media->path));

// Inline display
return response()->file(storage_path('app/' . $media->path));
```

## Storage Configuration

### File Location

- **Default disk:** public
- **Storage path:** `storage/app/public/media/`
- **Web URL:** `http://yourapp.local/storage/media/...`
- **Access method:** `asset('storage/...')`

### Configure Disk

In `config/filesystems.php`:

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'path' => 'storage/app/public',
        'url' => env('APP_URL') . '/storage',
        'visibility' => 'public',
    ],
]
```

## Troubleshooting

### Files Not Accessible

**Problem:** 404 error when accessing media

**Solution:**
```bash
# Recreate symlink
php artisan storage:link

# Fix permissions
sudo chown -R www-data:www-data storage/app/public
sudo chmod -R 755 storage/app/public
```

### Upload Failures

**Problem:** Upload fails silently

**Solution:**
```bash
# Check Livewire config
php artisan livewire:publish --config

# Verify temp directory
php -r "echo sys_get_temp_dir();"

# Check PHP limits in php.ini
upload_max_filesize = 10M
post_max_size = 10M
```

### Image Dimensions Not Detected

**Problem:** width/height are null

**Solution:**
```bash
# Check GD extension
php -m | grep GD

# or for Imagick
php -m | grep imagick

# Install if missing
sudo apt-get install php-gd
sudo systemctl restart apache2
```

### Symlink Issues on Windows

**Problem:** Storage symlink doesn't work on Windows

**Solution:**
```bash
# Use directory junction instead
mklink /J public\storage storage\app\public

# Or disable symlink requirement
# Use absolute path in asset() calls
```

## Performance Tips

### Optimize Image Upload

```php
// Validate file size early
'file' => 'required|file|max:5120' // 5MB limit

// Use WebP when possible
// Compress images automatically
// Generate thumbnails on upload
```

### Database Query Optimization

```php
// Use eager loading
$posts = Post::with('media')->paginate();

// Limit columns
$media = Media::select('id', 'name', 'path')->get();

// Add indexes
Schema::table('media', function (Blueprint $table) {
    $table->index('media_type');
    $table->index('collection_name');
});
```

### Caching

```php
// Cache media queries
$media = Cache::remember('media.gallery', 3600, function() {
    return Media::where('media_type', 'image')->get();
});

// Clear cache on upload
Cache::forget('media.gallery');
```

## Advanced Topics

### Custom Metadata

Store any JSON data:

```php
$media->update([
    'metadata' => [
        'alt_text' => 'Photo description',
        'caption' => 'Image caption',
        'credits' => 'Photographer',
        'location' => 'GPS coordinates',
        'camera' => 'Camera model',
        'tags' => ['tag1', 'tag2'],
        'custom_field' => 'custom value'
    ]
]);

// Access metadata
$alt = $media->metadata['alt_text'] ?? '';
```

### Bulk Operations

```php
// Delete multiple files
Media::whereIn('id', $ids)->each(function($media) {
    Storage::disk('public')->delete($media->path);
    $media->delete();
});

// Update multiple records
Media::where('media_type', 'image')
    ->update(['metadata' => DB::raw("JSON_SET(metadata, '$.processed', true)")]);
```

### Event Hooks

```php
// Listen for media events
use Scapteinc\LaraVeil\Models\Media;

Media::created(function($media) {
    // Log upload
    // Send notifications
    // Generate thumbnails
});

Media::deleted(function($media) {
    // Cleanup related data
    // Update statistics
    // Trigger workflows
});
```

## API Integration

### REST Endpoints

```bash
# List media
curl -H "Authorization: Bearer {token}" \
  http://api.yourapp.local/api/system/media

# Upload media
curl -F "file=@image.jpg" \
  -H "Authorization: Bearer {token}" \
  http://api.yourapp.local/api/system/media

# Get specific media
curl -H "Authorization: Bearer {token}" \
  http://api.yourapp.local/api/system/media/{id}

# Delete media
curl -X DELETE \
  -H "Authorization: Bearer {token}" \
  http://api.yourapp.local/api/system/media/{id}
```

## Support

For detailed documentation, see:
- [MEDIA_MANAGEMENT.md](MEDIA_MANAGEMENT.md) - Complete implementation guide
- [README.md](README.md) - Full package documentation
- [CONTRIBUTING.md](CONTRIBUTING.md) - Development guidelines
