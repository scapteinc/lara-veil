# Lara-Veil Admin Interface - Quick Reference

## Three-System Management Pattern

All three systems follow the same architecture for consistency and familiarity.

### Access Points

| System | URL | Component | Purpose |
|--------|-----|-----------|---------|
| **Plugins** | `/admin/plugins` | `plugins-gallery.volt` | Browse & manage plugins |
| **Themes** | `/admin/themes` | `themes-gallery.volt` | Browse & manage themes |
| **Media** | `/admin/media` | `media-gallery-grid.volt` | Browse & manage files |

## Admin Panel Features

### Plugin Gallery (`/admin/plugins`)
```
🎨 Grid Layout: 3 columns (desktop) → 2 columns (tablet) → 1 column (mobile)
📊 Pagination: 12 items per page
🔘 Status Badges: Active (green) | Inactive (gray) | Broken (red)
⚡ Quick Actions:
  • Settings - Configure plugin options
  • Activate/Deactivate - Toggle status
  • Delete - Remove with confirmation
📝 Card Display:
  • Plugin name and namespace
  • Description (truncated)
  • Version number
  • Author name
  • Installation date
```

**Features:**
- ✅ Create new plugin
- ✅ Manage plugin settings (JSON metadata)
- ✅ Activate/deactivate plugins
- ✅ Delete plugins
- ✅ View metadata and version info
- ✅ Session notifications on success/error
- ✅ Empty state with install CTA

### Theme Gallery (`/admin/themes`)
```
🎨 Grid Layout: 3 columns (desktop) → 2 columns (tablet) → 1 column (mobile)
📸 Thumbnail Display: Custom images with fallback placeholder
✨ Active Indicator: Green badge overlay on active theme
📊 Pagination: 12 items per page
🔘 Status Display: Active or Inactive
⚡ Quick Actions:
  • Settings - Configure theme options
  • Activate - Make theme live (one active at a time)
  • Delete - Remove with cascading child deletion
📝 Card Display:
  • Theme name and slug
  • Description (truncated)
  • Version number
  • Author name
  • Child theme count
  • Installation date
```

**Features:**
- ✅ Parent/child theme support
- ✅ Manage theme settings (JSON metadata)
- ✅ View theme details (name, description, version)
- ✅ Thumbnail image support
- ✅ Activate/deactivate themes (single active)
- ✅ Delete themes with child cascading
- ✅ Session notifications on success/error
- ✅ Empty state with install CTA

### Media Gallery (`/admin/media`)
```
🎨 Grid Layout: 2 columns (mobile) → 4 columns (tablet) → 6 columns (desktop)
🖼️  Thumbnail Display: Image previews with file type icons
📊 Pagination: 24 items per page
⚡ Quick Actions:
  • Edit - Open image editor with transformations
  • Duplicate - Create copy with collision detection
  • Delete - Remove with confirmation
📝 File Info:
  • Filename (truncated with tooltip)
  • File size (formatted)
  • Media type indicator
```

**Features:**
- ✅ Upload new media files
- ✅ View image metadata (dimensions, size, MIME type)
- ✅ Image transformations (rotate, flip, brightness, contrast, blur, greyscale)
- ✅ Replace media with new version
- ✅ Duplicate files with smart naming
- ✅ Delete files permanently
- ✅ Copy public URLs
- ✅ Session notifications on success/error
- ✅ Empty state with upload CTA

## Create/Install Pages

### Install Plugin (`/admin/plugins/create`)
```
📋 Form Fields:
  • Plugin Name (required) - Display name
  • Namespace (required) - PHP namespace
  • Version (required) - Semantic version
  • Author (optional) - Creator name
  • Description (optional) - Plugin overview
```

### Install Theme (`/admin/themes/create`)
Similar pattern as plugins (if created).

### Upload Media (`/admin/media/create`)
```
📋 Form:
  • File input (drag & drop support)
  • File validation (max 10MB)
  • Upload progress bar
  • Back to library link
```

