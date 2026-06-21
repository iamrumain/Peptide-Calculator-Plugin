# Peptide Calculator

A WordPress plugin that provides a Peptide Calculator for use as a shortcode or Elementor widget.

## Features

- Shortcode: `[peptide_calculator]`
- Elementor widget: "Peptide Calculator"
- Admin dashboard for customizing:
  - labels and default input values
  - calculator layout and typography
  - input/result card colors and styles
  - disclaimer text
  - custom CSS
- Live preview in admin when editing settings
- Frontend JavaScript performs calculations for:
  - concentration
  - injection volume
  - syringe units
- Supports U-100, U-50, U-40, or custom syringe units

## Installation

1. Upload the plugin folder to `wp-content/plugins/`.
2. Activate the plugin from the WordPress admin Plugins page.
3. Open the Peptide Calculator settings page under the admin menu.

## Usage

### Shortcode

Use the shortcode in any page, post, or builder:

```php
[peptide_calculator]
```

Shortcode attributes can override settings for that instance. Example:

```php
[peptide_calculator calc_title="My Calculator" accent_color="#ff0000"]
```

### Elementor

If Elementor is active, the plugin registers an Elementor widget under the category "Peptide Calculator." Search for "Peptide Calculator" in the widget panel.

## Settings

The settings page contains tabs for:

- Content & Labels
- Disclaimer
- Input Card Style
- Results Card Style
- Typography
- Layout
- Advanced

Available global settings include:

- Calculator title, field labels, and results labels
- Default vial size, water volume, and desired dose
- Dose unit (mcg / mg)
- Syringe type and custom units per mL
- Decimal precision for concentration, volume, and syringe units
- Card colors, border, icon visibility, and shadow
- Results gradient and text colors
- Font family settings and base font sizes
- Layout style (side-by-side or stacked)
- Custom CSS

## Templates and Assets

- `templates/calculator.php` renders the calculator markup.
- `assets/js/pc-frontend.js` handles frontend interaction and recalculation.
- `assets/css/pc-frontend.css` contains frontend styles.
- Admin styles and scripts are loaded in `assets/css/pc-admin.css` and `assets/js/pc-admin.js`.

## Data Storage

- Options are stored in a single array under `pc_settings`.
- Default values are defined in `includes/class-pc-settings.php`.
- The admin uses the WordPress Settings API and sanitizes all values.

## Uninstall

The uninstall handler in `uninstall.php` deletes the `pc_settings` option and cleans up multisite settings if applicable.

## Files

- `peptide-calculator.php` — main plugin bootstrap and Elementor registration.
- `includes/class-pc-settings.php` — settings defaults, sanitization, and helpers.
- `includes/class-pc-assets.php` — enqueue frontend styles/scripts.
- `includes/class-pc-render.php` — builds calculator markup and passes data.
- `includes/class-pc-shortcode.php` — shortcode registration.
- `includes/class-pc-admin.php` — admin menu, settings page, and preview.
- `includes/class-pc-elementor-widget.php` — Elementor widget integration.
- `templates/calculator.php` — actual calculator HTML template.
- `uninstall.php` — plugin uninstall cleanup.

## Notes

- The calculator assumes peptide vial amount in mg and converts to mcg if the dose unit is `mcg`.
- The frontend supports multiple calculators on the same page by generating unique IDs.
- The admin preview renders the same template and style as the frontend.

## Admin dashboard view

<img width="1443" height="838" alt="image" src="https://github.com/user-attachments/assets/7f21379c-c272-4772-b8b9-1cc8576613e0" />

<img width="1518" height="841" alt="image" src="https://github.com/user-attachments/assets/a402496a-1b56-464b-9e21-2e8f4dd37448" />

<img width="1335" height="858" alt="image" src="https://github.com/user-attachments/assets/36dc5582-aa93-4108-b706-1f58f9229742" />

<img width="1306" height="851" alt="image" src="https://github.com/user-attachments/assets/bdecc89c-3e8a-4b1c-b403-9ac7b5fb58ad" />


## User interface
<img width="1056" height="399" alt="image" src="https://github.com/user-attachments/assets/b22be3c5-1f2c-4551-b3d6-06f960820d50" />
