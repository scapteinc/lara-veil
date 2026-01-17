# Plugin Management Implementation Guide

## Overview

The Lara-Veil package includes a complete plugin management system with an advanced admin interface powered by Laravel Volt components, matching the media management pattern.

## Database Schema

### Plugins Table

```sql
CREATE TABLE plugins (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,           -- Display name
    namespace VARCHAR(255) UNIQUE NOT NULL,      -- PHP namespace
    description TEXT NULLABLE,                   -- Plugin description
    version VARCHAR(255) NOT NULL,               -- Semantic version
    author VARCHAR(255) NULLABLE,                -- Author name
    status ENUM('active', 'inactive', 'broken')  -- Current status
    settings JSON NULLABLE,                      -- Plugin configuration
    metadata JSON NULLABLE,                      -- Custom metadata
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX (status)
);
```

## Routes

### Plugin Management Routes

Base path: `/admin/plugins`

| Method | Route | Controller | Name | Purpose |
|--------|-------|-----------|------|---------|
| GET | `/` | index | `lara-veil.plugins.index` | List all plugins (Gallery grid) |
| GET | `/create` | create | `lara-veil.plugins.create` | Show plugin install form |
| POST | `/` | store | `lara-veil.plugins.store` | Create new plugin |
| GET | `/{plugin}/edit` | edit | `lara-veil.plugins.edit` | Edit plugin settings |
| PUT | `/{plugin}` | update | `lara-veil.plugins.update` | Update plugin |
| DELETE | `/{plugin}` | destroy | `lara-veil.plugins.destroy` | Delete plugin |
| POST | `/{plugin}/activate` | activate | `lara-veil.plugins.activate` | Activate plugin |
| POST | `/{plugin}/deactivate` | deactivate | `lara-veil.plugins.deactivate` | Deactivate plugin |

All routes protected by `auth` middleware.

## Volt Component: plugins-gallery.volt

**Location:** `resources/views/components/plugins-gallery.volt`

**Purpose:** Gallery view of all installed plugins with management actions

**Livewire Traits:**
- `WithPagination` - Handles pagination (12 per page)

**Public Methods:**

`with()`
- Returns paginated plugins list

`activate($id)`
- Changes plugin status to 'active'
- Flashes success message

`deactivate($id)`
- Changes plugin status to 'inactive'
- Flashes success message

`delete($id)`
- Removes plugin from database
- Flashes success message

**Template Features:**
- Responsive grid layout (1 col mobile, 2 col tablet, 3 col desktop)
- Plugin cards with metadata
- Status badge with color indicator
- Quick action buttons
- Pagination links
- Empty state

**Cards Display:**
- Name and namespace
- Description (truncated)
- Status badge (active/inactive/broken)
- Version number
- Author name
- Installation date
- Action buttons (Settings, Activate/Deactivate, Delete)

## Models

### Plugin Model

Location: `src/Models/Plugin.php`

**Fillable Attributes:**
```php
[
    'name', 'namespace', 'description', 'version', 'author',
    'status', 'settings', 'metadata'
]
```

**Accessors:**
- `status_color` - Returns color code based on status
- `status_label` - Returns formatted status string

**Methods:**
- `isActive()` - Boolean check
- `isBroken()` - Boolean check

## Admin Views

All views located in `resources/views/admin/plugins/`

### index.blade.php
```blade
<x-layouts::app :title="'Plugins'">
    <livewire:plugins-gallery />
</x-layouts::app>
```

### create.blade.php
Form for installing new plugins with fields:
- Plugin Name (required)
- Namespace (required)
- Version (required)
- Author
- Description

### edit.blade.php
Settings editor showing:
- Plugin info (namespace, version, author, description)
- Custom metadata editor (JSON)
- Save/cancel buttons

## Features

✅ Grid gallery layout with responsive design
✅ Plugin card display with metadata
✅ Activation/deactivation
✅ Plugin deletion
✅ Version tracking
✅ Author information
✅ Description support
✅ Settings management
✅ Session flash messages
✅ Empty state
✅ Pagination