## Edit/Settings Pages

### Plugin Settings (`/admin/plugins/{id}/edit`)
```
📋 Display:
  • Plugin name and namespace (read-only)
  • Version (read-only)
  • Author (read-only)
  • Description (read-only)

📝 Editor:
  • Custom metadata (JSON format)
  • Save button
  • Cancel button
```

### Theme Settings (`/admin/themes/{id}`)
```
📋 Sidebar:
  • Thumbnail image
  • Theme name and slug
  • Version, author, status
  • Installation date

📝 Main Content:
  • Full description
  • Custom metadata editor (JSON)
  • Child themes list (if any)
  • Save button
```

### Media Editor (`/admin/media/{id}/edit`)
```
📸 Image Preview:
  • Full-size display
  • Auto-refresh timestamp
  • Aspect ratio scaling

📋 File Details:
  • Filename
  • MIME type
  • File size (formatted)
  • Dimensions (width × height)
  • Public URL (with copy button)

⚙️  Tools (Right Column):
  • Upload new version
  • Resize (with aspect ratio toggle)
  • Rotate (0/90/180/270°)
  • Flip (horizontal/vertical)
  • Brightness slider (-100 to +100)
  • Contrast slider (-100 to +100)
  • Blur slider (0-100)
  • Greyscale toggle
  • Delete button (red)
```

## Database Schema Reference

### Plugins Table
```sql
id, name, namespace, description, version, author, 
status, settings, metadata, created_at, updated_at
Index: status
```

### Themes Table
```sql
id, name, slug, description, version, author, 
thumbnail_path, parent_id, is_active, 
settings, metadata, created_at, updated_at
Indexes: is_active, parent_id
```

### Media Table
```sql
id, name, path, media_type, mime_type, file_size, 
width, height, disk, model_type, model_id, 
collection_name, metadata, created_at, updated_at
Indexes: media_type, collection_name
```

## Console Commands

### Plugin Commands
```bash
php artisan plugin:list              # List all plugins
php artisan plugin:install NAME      # Install plugin
php artisan plugin:activate NAME     # Activate plugin
php artisan plugin:deactivate NAME   # Deactivate plugin
php artisan plugin:uninstall NAME    # Remove plugin
```

### Theme Commands
```bash
php artisan theme:list               # List all themes
php artisan theme:activate SLUG      # Activate theme
php artisan theme:install SLUG       # Install theme
```

### Media Commands
```bash
php artisan media:info               # Show statistics
php artisan media:cleanup            # Remove orphaned files
php artisan media:prune --days=30    # Delete old media
php artisan media:diagnose           # System health check
```

## Livewire Component Methods

### plugins-gallery.volt
- `with()` - Returns paginated plugins
- `activate($id)` - Set plugin active
- `deactivate($id)` - Set plugin inactive
- `delete($id)` - Remove plugin

### themes-gallery.volt
- `with()` - Returns paginated themes (parent only)
- `activate($id)` - Set theme active (deactivates others)
- `delete($id)` - Remove theme and children

### media-gallery-grid.volt
- `with()` - Returns paginated media
- `duplicate($id)` - Create file copy
- `deleteMedia($id)` - Remove file

### media-editor.volt
- `mount(Media $media)` - Initialize editor
- `save()` - Save changes and replacements
- `delete()` - Remove media permanently

### media-uploader.volt
- `save()` - Upload and create media record

## Styling Classes

All components use Tailwind CSS with Lara-Veil classes:

```css
.lara-veil-card              /* Card container */
.lara-veil-card-header       /* Card header section */
.lara-veil-card-body         /* Card content area */
.lara-veil-button            /* Base button style */
.lara-veil-button-primary    /* Primary action (blue) */
.lara-veil-button-danger     /* Danger action (red) */
.lara-veil-form-input        /* Text input */
.lara-veil-form-label        /* Form label */
.lara-veil-form-group        /* Form field wrapper */
.lara-veil-media-card        /* Media thumbnail card */
```

