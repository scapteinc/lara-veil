# Lara-Veil

A WordPress-like extensibility system for Laravel applications that allows dynamic loading of plugins and themes without modifying core code. Build modular, extensible applications with a robust plugin ecosystem, theme support, and advanced media processing.

## 🎯 Features

✅ **Dynamic Plugin System** - Install, activate, deactivate, and uninstall plugins at runtime  
✅ **Theme Management** - Switch themes dynamically with parent/child inheritance support  
✅ **Hook System** - WordPress-style Action/Filter system for extensibility  
✅ **Media Forge Service** - Advanced media processing with image manipulation, compression, and format conversion  
✅ **Reactive Admin Panel** - Laravel Volt components with Livewire interactivity  
✅ **RESTful API** - Full API endpoints for plugins, themes, media, and system management  
✅ **Console Commands** - 12 artisan commands for plugin/theme/media management  
✅ **Service Management** - Automatic service provider registration for plugins/themes  
✅ **Database Integration** - Full Eloquent models with migrations  
✅ **Caching System** - Performance-optimized with configurable caching  
✅ **Authentication** - Built-in admin authentication middleware  
✅ **Precompiled Assets** - Ready-to-use CSS and JavaScript with Tailwind styling

## 📦 System Architecture

### Core Components

```
src/
├── Core/
│   ├── PluginManager.php      # Plugin lifecycle management
│   ├── ThemeManager.php       # Theme lifecycle management
│   ├── HookSystem.php         # Action/Filter system
│   └── AssetManager.php       # Asset management
├── Services/Vrm/
│   └── MediaForgeService.php  # Advanced media processing
├── Models/
│   ├── Plugin.php             # Plugin Eloquent model
│   ├── Theme.php              # Theme Eloquent model
│   └── Media.php              # Media Eloquent model
├── Http/
│   ├── Controllers/           # API controllers
│   └── Controllers/Admin/     # Admin controllers
├── Console/Commands/          # 12 artisan commands
├── routes/
│   ├── web.php               # Admin web routes
│   └── api.php               # RESTful API routes
├── database/migrations/       # Database schemas
└── resources/
    ├── views/                # Blade templates
    ├── components/           # Volt reactive components
    ├── css/lara-veil.css    # Tailwind styles
    └── js/lara-veil.js      # Admin JavaScript
```

## 🚀 Installation

### For Production (After Publishing to Packagist)

Install via Composer:

```bash
composer require scapteinc/lara-veil
```

Publish assets:

```bash
php artisan vendor:publish --tag=lara-veil-assets
```

### For Development (Local/Path Repository)

If developing locally, add to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../lara-veil",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "scapteinc/lara-veil": "@dev"
    }
}
```

Run:

```bash
composer require scapteinc/lara-veil:@dev
```

## ⚙️ Configuration

### Publish Configuration Files

```bash
php artisan vendor:publish --tag=lara-veil-config
```

This creates:
- `config/lara-veil.php` - Main system configuration
- `config/vormia.php` - MediaForge configuration

### Example Configuration

```php
// config/lara-veil.php
return [
    'app' => [
        'name' => 'Lara-Veil',
        'description' => 'Extensibility System',
        'admin_email' => 'admin@example.com',
        'url' => env('APP_URL'),
    ],
    'features' => [
        'plugins' => ['enabled' => true],
        'themes' => ['enabled' => true],
        'media' => ['enabled' => true],
    ],
    'caching' => [
        'enabled' => true,
        'ttl' => 3600,
    ],
];
```

## 📚 Usage

### Plugin Management

#### CLI Commands

```bash
# List all plugins
php artisan plugin:list

# Install a plugin
php artisan plugin:install vendor/plugin-name

# Activate a plugin
php artisan plugin:activate vendor/plugin-name

# Deactivate a plugin
php artisan plugin:deactivate vendor/plugin-name

# Uninstall a plugin
php artisan plugin:uninstall vendor/plugin-name --force
```

#### Admin Panel

Navigate to `/admin/plugins` to manage plugins via the web interface with:
- Plugin listing with status and version info
- Activate/deactivate plugins
- Create custom plugins
- View plugin details

### Theme Management

#### CLI Commands

```bash
# List all themes
php artisan theme:list

# Install a theme
php artisan theme:install vendor/theme-name

