# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Fred Video Produto** is a WordPress plugin that adds video upload capabilities to products and pages. It allows administrators to attach two videos to products and pages:
1. A thumbnail/preview video (shown in a floating bubble)
2. A main video (shown in a lightbox when the bubble is clicked)

The plugin is modularized across multiple files with no external dependencies beyond WordPress (pages) and WooCommerce (products).

## Development Setup

### Local WordPress Environment

To develop and test this plugin locally, you'll need a WordPress installation with WooCommerce enabled:

1. Install WordPress locally (using XAMPP, Docker, Local, or similar)
2. Install WooCommerce plugin
3. Place `fred-video-produto.php` in `wp-content/plugins/fred-video-produto/`
4. Activate the plugin from WordPress admin panel

### Testing the Plugin

- **Admin video upload fields**: Edit any WooCommerce product and look for "Miniatura do vídeo" and "Vídeo principal" fields in the General product data section
- **Frontend display**: Visit a product page with both videos configured; you should see a floating bubble in the top-left corner
- **Video interactions**: Click the bubble to open the lightbox; test close buttons (X on bubble and overlay); test ESC key to close overlay

## Architecture

The plugin is modularized into three main files:

### 1. **includes/fred-video-common.php** - Shared Functionality
- **CSS Styles**: All video bubble and lightbox styling (unified across products and pages)
- **Rendering Function**: `fvp_render_video_bubble($type, $thumb_url, $video_url)`
  - Generates unique IDs based on type ('product' or 'page')
  - Renders HTML for bubble + lightbox
  - Includes vanilla JavaScript for interactivity
  - Outputs CSS only once (checks `$fvp_styles_rendered` flag)
- **JavaScript**: Handles:
  - Bubble click → opens overlay with main video
  - Close buttons → hide bubble/overlay
  - ESC key → closes overlay
  - Click outside overlay → closes overlay
  - Autoplay on open (muted to comply with browser policies)

### 2. **includes/fred-video-produtos.php** - Product Functionality
- **Admin Fields**: `fvp_add_video_upload_fields_to_products()`
  - Hook: `woocommerce_product_options_general_product_data`
  - Adds two input fields to WooCommerce product edit page
  - Media uploader with MP4 validation
- **Save Handler**: `fvp_save_video_upload_fields_product($product)`
  - Hook: `woocommerce_admin_process_product_object`
  - Meta keys: `_fred_video_thumbnail`, `_fred_product_video`
- **Frontend Display**: `fvp_display_product_video_bubble()`
  - Hook: `wp_footer` (only on single product pages)
  - Retrieves video URLs and calls `fvp_render_video_bubble('product', ...)`

### 3. **includes/fred-video-pages.php** - Page Functionality
- **Admin Meta Box**: `fvp_add_page_video_meta_box()`
  - Hook: `add_meta_boxes`
  - Adds meta box to page editor (not WooCommerce-dependent)
  - Callback: `fvp_page_video_meta_box_callback()`
- **Save Handler**: `fvp_save_video_upload_fields_page($post_id)`
  - Hook: `save_post_page`
  - Meta keys: `_fred_page_video_thumbnail`, `_fred_page_video`
- **Frontend Display**: `fvp_display_page_video_bubble()`
  - Hook: `wp_footer` (only on single pages)
  - Retrieves video URLs and calls `fvp_render_video_bubble('page', ...)`

### 4. **fred-video-produto.php** - Main Plugin File
- Declares plugin metadata
- Loads all three modules via `require_once`
- No hooks or functionality itself

## Key Implementation Details

### Module Separation Strategy
- **Products**: Requires WooCommerce; uses product meta (prefixed `_fred_product_`)
- **Pages**: Standard WordPress; uses post meta (prefixed `_fred_page_`)
- **Shared**: `fvp_render_video_bubble()` accepts a `$type` parameter to generate unique DOM IDs
- **CSS**: Single stylesheet renders once via `$fvp_styles_rendered` flag to avoid duplicate styles

