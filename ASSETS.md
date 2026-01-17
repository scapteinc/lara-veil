# Lara-Veil Assets Documentation

This document describes the precompiled assets included in the Lara-Veil package.

## 📦 Asset Files

### CSS Styles (`resources/css/lara-veil.css`)

Precompiled Tailwind-based stylesheet with component classes for the admin panel.

#### Included Utilities

- **Container Classes**
  - `.lara-veil-container` - Main page container
  - `.lara-veil-card` - Card component with shadow and rounding
  - `.lara-veil-card-header` - Card header section
  - `.lara-veil-card-body` - Card body section
  - `.lara-veil-card-footer` - Card footer section

- **Navigation Classes**
  - `.lara-veil-nav` - Navigation bar
  - `.lara-veil-nav-item` - Navigation item
  - `.lara-veil-nav-item.active` - Active navigation item

- **Button Classes**
  - `.lara-veil-button` - Base button style
  - `.lara-veil-button-primary` - Primary (blue) button
  - `.lara-veil-button-secondary` - Secondary (gray) button
  - `.lara-veil-button-success` - Success (green) button
  - `.lara-veil-button-danger` - Danger (red) button

- **Table Classes**
  - `.lara-veil-table` - Base table
  - `.lara-veil-table thead` - Table header styling
  - `.lara-veil-table th` - Table heading cells
  - `.lara-veil-table tbody` - Table body
  - `.lara-veil-table td` - Table data cells

- **Badge Classes**
  - `.lara-veil-badge` - Base badge
  - `.lara-veil-badge-active` - Active badge (green)
  - `.lara-veil-badge-inactive` - Inactive badge (gray)

- **Form Classes**
  - `.lara-veil-form-group` - Form field grouping
  - `.lara-veil-form-label` - Form label
  - `.lara-veil-form-input` - Form input field
  - `.lara-veil-form-error` - Form error message

- **Modal Classes**
  - `.lara-veil-modal` - Modal container
  - `.lara-veil-modal-content` - Modal content wrapper

- **Grid Utilities**
  - `.lara-veil-grid` - 3-column responsive grid
  - `.lara-veil-grid-2` - 2-column responsive grid
  - `.lara-veil-grid-4` - 4-column responsive grid

### JavaScript (`resources/js/lara-veil.js`)

Interactive functionality for the admin panel.

#### Initialization

Automatically initializes when DOM is ready:
```javascript
document.addEventListener('DOMContentLoaded', () => {
    initializeAdminPanel();
});
```

#### Features

1. **Table Actions**
   - Edit row handler
   - Delete row with confirmation
   - Activate/deactivate items
   - Listens to `[data-action]` attributes

2. **Form Validation**
   - Real-time validation
   - Error styling
   - Required field checking
   - Listens to forms with `[data-validate]`

3. **Modal Handling**
   - Open/close modals
   - Background click to close
   - Modal state management
   - Uses `[data-modal-trigger]` and `[data-modal]`

4. **Notifications**
   - Success/error/warning/info types
   - Auto-dismiss after 5 seconds
   - Manual close button
   - Custom timeout support

5. **Confirmations**
   - Confirm delete operations
   - Custom messages
   - Uses `[data-confirm]` attribute

#### Global API

Access functionality via `window.LaraVeil` object:

```javascript
// Show notification
LaraVeil.showNotification('Success!', 'success');

// Validate form
LaraVeil.validateForm(formElement);

// Edit/delete/activate/deactivate
LaraVeil.editRow(id);
LaraVeil.deleteRow(id);
LaraVeil.activateItem(id);
LaraVeil.deactivateItem(id);
```

## 🚀 Publishing Assets

Assets are automatically served from the package but can be published for customization:

### Publish Only Assets

```bash
php artisan vendor:publish --tag=lara-veil-assets
```

This copies files to:
- `public/vendor/lara-veil/css/lara-veil.css`
- `public/vendor/lara-veil/js/lara-veil.js`

### Publish All Resources

```bash
php artisan vendor:publish --tag=lara-veil-all
```

This publishes:
- Configuration files
- Migrations
- Views
- Components
- Assets

## 💻 Usage Examples

### Using CSS Classes

