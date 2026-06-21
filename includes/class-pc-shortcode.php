<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PC_Shortcode {

	public static function init() {
		add_shortcode( 'peptide_calculator', array( __CLASS__, 'render' ) );
	}

	/**
	 * [peptide_calculator title="Custom Title" layout="stack" accent_color="#ff0000" ...]
	 * Any shortcode attribute matching a settings key overrides the global default
	 * for that one instance only.
	 */
	public static function render( $atts ) {
		PC_Assets::enqueue_frontend();

		$atts = is_array( $atts ) ? $atts : array();

		// Friendly alias: title -> calc_title
		if ( isset( $atts['title'] ) && ! isset( $atts['calc_title'] ) ) {
			$atts['calc_title'] = $atts['title'];
		}

		return PC_Render::render( $atts );
	}
}