---

# Theme Management Implementation Guide

## Overview

Complete theme management system with Volt components for browsing, activating, and managing themes.

## Database Schema

### Themes Table

```sql
CREATE TABLE themes (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,           -- Display name
    slug VARCHAR(255) UNIQUE NOT NULL,           -- URL slug
    description TEXT NULLABLE,                   -- Theme description
    version VARCHAR(255) NULLABLE,               -- Semantic version
    author VARCHAR(255) NULLABLE,                -- Author name
    thumbnail_path VARCHAR(255) NULLABLE,        -- Thumbnail image path
    parent_id BIGINT UNSIGNED NULLABLE,          -- Parent theme (child theme support)
    is_active BOOLEAN DEFAULT FALSE,             -- Active status
    settings JSON NULLABLE,                      -- Theme configuration
    metadata JSON NULLABLE,                      -- Custom metadata
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES themes(id) ON DELETE CASCADE,
    INDEX (is_active),
    INDEX (parent_id)
);
```

## Routes

### Theme Management Routes

Base path: `/admin/themes`

| Method | Route | Controller | Name | Purpose |
|--------|-------|-----------|------|---------|
| GET | `/` | index | `lara-veil.themes.index` | List all themes (Gallery grid) |
| GET | `/{theme}` | show | `lara-veil.themes.show` | View theme details |
| POST | `/{theme}/activate` | activate | `lara-veil.themes.activate` | Activate theme |
| GET | `/{theme}/customize` | customize | `lara-veil.themes.customize` | Customize theme |
| PUT | `/{theme}/customize` | updateCustomization | `lara-veil.themes.customize.update` | Save customization |
| GET | `/{theme}/preview` | preview | `lara-veil.themes.preview` | Preview theme |
| GET | `/{theme}/settings` | settings | `lara-veil.themes.settings` | Theme settings |
| PUT | `/{theme}/settings` | updateSettings | `lara-veil.themes.settings.update` | Save settings |

All routes protected by `auth` middleware.

## Volt Component: themes-gallery.volt

**Location:** `resources/views/components/themes-gallery.volt`

**Purpose:** Gallery view of all installed themes with management actions

**Livewire Traits:**
- `WithPagination` - Handles pagination (12 per page)

**Public Methods:**

`with()`
- Returns paginated themes (parent themes only)
- Eager loads child themes

`activate($id)`
- Deactivates all themes
- Activates selected theme
- Flashes success message

`delete($id)`
- Deletes theme and all child themes
- Flashes success message

**Template Features:**
- Responsive grid layout (1 col mobile, 2 col tablet, 3 col desktop)
- Theme thumbnails with fallback
- Active badge overlay
- Description (truncated)
- Metadata display
- Child theme count
- Installation date
- Action buttons
- Pagination links
- Empty state

**Cards Display:**
- Thumbnail image with placeholder
- Active badge overlay (green checkmark)
- Name and slug
- Description
- Version number
- Author name
- Child theme count
- Installation date
- Action buttons (Settings, Activate/Disabled, Delete)

## Models

### Theme Model

Location: `src/Models/Theme.php`

**Fillable Attributes:**
```php
[
    'name', 'slug', 'description', 'version', 'author',
    'thumbnail_path', 'parent_id', 'is_active',
    'settings', 'metadata'
]
```

**Relationships:**
- `parent()` - Belongs to parent theme
- `children()` - Has many child themes

**Accessors:**
- `status_label` - Returns 'Active' or 'Inactive'
- `thumbnail_url` - Returns image URL or placeholder

**Methods:**
- `isChild()` - Check if has parent theme

## Admin Views

All views located in `resources/views/admin/themes/`

### index.blade.php
```blade
<x-layouts::app :title="'Themes'">
    <livewire:themes-gallery />
</x-layouts::app>
```

### show.blade.php
Comprehensive theme details page with:
- Thumbnail image
- Theme metadata sidebar
- Description
- Custom settings editor (JSON)
- Child themes list (if any)