### Security Considerations
- All video URLs are escaped with `esc_url()` before output to prevent XSS
- Input is sanitized with `esc_url_raw()` before saving to database
- Product/page existence is checked before accessing metadata
- Media uploader validates MP4 extension on client-side

### Video Playback Behavior
- **Thumbnail video**: Autoplays, muted, loops (used as visual indicator)
- **Main video**: Autoplays on overlay open, muted by default, includes error handling for browsers that block autoplay
- **Video format**: MP4 only (validated on upload)

### Responsive Design
- Desktop: 90px fixed bubble at top-left (20%)
- Mobile (≤640px): 27vw bubble at bottom-left, uses viewport-relative sizing for all elements

### Language
- All UI text and comments are in Portuguese (pt-BR)

### Element ID Convention
- Product elements: `fvp-video-bubble-wrapper-product`, `fvp-video-overlay-product`, etc.
- Page elements: `fvp-video-bubble-wrapper-page`, `fvp-video-overlay-page`, etc.
- This allows multiple videos on the same page (e.g., product in sidebar + page content) without conflicts

## Common Development Tasks

### Adding Support for a New Post Type (e.g., Custom Products)
1. Create a new file `includes/fred-video-[posttype].php` (e.g., `fred-video-custom-products.php`)
2. Create field registration function using appropriate hook for that post type
3. Create save handler for that post type's save event
4. Create frontend display function that:
   - Checks `is_singular('[posttype]')`
   - Retrieves meta with unique prefix (e.g., `_fred_[posttype]_video_thumbnail`)
   - Calls `fvp_render_video_bubble('[posttype]', $thumb_url, $video_url)`
5. Load the new module in `fred-video-produto.php` with `require_once`

Example structure for posts:
```php
// includes/fred-video-posts.php
add_action('add_meta_boxes', 'fvp_add_post_video_meta_box');
add_action('save_post_post', 'fvp_save_video_upload_fields_post');
add_action('wp_footer', 'fvp_display_post_video_bubble');

function fvp_display_post_video_bubble() {
    if (!is_singular('post')) return;
    // ... retrieve video meta with _fred_post_ prefix
    // ... call fvp_render_video_bubble('post', ...)
}
```

### Modifying UI/Styling
- Styles are in `includes/fred-video-common.php` in the `<style>` tag
- Responsive breakpoints at 640px media query
- CSS classes use `fvp-` prefix to avoid conflicts
- Modify `fvp_render_video_bubble()` to adjust element structure

### Testing Video Compatibility
- Always test with actual MP4 files of varying sizes/codecs
- Test on mobile devices to ensure responsive design works
- Test autoplay behavior across browsers (some restrict autoplay of non-muted media)
- Test that multiple videos (product + page) render correctly without ID conflicts

## File Structure

```
fred-video-produto/
├── fred-video-produto.php              # Main plugin file (loader)
├── includes/
│   ├── fred-video-common.php           # Shared: CSS, rendering function, JS
│   ├── fred-video-produtos.php         # Product-specific: fields, save, display
│   └── fred-video-pages.php            # Page-specific: meta box, save, display
└── CLAUDE.md                            # This documentation
```

## File Activation & Deployment

1. Copy entire `fred-video-produto/` directory to `wp-content/plugins/`
2. Activate via WordPress admin
3. No database migrations or additional setup required
4. Products get video fields in WooCommerce editor
5. Pages get video meta box in standard WordPress editor

## Important Notes

- **Modular architecture**: Each post type (products, pages) has its own module for maintainability
- **WooCommerce optional**: Products require WooCommerce; pages work with core WordPress
- **No text domain**: The plugin does not currently use WordPress i18n functions for translations
- **Fixed positioning**: The video bubble uses fixed positioning relative to the viewport, not the content
- **Unique IDs**: DOM elements use `type`-based suffixes to allow multiple videos on same page without conflicts
- **CSS optimization**: Styles render only once despite multiple videos via `$fvp_styles_rendered` flag
- **Version control**: Project is now under git control (v2.0 introduced modular architecture)
