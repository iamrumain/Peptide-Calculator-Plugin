<?php
/**
 * Builds the calculator markup. Used by the shortcode, the Elementor
 * widget, and the admin live preview — so all three always render
 * identically and stay in sync with the same settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PC_Render {

	/** @var int Used to generate unique element IDs when multiple calculators are on one page. */
	private static $instance_count = 0;

	public static function render( $overrides = array() ) {
		self::$instance_count++;
		$uid = 'pc-' . self::$instance_count . '-' . wp_rand( 1000, 9999 );

		$get = function( $key ) use ( $overrides ) {
			return PC_Settings::get( $key, $overrides );
		};

		$layout       = $get( 'layout' ) === 'stack' ? 'pc-layout-stack' : 'pc-layout-row';
		$show_icons   = $get( 'show_icons' ) === '1' || $get( 'show_icons' ) === 'yes';
		$show_shadow  = $get( 'show_shadow' ) === '1' || $get( 'show_shadow' ) === 'yes';
		$show_disc    = $get( 'show_disclaimer' ) === '1' || $get( 'show_disclaimer' ) === 'yes';

		$google_font_url = '';
		if ( $get( 'google_font' ) ) {
			$google_font_url = 'https://fonts.googleapis.com/css2?family=' . rawurlencode( $get( 'google_font' ) ) . ':wght@400;600;700&display=swap';
		}

		$vars = array(
			'--pc-font-heading'   => $get( 'font_heading' ),
			'--pc-font-body'      => $google_font_url ? "'" . $get( 'google_font' ) . "', " . $get( 'font_body' ) : $get( 'font_body' ),
			'--pc-font-size-base' => $get( 'font_size_base' ) . 'px',
			'--pc-font-size-title'=> $get( 'font_size_title' ) . 'px',
			'--pc-font-size-result' => $get( 'font_size_result' ) . 'px',
			'--pc-radius-card'    => $get( 'card_radius' ) . 'px',
			'--pc-radius-input'   => $get( 'input_radius' ) . 'px',
			'--pc-padding'        => $get( 'card_padding' ) . 'px',
			'--pc-gap'            => $get( 'card_gap' ) . 'px',

			'--pc-input-bg'           => $get( 'input_bg' ),
			'--pc-input-border'       => $get( 'input_border' ),
			'--pc-input-text'         => $get( 'input_text' ),
			'--pc-input-title-color'  => $get( 'input_title_color' ),
			'--pc-label-color'        => $get( 'label_color' ),
			'--pc-accent-color'       => $get( 'accent_color' ),
			'--pc-icon-color'         => $get( 'icon_color' ),
			'--pc-card-border-color'  => $get( 'card_border_color' ),

			'--pc-results-grad-start' => $get( 'results_grad_start' ),
			'--pc-results-grad-end'   => $get( 'results_grad_end' ),
			'--pc-results-title-color'=> $get( 'results_title_color' ),
			'--pc-results-subtitle-color' => $get( 'results_subtitle_color' ),
			'--pc-results-label-color'=> $get( 'results_label_color' ),
			'--pc-results-value-color'=> $get( 'results_value_color' ),
			'--pc-results-unit-color' => $get( 'results_unit_color' ),
			'--pc-divider-color'      => $get( 'divider_color' ),
			'--pc-disclaimer-bg'      => $get( 'disclaimer_bg' ),
			'--pc-disclaimer-text'    => $get( 'disclaimer_text_color' ),
		);

		$style_attr = '';
		foreach ( $vars as $name => $val ) {
			if ( $val === '' || $val === null ) {
				continue;
			}
			$style_attr .= esc_attr( $name ) . ':' . esc_attr( $val ) . ';';
		}

		$data = array(
			'vial'       => $get( 'default_vial_mg' ),
			'water'      => $get( 'default_water_ml' ),
			'dose'       => $get( 'default_dose_mcg' ),
			'unitsPerMl' => PC_Settings::units_per_ml( $overrides ),
			'unitWord'   => PC_Settings::syringe_unit_word( $overrides ),
			'decConc'    => (int) $get( 'decimals_concentration' ),
			'decVol'     => (int) $get( 'decimals_volume' ),
			'decUnits'   => (int) $get( 'decimals_units' ),
			'doseUnit'   => $get( 'dose_unit' ),
		);

		ob_start();
		include PC_PATH . 'templates/calculator.php';
		return ob_get_clean();
	}
}