# Activate a theme
php artisan theme:activate theme-slug
```

#### Admin Panel

Navigate to `/admin/themes` to manage themes via the web interface with:
- Theme grid view with thumbnails
- Active/inactive status
- Theme activation
- Theme customization options

### Media Management

#### CLI Commands

```bash
# View media statistics
php artisan media:info

# Cleanup missing files
php artisan media:cleanup --dry-run

# Prune old files (older than 30 days)
php artisan media:prune --days=30

# Run diagnostics
php artisan media:diagnose
```

#### Admin Panel

Navigate to `/admin/media` for the Media Library with:
- File upload with drag-and-drop
- Search and filtering by type
- Grid view with preview
- File management and deletion

### Hook System

#### Register Hooks

```php
// In a plugin or service provider
use Scapteinc\LaraVeil\Core\HookSystem;

app('hook')->addAction('system.init', function() {
    // Custom initialization logic
});

app('hook')->addFilter('media.upload', function($file) {
    // Process before upload
    return $file;
});
```

#### Execute Hooks

```php
// Execute actions
app('hook')->doAction('custom.action', $data);

// Apply filters
$result = app('hook')->applyFilters('custom.filter', $initialValue);
```

### MediaForge Service

Advanced media processing with fluent API:

```php
use Scapteinc\LaraVeil\Services\Vrm\MediaForgeService;

$forge = app('media.forge');

// Process images
$results = $forge
    ->upload($request->file('image'))
    ->resize(1920, 1080, keepAspectRatio: true)
    ->compress(quality: 85)
    ->thumbnail([300, 200, 100])
    ->convert('webp', quality: 85)
    ->watermark('assets/logo.png', position: 'bottom-right')
    ->to('media/uploads')
    ->run();

// Delete media
$forge->delete('path/to/file.jpg', type: ['thumbnail', 'converted']);
```

#### Supported Operations

- **resize** - Resize with aspect ratio preservation
- **compress** - JPEG compression with quality control
- **convert** - Format conversion (JPG, PNG, WebP, GIF)
- **thumbnail** - Multi-size thumbnail generation
- **watermark** - Image/text watermarking with positioning
- **avatar** - Rounded square avatar creation
- **rotate** - Image rotation with background color
- **flip** - Horizontal/vertical/both flips
- **blur** - Blur filter with amount control

### RESTful API

Full RESTful API for integration:

```bash
# Plugins
GET    /api/system/plugins          # List all plugins
POST   /api/system/plugins          # Create plugin
GET    /api/system/plugins/{id}     # Get plugin details
PUT    /api/system/plugins/{id}     # Update plugin
DELETE /api/system/plugins/{id}     # Delete plugin
POST   /api/system/plugins/{id}/activate
POST   /api/system/plugins/{id}/deactivate

# Themes
GET    /api/system/themes           # List themes
GET    /api/system/themes/{id}      # Get theme details
POST   /api/system/themes/{id}/activate

