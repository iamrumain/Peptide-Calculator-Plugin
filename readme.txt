=== Peptide Calculator ===
Contributors: Rumain Islam
Tags: calculator, elementor, reconstitution, dosage, widget
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A reconstitution calculator widget/shortcode with a full admin dashboard for colors, fonts, sizes, text, and layout.

== Description ==

Adds a two-panel reconstitution calculator:

* Left panel: vial size, water (diluent) volume, and desired dose inputs.
* Right panel: live results — concentration, injection volume, and syringe units (U-100 / U-40 / U-50 / custom).

Use it via:

* The `[peptide_calculator]` shortcode — works in any page builder or the block editor.
* The "Peptide Calculator" Elementor widget — drag, drop, and style per-instance.

Every color, font, size, label, default value, and the layout itself (side-by-side or stacked) is editable from
**Peptide Calculator** in the WordPress admin menu, with a live preview that updates as you type. Elementor widget
instances can also override any of these per-widget; leave a control empty/default to inherit the dashboard setting.

This plugin performs unit conversion math only (concentration, volume, syringe units). It does not provide dosing
recommendations — the desired dose is entered by the site visitor, and a disclaimer (editable, can be hidden) is
shown beneath the results by default.

== Installation ==

1. Upload the `peptide-calculator` folder to `/wp-content/plugins/`.
2. Activate the plugin through the “Plugins” screen in WordPress.
3. Go to **Peptide Calculator** in the admin menu to configure labels, colors, fonts, and layout.
4. Add `[peptide_calculator]` to any page, or drag the “Peptide Calculator” widget into an Elementor page.

== Frequently Asked Questions ==

= Can I have different colors on different pages? =
Yes. The dashboard sets the global default. Each Elementor widget instance can override any color, font, label,
or layout setting individually in its Content/Style tabs.

= Does this tell visitors what dose to take? =
No. The visitor enters the dose they already intend to use; the plugin only does the unit-conversion math
(concentration, volume, syringe units) and displays a disclaimer recommending they verify independently.

== Changelog ==

= 1.0.0 =
* Initial release.