## Response Codes & Messages

All operations use session flash messages:

### Success Messages
```
✅ "Plugin installed successfully."
✅ "Plugin settings saved successfully."
✅ "Plugin activated successfully."
✅ "Plugin deactivated successfully."
✅ "Plugin deleted successfully."
✅ "Theme activated successfully."
✅ "Theme deleted successfully."
✅ "File uploaded successfully."
✅ "Media duplicated successfully."
✅ "Media deleted successfully."
```

### Error Handling
```
❌ Validation errors displayed inline
❌ File upload errors shown in notification
❌ Database errors caught and reported
❌ Permission errors blocked with auth middleware
```

## Security Features

- ✅ Authentication required (auth middleware)
- ✅ CSRF protection on all forms
- ✅ Form validation on server & client
- ✅ Confirmation dialogs before deletion
- ✅ Unique constraints on database
- ✅ Input sanitization in models
- ✅ Livewire method authorization

## Mobile Responsiveness

All galleries adapt to screen size:

```
📱 Mobile:    1 column grid
📱 Tablet:    2-3 columns
🖥️  Desktop:  3-6 columns
```

## Search & Filtering (Future)

Currently available:
- ✅ Pagination
- ✅ Direct access via URLs
- ✅ Status indicators

Planned enhancements:
- 🔲 Search by name/namespace
- 🔲 Filter by status
- 🔲 Filter by type
- 🔲 Sort by date/name/status
- 🔲 Bulk operations

## Performance Metrics

- ⚡ Pagination: Prevents loading all items at once
- ⚡ Lazy loading: Images load on demand
- ⚡ Database indexes: Fast status/type queries
- ⚡ Caching ready: Can cache gallery queries
- ⚡ Efficient queries: Uses eager loading where needed

## File Organization

```
lara-veil/
├── src/
│   ├── Models/
│   │   ├── Plugin.php          (Enhanced)
│   │   ├── Theme.php           (Enhanced)
│   │   └── Media.php           (Enhanced)
│   ├── Database/Migrations/
│   │   ├── *_create_plugins_table.php    (Enhanced)
│   │   ├── *_create_themes_table.php     (Enhanced)
│   │   └── *_create_media_table.php      (Enhanced)
│   ├── Http/Controllers/Admin/
│   │   ├── PluginManagementController.php (Updated)
│   │   ├── ThemeManagementController.php  (Updated)
│   │   └── MediaManagementController.php  (Updated)
│   └── Routes/
│       └── web.php             (Existing routes)
└── resources/
    └── views/
        ├── admin/
        │   ├── plugins/
        │   │   ├── index.blade.php   (Updated)
        │   │   ├── create.blade.php  (Updated)
        │   │   └── edit.blade.php    (New)
        │   ├── themes/
        │   │   ├── index.blade.php   (Updated)
        │   │   └── show.blade.php    (New)
        │   └── media/
        │       ├── index.blade.php   (Updated)
        │       ├── create.blade.php  (New)
        │       ├── edit.blade.php    (New)
        │       └── show.blade.php    (New)
        └── components/
            ├── plugins-gallery.volt  (New)
            ├── themes-gallery.volt   (New)
            ├── media-gallery-grid.volt
            ├── media-editor.volt
            └── media-uploader.volt
```

## Documentation Files

- 📖 [README.md](README.md) - Main documentation
- 📖 [PLUGIN_THEME_MANAGEMENT.md](PLUGIN_THEME_MANAGEMENT.md) - Detailed guide
- 📖 [MEDIA_MANAGEMENT.md](MEDIA_MANAGEMENT.md) - Media system reference
- 📖 [MEDIA_QUICK_START.md](MEDIA_QUICK_START.md) - Quick start guide
- 📖 [PLUGIN_THEME_IMPLEMENTATION.md](PLUGIN_THEME_IMPLEMENTATION.md) - Implementation details