# Media
GET    /api/system/media            # List media
POST   /api/system/media            # Upload file
GET    /api/system/media/{id}       # Get media details
DELETE /api/system/media/{id}       # Delete media
```

## 🎨 Admin Components

### Volt Components (Reactive)

Interactive admin panel with Laravel Volt:

- **dashboard.volt** - System statistics and quick actions
- **plugins-table.volt** - Plugin listing with filtering and actions
- **themes-table.volt** - Theme management with activation
- **media-gallery.volt** - Media library with upload
- **settings-form.volt** - System settings management

### Blade Views

Server-rendered admin pages:

- Dashboard overview
- Plugin management
- Theme management
- Media library
- Settings
- System information

## 📊 Database

### Migrations

Auto-loaded migrations create three main tables:

```sql
-- Plugins registry
CREATE TABLE plugins (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  namespace VARCHAR(255) UNIQUE,
  version VARCHAR(50),
  is_active BOOLEAN,
  settings JSON,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Themes registry
CREATE TABLE themes (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  slug VARCHAR(255) UNIQUE,
  version VARCHAR(50),
  parent_id BIGINT,
  is_active BOOLEAN,
  settings JSON,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Media files
CREATE TABLE media (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  path VARCHAR(255),
  media_type VARCHAR(50),
  mime_type VARCHAR(100),
  file_size BIGINT,
  mediaable_type VARCHAR(255),
  mediaable_id BIGINT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Models

Use Eloquent models for database interaction:

```php
use Scapteinc\LaraVeil\Models\Plugin;
use Scapteinc\LaraVeil\Models\Theme;
use Scapteinc\LaraVeil\Models\Media;

// Get all active plugins
$active = Plugin::where('is_active', true)->get();

// Get active theme
$theme = Theme::where('is_active', true)->first();

// Get media files
$media = Media::latest()->paginate(15);
```

## 🔐 Authentication

All admin routes are protected by the `auth` middleware:

```php
// web.php
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // ... other routes
});
```

## 📦 Asset Publishing

Publish assets to customize styles and scripts:

```bash
# Publish only assets
php artisan vendor:publish --tag=lara-veil-assets

# Publish configuration
php artisan vendor:publish --tag=lara-veil-config

# Publish migrations
php artisan vendor:publish --tag=lara-veil-migrations

# Publish views
php artisan vendor:publish --tag=lara-veil-views

# Publish everything
php artisan vendor:publish --tag=lara-veil-all
```

## 🧪 Testing

Run tests:

```bash
./vendor/bin/pest tests/
```

## 📝 Creating Plugins

A Lara-Veil plugin is a Laravel package with metadata:

```php
// config/plugin.php
return [
    'name' => 'My Plugin',
    'namespace' => 'vendor/my-plugin',
    'version' => '1.0.0',
    'description' => 'Plugin description',
    'author' => 'Your Name',
];
```

## 📖 Full Documentation

For complete documentation, see [system.md](../../system.md) in the root project.

## 🤝 Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## 📄 License

Licensed under the [MIT License](LICENSE).

## 🔒 Security

See [SECURITY.md](SECURITY.md) for security reporting and policies.
composer update
```

## Quick Start

### 1. Package Auto-Loads Resources

The Lara-Veil package automatically loads:
- ✅ **Migrations** - Database tables are registered
- ✅ **Views** - Admin dashboard and management panels (no copying needed)
- ✅ **Routes** - Web routes and API endpoints are registered
- ✅ **Controllers** - All HTTP controllers are included
- ✅ **Services** - MediaForge and other services are available

No files need to be copied to your Laravel project!

### 2. (Optional) Publish Configuration Files

If you want to customize the default configuration:

```bash
php artisan vendor:publish --tag=lara-veil-config
```

This publishes configuration to `config/lara-veil.php` and `config/vormia.php`.

### 3. (Optional) Publish Migrations

To customize migrations before running them:

```bash
php artisan vendor:publish --tag=lara-veil-migrations
```

### 4. Create Required Directories

In your Laravel project root, create:

```bash
mkdir -p packages themes
```

### 5. Run Migrations

```bash
php artisan migrate
```

## Package Structure

The package is self-contained within the `scapteinc/lara-veil` composer package:

```
scapteinc/lara-veil/
├── src/
│   ├── Core/                    # Plugin, Theme, Hook managers
│   ├── Services/                # MediaForge service
│   ├── Models/                  # Eloquent models
│   ├── Http/
│   │   └── Controllers/         # API & Admin controllers
│   ├── Providers/               # Service providers
│   ├── routes/                  # Web & API routes
│   └── database/
│       └── migrations/          # Database migrations
├── config/                      # Configuration files
├── resources/
│   └── views/                   # Blade templates (auto-loaded)
└── composer.json
```

## Usage Examples

### Managing Plugins

```php
use Scapteinc\LaraVeil\Managers\PluginManager;

$manager = app('plugin.manager');

// List all plugins
$plugins = $manager->all();

// Activate a plugin
$manager->activate('my-plugin');

// Deactivate a plugin
$manager->deactivate('my-plugin');
```

### Using Hooks

```php
use Scapteinc\LaraVeil\Facades\Hook;

// Add an action
Hook::addAction('admin_menu', function() {
    // Custom logic
});

// Apply a filter
$content = Hook::applyFilters('the_content', $post->content);
```

### Media Processing

```php
use Scapteinc\LaraVeil\Services\MediaForgeService;

$media = app('media.forge')
    ->upload($request->file('image'))
    ->resize(1920, 1080)
    ->compress(quality: 85)
    ->thumbnail([[300, 300, 'thumb'], [150, 150, 'mini']])
    ->run();
```

### Theme Management

```php
use Scapteinc\LaraVeil\Managers\ThemeManager;

$manager = app('theme.manager');

// Get active theme
$theme = $manager->active();

// Switch theme
$manager->setActive('dark-theme');
```

## Configuration

### Main Configuration (`config/lara-veil.php`)

```php
return [
    'plugin' => [
        'paths' => [
            base_path('packages'),
        ],
        'cache' => [
            'enabled' => env('PLUGIN_CACHE', true),
            'ttl' => 3600,
        ],
    ],
    'theme' => [
        'paths' => [
            base_path('themes'),
        ],
    ],
];
```

### Media Configuration (`config/vormia.php`)

```php
return [
    'mediaforge' => [
        'driver' => env('MEDIA_DRIVER', 'auto'), // 'auto', 'gd', 'imagick'
        'default_quality' => 85,
        'default_format' => 'webp',
        'preserve_originals' => true,
    ],
];
```

## Available Hooks

### System Hooks
- `system.init` - After system initialization
- `system.booted` - After application boot
- `system.shutdown` - Before application shutdown

### Request Hooks
- `request.received` - When request is received
- `request.routed` - After route is matched
- `request.validated` - After request validation

### Database Hooks
- `db.query.executing` - Before query execution
- `db.query.executed` - After query execution
- `db.model.saving` - Before model save
- `db.model.saved` - After model save

### View Hooks
- `view.composing` - Before view is composed
- `view.rendering` - Before view is rendered
- `view.rendered` - After view is rendered

## Database

The package includes migrations for:
- **Plugins table** - Plugin registry and status
- **Themes table** - Theme registry with inheritance
- **Media table** - Media file management with model associations

## Artisan Commands

### Plugin Commands
```bash
php artisan plugin:list              # List all plugins
php artisan plugin:activate {name}   # Activate a plugin
php artisan plugin:deactivate {name} # Deactivate a plugin
php artisan plugin:cache             # Cache plugins
php artisan plugin:clear-cache       # Clear plugin cache
php artisan plugin:diagnose          # Run diagnostics
```

### Theme Commands
```bash
php artisan theme:list               # List all themes
php artisan theme:activate {name}    # Activate a theme
php artisan theme:diagnose           # Run diagnostics
```

### Media Commands
```bash
php artisan media:diagnose           # Media diagnostics
php artisan media:info               # System information
php artisan media:cleanup            # Remove orphaned files
php artisan media:prune              # Delete unassociated media
```

## API Endpoints

The package registers RESTful API endpoints:

```
GET    /api/plugins                  # List plugins
POST   /api/plugins                  # Install plugin
PUT    /api/plugins/{id}             # Update plugin
DELETE /api/plugins/{id}             # Uninstall plugin
POST   /api/plugins/{id}/activate    # Activate plugin
POST   /api/plugins/{id}/deactivate  # Deactivate plugin

GET    /api/themes                   # List themes
POST   /api/themes/{id}/activate     # Activate theme
GET    /api/themes/{id}/preview      # Preview theme

POST   /api/media/upload             # Upload file with processing
GET    /api/media/{id}               # Get media details
DELETE /api/media/{id}               # Delete media and related files
```

## Architecture

```
Lara-Veil Core System
├── Core/
│   ├── PluginManager.php    # Plugin lifecycle management
│   ├── ThemeManager.php     # Theme lifecycle management
│   ├── HookSystem.php       # Action/Filter system
│   └── ServiceManager.php   # Service provider management
├── Services/
│   └── MediaForgeService.php  # Media processing and management
├── Models/
│   ├── Plugin.php           # Plugin Eloquent model
│   ├── Theme.php            # Theme Eloquent model
│   └── Media.php            # Media Eloquent model
└── Providers/
    └── LaraVeilServiceProvider.php  # Package service provider
```

## Security

The package includes:
- Permission-based access control
- Input validation and sanitization
- Security policies for plugin/theme operations
- Automatic signature validation for updates

## Performance

- Configurable plugin caching
- Lazy loading of non-essential components
- Optimized database queries with proper indexing
- Support for both GD and Imagick image drivers
- WebP format support for reduced file sizes

## Testing

Run the test suite:

```bash
npm run test
```

## Contributing

Contributions are welcome! Please create a pull request with:
- Clear description of changes
- Test coverage for new features
- Following PSR-12 coding standards

## License

This package is open-sourced software licensed under the MIT license.

## Support

For issues, questions, or suggestions, please visit:
- GitHub: [scapteinc/lara-veil](https://github.com/scapteinc/lara-veil)

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.
