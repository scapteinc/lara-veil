# Lara-Veil

A WordPress-like extensibility system for Laravel applications that allows dynamic loading of plugins and themes without modifying core code.

## Features

- **Dynamic Plugin System** - Load/unload plugins at runtime
- **Theme Management** - Switch themes dynamically with inheritance support
- **Hook System** - Action/Filter system similar to WordPress
- **Media Forge Service** - Advanced media processing with image manipulation
- **Service Management** - Automatic service provider registration for plugins/themes
- **Security Policies** - Permission-based access control
- **Update System** - Built-in update management with rollback support
- **Caching** - Performance-optimized with configurable caching
- **Database Integration** - Full database schema and model support

## Installation

Install via Composer:

```bash
composer require scapteinc/lara-veil
```

## Quick Start

### 1. Publish Configuration Files

```bash
php artisan vendor:publish --provider="Larapack\LaraVeil\Providers\LaraVeilServiceProvider"
```

### 2. Configure Plugin/Theme Directories

Ensure you have the following directories in your project root:
```
laravel-app/
├── packages/        # Plugins directory
└── themes/          # Themes directory
```

### 3. Register Service Provider

The package auto-registers with Laravel 5.3+. For older versions, add to `config/app.php`:

```php
'providers' => [
    // ...
    Scapteinc\LaraVeil\Providers\LaraVeilServiceProvider::class,
],
```

### 4. Run Migrations

```bash
php artisan migrate
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
