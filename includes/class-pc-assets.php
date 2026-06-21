<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PC_Assets {

	private static $frontend_done = false;

	public static function enqueue_frontend() {
		if ( self::$frontend_done ) {
			return;
		}
		self::$frontend_done = true;

		wp_enqueue_style( 'pc-frontend', PC_URL . 'assets/css/pc-frontend.css', array(), PC_VERSION );
		wp_enqueue_script( 'pc-frontend', PC_URL . 'assets/js/pc-frontend.js', array(), PC_VERSION, true );

		$settings = PC_Settings::get_all();

		if ( ! empty( $settings['google_font'] ) ) {
			wp_enqueue_style(
				'pc-google-font',
				'https://fonts.googleapis.com/css2?family=' . rawurlencode( $settings['google_font'] ) . ':wght@400;600;700&display=swap',
				array(),
				null
			);
		}

		if ( ! empty( $settings['custom_css'] ) ) {
			wp_add_inline_style( 'pc-frontend', $settings['custom_css'] );
		}
	}
}