```blade
<!-- Card Component -->
<div class="lara-veil-card">
    <div class="lara-veil-card-header">
        <h2 class="text-xl font-bold">Plugins</h2>
    </div>
    <div class="lara-veil-card-body">
        <!-- Content -->
    </div>
</div>

<!-- Responsive Grid -->
<div class="lara-veil-grid">
    <div>Item 1</div>
    <div>Item 2</div>
    <div>Item 3</div>
</div>

<!-- Buttons -->
<button class="lara-veil-button lara-veil-button-primary">Save</button>
<button class="lara-veil-button lara-veil-button-danger">Delete</button>

<!-- Table -->
<table class="lara-veil-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Plugin Name</td>
            <td><span class="lara-veil-badge lara-veil-badge-active">Active</span></td>
        </tr>
    </tbody>
</table>
```

### Using JavaScript Functions

```blade
<!-- Trigger notification -->
<button onclick="LaraVeil.showNotification('Saved!', 'success')">
    Save
</button>

<!-- Confirmation dialog -->
<button data-confirm="Are you sure you want to delete?" onclick="deleteItem()">
    Delete
</button>

<!-- Modal trigger -->
<button data-modal-trigger="myModal">Open Modal</button>

<div id="myModal" data-modal class="hidden">
    <div class="lara-veil-modal-content">
        <button data-modal-close>Close</button>
        <!-- Content -->
    </div>
</div>

<!-- Form validation -->
<form data-validate onsubmit="handleSubmit(event)">
    <input type="text" required>
    <button type="submit">Submit</button>
</form>
```

## 🎨 Customizing Assets

### Customize CSS

Published assets can be modified in `public/vendor/lara-veil/css/lara-veil.css`.

To recompile with custom Tailwind configuration, use the project's Vite setup:

```bash
npm run dev  # Development
npm run build  # Production
```

### Customize JavaScript

Edit `public/vendor/lara-veil/js/lara-veil.js` to modify behavior.

Or extend the global API:

```javascript
// Add custom functions
window.LaraVeil.customAction = function(id) {
    // Custom logic
};
```

## 📊 Asset Loading Order

1. Main app CSS/JS (Vite-compiled)
2. Lara-Veil CSS (`vendor/lara-veil/css/lara-veil.css`)
3. Lara-Veil JS (deferred, loads at end)

This ensures Tailwind utilities load before Lara-Veil-specific classes.

## 🔄 Development Workflow

When developing with assets:

1. **Development Mode**
   ```bash
   npm run dev
   ```
   - Vite watches for changes
   - Fast refresh enabled
   - Source maps available

2. **Production Build**
   ```bash
   npm run build
   ```
   - Minified CSS/JS
   - Optimized for performance
   - Ready for deployment

3. **Publish Assets**
   ```bash
   php artisan vendor:publish --tag=lara-veil-assets
   ```
   - Deploy precompiled assets
   - Ready for serving

## 📋 Asset Checklist

- ✅ CSS precompiled with Tailwind
- ✅ JavaScript with IIFE pattern
- ✅ Global API exposure (`window.LaraVeil`)
- ✅ No external dependencies required
- ✅ CSRF token support built-in
- ✅ Responsive design
- ✅ Dark mode compatible (can be extended)
- ✅ Accessibility considerations

## 🐛 Troubleshooting

### Assets Not Loading

1. Check that assets are published:
   ```bash
   php artisan vendor:publish --tag=lara-veil-assets
   ```

2. Verify files exist in `public/vendor/lara-veil/`

3. Clear browser cache and hard-refresh (Ctrl+Shift+R)

### Styles Not Applying

1. Ensure main app CSS loads before Lara-Veil CSS
2. Check for conflicting Tailwind classes
3. Verify Tailwind is configured in `tailwind.config.js`

### JavaScript Not Working

1. Open browser console for errors
2. Verify `resources/js/lara-veil.js` is loaded
3. Check CSRF token is present in `<meta>` tag
4. Ensure `defer` attribute on script tag

## 📚 Related Documentation

- [README.md](./README.md) - Main package documentation
- [SETUP_DEV.md](./SETUP_DEV.md) - Development setup
- [system.md](../../system.md) - Complete system architecture