## Features

✅ Grid gallery layout with thumbnails
✅ Responsive design
✅ Active theme indicator
✅ Parent/child theme support
✅ Theme activation (single active at a time)
✅ Theme deletion with cascading
✅ Version tracking
✅ Author information
✅ Description support
✅ Thumbnail support with placeholder
✅ Settings management
✅ Child theme display
✅ Session flash messages
✅ Empty state
✅ Pagination

## Child Theme Support

Themes can inherit from parent themes:

```php
// Create child theme
$childTheme = Theme::create([
    'name' => 'Parent Theme - Dark Variant',
    'slug' => 'parent-dark',
    'parent_id' => $parentTheme->id,
]);

// Access parent
$parent = $childTheme->parent;

// List all children
$children = $parentTheme->children;
```

## Integration with Controllers

### PluginManagementController

The `index()` method now returns a view that renders the `plugins-gallery` Volt component. All plugin list operations are handled by the component's Livewire methods.

```php
public function index()
{
    return view('lara-veil::admin.plugins.index');
}
```

### ThemeManagementController

The `index()` method returns a view that renders the `themes-gallery` Volt component.

```php
public function index()
{
    return view('lara-veil::admin.themes.index');
}
```

## Unified Management Pattern

Both plugin and theme management follow the same pattern:

1. **Gallery/List View** - Volt component displays grid of items
2. **Create/Install View** - Form to add new item
3. **Edit/Settings View** - Manage item metadata and settings
4. **Show/Details View** - View full item details

This consistent pattern provides:
- Unified user experience
- Easier to navigate and use
- Predictable interactions
- Responsive across devices
- Modern UI with Tailwind CSS

## Console Commands Available

### Plugin Commands
```bash
php artisan plugin:list
php artisan plugin:install vendor/plugin
php artisan plugin:activate vendor/plugin
php artisan plugin:deactivate vendor/plugin
php artisan plugin:uninstall vendor/plugin
```

### Theme Commands
```bash
php artisan theme:list
php artisan theme:activate slug
php artisan theme:install slug
```

## Best Practices

### Plugin Installation

1. Fill in required fields (name, namespace, version)
2. Add author and description for clarity
3. Click "Install Plugin"
4. Configure settings from edit page
5. Activate plugin to enable functionality

### Theme Selection

1. Browse available themes in gallery
2. View theme details including child themes
3. Click "Activate" to make theme live
4. Customize settings from theme details page
5. Only one theme can be active at a time

### Metadata Management

Both plugins and themes support custom JSON metadata for extensions:

```php
$plugin->update([
    'metadata' => [
        'requires_api' => true,
        'database_tables' => ['custom_table'],
        'config_keys' => ['plugin_setting'],
    ]
]);

$theme->update([
    'metadata' => [
        'color_scheme' => 'dark',
        'responsive' => true,
        'supports' => ['rtl', 'widgets'],
    ]
]);
```

## Security Considerations

- All routes protected by `auth` middleware
- Form validation on create/update
- Confirmation dialogs before deletion
- CSRF protection on all forms
- Unique constraints on name/namespace/slug

## Future Enhancements

### Plugins
- Search and filtering
- Status filtering (active/inactive/broken)
- Bulk operations
- Plugin dependencies
- Automatic update checking
- Plugin marketplace integration

### Themes
- Search and filtering
- Thumbnail upload
- Live preview before activation
- Customization UI builder
- Color picker for theme colors
- Font selection interface
- Theme marketplace integration

## Troubleshooting

### Plugin Not Appearing

1. Verify plugin exists in database
2. Check status is not 'broken'
3. Clear any caches

### Theme Not Activating

1. Ensure no other theme is active (only one allowed)
2. Check theme has valid slug
3. Verify child themes don't conflict

### Metadata Not Saving

1. Ensure JSON is valid
2. Check textarea values are properly escaped
3. Verify model has metadata in fillable array
