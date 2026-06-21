<?php
/**
 * Plugin Name:       Peptide Calculator
 * Plugin URI:         https://github.com/iamrumain/Peptide-Calculator-Plugin
 * Description:        A reconstitution calculator (vial size, water volume, desired dose → concentration, injection volume, syringe units) with an Elementor widget, a [peptide_calculator] shortcode, and a full admin dashboard for colors, fonts, sizes, text, and layout.
 * Version:             1.0.0
 * Requires at least:   6.0
 * Requires PHP:        7.4
 * Author:              Rumain Islam
 * Author URI:          https://github.com/iamrumain
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         peptide-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PC_VERSION', '1.0.0' );
define( 'PC_PATH', plugin_dir_path( __FILE__ ) );
define( 'PC_URL', plugin_dir_url( __FILE__ ) );
define( 'PC_BASENAME', plugin_basename( __FILE__ ) );

require_once PC_PATH . 'includes/class-pc-settings.php';
require_once PC_PATH . 'includes/class-pc-assets.php';
require_once PC_PATH . 'includes/class-pc-render.php';
require_once PC_PATH . 'includes/class-pc-shortcode.php';
require_once PC_PATH . 'includes/class-pc-admin.php';

register_activation_hook( __FILE__, array( 'PC_Settings', 'install_defaults' ) );

add_action( 'init', array( 'PC_Shortcode', 'init' ) );

if ( is_admin() ) {
	PC_Admin::init();
}

/**
 * Register the Elementor widget once Elementor itself is ready.
 * Using elementor/widgets/register (Elementor 3.5+) with a fallback
 * to the legacy hook for older installs.
 */
function pc_register_elementor_widget( $widgets_manager ) {
	static $registered = false;
	if ( $registered ) {
		return; // Some Elementor versions fire both the legacy and current hooks.
	}
	$registered = true;

	require_once PC_PATH . 'includes/class-pc-elementor-widget.php';

	if ( method_exists( $widgets_manager, 'register' ) ) {
		$widgets_manager->register( new PC_Elementor_Widget() );
	} else {
		// Elementor < 3.5
		$widgets_manager->register_widget_type( new PC_Elementor_Widget() );
	}
}
add_action( 'elementor/widgets/register', 'pc_register_elementor_widget' );
add_action( 'elementor/widgets/widgets_registered', 'pc_register_elementor_widget' );

/**
 * Optional: give the widget its own category in the Elementor panel so
 * it's easy to find.
 */
function pc_add_elementor_category( $elements_manager ) {
	$elements_manager->add_category(
		'peptide-calculator',
		array(
			'title' => __( 'Peptide Calculator', 'peptide-calculator' ),
			'icon'  => 'eicon-calculator',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'pc_add_elementor_category' );
