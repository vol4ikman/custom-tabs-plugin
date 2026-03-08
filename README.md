# Custom Tabs Plugin

Responsive, accessible tabs section for WordPress with a shortcode renderer and an ACF-powered admin UI.

## Overview

`Custom Tabs Plugin` renders a multi-tab content block with:

- Keyboard-accessible tab navigation
- Rich quote content per tab
- User metadata (avatar, name, role, company image)
- Per-tab desktop/mobile background images
- Percent highlight block and CTA
- Global gallery (outside tabs), limited to 5 images

The plugin provides sensible defaults and supports content management from a custom admin page.

## Features

- Shortcode: `[custom_tabs_plugin]`
- Admin page: `Custom Tabs` in WordPress dashboard
- ACF local field group: `Custom Tabs settings`
- Global gallery field (`custom_tabs_gallery`) outside tabs
- Tab repeater field (`custom_tabs_tabs`) with media and text fields
- Responsive mode switching:
    - Desktop: accessible ARIA tabs
    - Mobile (`<=1279px`): synced Slick sliders for tab headers and panels
- Dynamic asset versioning with `time()` cache-busting
- Frontend behavior handled by plugin JS + Slick Carousel

## Requirements

- WordPress 5.8+
- PHP 7.4+ (recommended 8.0+)
- Advanced Custom Fields (ACF) plugin (required for admin editing experience)
- jQuery (bundled with WordPress)
- External Slick Carousel assets loaded from jsDelivr CDN

## Installation

1. Copy folder to:
   `wp-content/plugins/custom-tabs-plugin`
2. Activate **Custom Tabs Plugin** in WP Admin > Plugins.
3. Install and activate **Advanced Custom Fields**.
4. Open WP Admin > **Custom Tabs** and configure fields.

## Usage

Add shortcode anywhere shortcodes are supported:

```text
[custom_tabs_plugin]
```

## Admin Configuration

Open: **WP Admin > Custom Tabs**

### Field Group: Custom Tabs settings

1. **Tabs** (repeater)
2. **Gallery** (top-level gallery, max 5 images)

### Tabs Repeater Fields

- `title` (text)
- `quote` (WYSIWYG, basic toolbar)
- `user_avatar` (image)
- `user_name` (text)
- `user_role` (text)
- `user_company` (image)
- `description` (textarea)
- `percent` (text)
- `percent_description` (textarea)
- `tab_image` (image)
- `tab_image_mobile` (image)
- `cta_text` (text)
- `cta_url` (url)

### Global Fields

- `custom_tabs_gallery` (gallery)
    - Maximum: 5 images
    - Stored outside tabs

## Data Flow

The plugin resolves settings in this order:

1. ACF option values (`get_field(..., 'option')`) if ACF is active
2. Legacy option fallback (`custom_tabs_plugin_options`)
3. Internal defaults from `get_default_settings()`

Image values are normalized to URL strings. Gallery values are normalized to URL arrays and hard-limited to 5 entries.

## Accessibility

Tabs implement ARIA roles and keyboard interactions:

- Arrow Left/Right/Up/Down: move between tabs
- Home: first tab
- End: last tab

Implemented in `assets/js/custom-tabs-plugin.js`.

On mobile viewports (`<=1279px`), tabs/panels switch to synced Slick sliders, while desktop keeps ARIA tab semantics.

## Styling & Fonts

- Typekit import: `https://use.typekit.net/wuz0gtr.css`
- Font family: `"proxima-nova", sans-serif`
- Main style file enqueued: `assets/css/custom-tabs-plugin.min.css`
- SCSS source: `assets/scss/custom-tabs-plugin.scss`

## Hooks

### Filter: `custom_tabs_plugin_items`

Filter tab items before rendering:

```php
add_filter( 'custom_tabs_plugin_items', function( $tabs ) {
    // Modify $tabs here.
    return $tabs;
} );
```

## Files

- `custom-tabs-plugin.php` - main plugin class, admin setup, ACF fields, shortcode rendering
- `assets/js/custom-tabs-plugin.js` - desktop tab logic + mobile Slick sync logic
- `assets/scss/custom-tabs-plugin.scss` - source styles
- `assets/css/custom-tabs-plugin.min.css` - deployed frontend styles

## Frontend Markup Notes

Gallery output structure:

- `.ctp__gallery` (container + title)
- `.ctp__gallery-items` (logos row wrapper)
- `.ctp__gallery-item` (single logo item)

## Notes

- Asset versioning currently uses `time()` at runtime to prevent stale cache while developing.
- If you want long-term browser caching in production, switch to a fixed semantic version or filemtime strategy.

## Troubleshooting

### Admin fields do not appear

- Ensure ACF is installed and activated.
- Confirm you are on **Custom Tabs** admin page.

### Shortcode renders empty

- Ensure at least one tab exists (or defaults are available).
- Check for PHP errors/logs and plugin conflicts.

### Images not rendering

- Verify image fields contain valid media values.
- Confirm media URLs are publicly accessible.

## Changelog

### 1.0.0

- Initial public plugin structure
- ACF-backed admin settings page
- Repeater tabs with media/text fields
- Global gallery field (max 5)
- Accessible shortcode tabs renderer
