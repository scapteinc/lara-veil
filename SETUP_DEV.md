# Development Setup Guide

This guide explains how to set up Lara-Veil for local development.

## Prerequisites

- PHP 8.1+
- Composer
- Laravel 9.0+
- Git

## Local Development Setup

### Option 1: Path Repository (Recommended for Development)

If you're developing Lara-Veil alongside a Laravel application:

#### In your Laravel project's `composer.json`:

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

Replace `../lara-veil` with the actual path to the lara-veil directory.

#### Then run:

```bash
composer update
```

### Option 2: Composer Linking

For simpler setups without symlinks:

```bash
# In your Laravel project
composer require scapteinc/lara-veil:@dev --prefer-source
```

### Option 3: GitHub Repository (Before Publishing to Packagist)

Once pushed to GitHub:

```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/scapteinc/lara-veil.git"
        }
    ],
    "require": {
        "scapteinc/lara-veil": "dev-main"
    }
}
```

## Setting Up the Package

### 1. Clone the Repository

```bash
git clone https://github.com/scapteinc/lara-veil.git
cd lara-veil
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Publish Configuration (in your Laravel app)

```bash
php artisan vendor:publish --provider="Scapteinc\LaraVeil\Providers\LaraVeilServiceProvider"
```

This will publish:
- `config/lara-veil.php` - Main package configuration
- `config/vormia.php` - MediaForge configuration
- Database migrations

### 4. Create Required Directories

In your Laravel project root:

```bash
mkdir -p packages themes
```

## Running Tests

From the lara-veil directory:

```bash
# Run tests
composer test

# Format code
composer format
```

## Troubleshooting

### "Could not find a version matching scapteinc/lara-veil"

This means Composer is trying to find the package on Packagist. Solution:

1. **For development**: Use the path repository method (Option 1 above)
2. **For testing**: Push to GitHub and use the GitHub repository method (Option 3 above)
3. **For production**: Wait for Packagist publishing and use `composer require scapteinc/lara-veil`

### Namespace Not Found

If you get namespace errors:

1. Clear Composer cache:
   ```bash
   composer dump-autoload
   ```

2. Ensure the path in `repositories` is correct in your `composer.json`

3. Check that `autoload.psr-4` in lara-veil's `composer.json` matches:
   ```json
   "Scapteinc\\LaraVeil\\": "src/"
   ```

### Symlink Issues on Windows

If symlinks don't work on Windows, remove `"symlink": true` from the repository configuration:

```json
{
    "type": "path",
    "url": "../lara-veil"
}
```

## Next Steps

- Read [README.md](README.md) for usage documentation
- Check [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines
- Review [SECURITY.md](SECURITY.md) for security best practices

## Publishing to Packagist

When ready to release:

1. **Initialize Git** (if not already done):
   ```bash
   git init
   git add .
   git commit -m "Initial commit: Lara-Veil package"
   ```

2. **Create GitHub Repository**:
   - Go to github.com/new
   - Create repository `lara-veil` under scapteinc
   - Push your local repository

3. **Create Release Tag**:
   ```bash
   git tag -a v2.0.0 -m "Release version 2.0.0"
   git push origin v2.0.0
   ```

4. **Submit to Packagist**:
   - Visit https://packagist.org/packages/submit
   - Enter: `https://github.com/scapteinc/lara-veil`
   - Packagist will automatically sync with GitHub releases

## Getting Help

- Check the [README.md](README.md)
- Review [CONTRIBUTING.md](CONTRIBUTING.md)
- Open an issue on GitHub
