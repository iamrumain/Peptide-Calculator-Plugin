<?php
/**
 * Settings schema, defaults, and option helpers.
 *
 * Every value the admin dashboard can edit lives in ONE option array
 * (option name: pc_settings) so Elementor widget instances and the
 * [peptide_calculator] shortcode can both fall back to the same
 * global defaults, and the live preview can read/write them in one pass.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PC_Settings {

	const OPTION_KEY = 'pc_settings';

	/**
	 * Full default settings array. Field keys match the admin form's
	 * `name="pc_settings[...]"` inputs and the Elementor widget control IDs.
	 */
	public static function defaults() {
		return array(

			// ---- Content / labels -------------------------------------------------
			'calc_title'          => 'Reconstitution Calculator',
			'results_title'       => 'Results',
			'results_subtitle'    => 'Based on your inputs',

			'vial_label'          => 'Vial size (mg)',
			'water_label'         => 'Bacteriostatic water (mL)',
			'dose_label'          => 'Desired dose (mcg)',

			'concentration_label' => 'Concentration',
			'volume_label'        => 'Injection volume',
			'syringe_label'       => 'Insulin syringe',

			'disclaimer_text'     => 'For research purposes only. Always consult published protocols and verify calculations independently.',
			'show_disclaimer'     => '1',
			'disclaimer_text_color' => '#e7e4fb',

			// ---- Defaults shown in the calculator on load --------------------------
			'default_vial_mg'     => '10',
			'default_water_ml'    => '2',
			'default_dose_mcg'    => '250',

			// ---- Behaviour ----------------------------------------------------------
			'syringe_type'        => 'u100', // u100 | u40 | u50 | custom
			'syringe_custom_units_per_ml' => '100',
			'syringe_custom_label' => 'units',
			'decimals_concentration' => '1',
			'decimals_volume'        => '3',
			'decimals_units'         => '1',
			'dose_unit'              => 'mcg', // mcg | mg

			// ---- Layout ---------------------------------------------------------------
			'layout'              => 'row', // row | stack
			'show_icons'          => '1',
			'show_shadow'         => '1',
			'card_radius'         => '16',
			'input_radius'        => '10',
			'card_padding'        => '32',
			'card_gap'            => '24',

			// ---- Typography -----------------------------------------------------------
			'font_heading'        => "Georgia, 'Times New Roman', Times, serif",
			'font_body'           => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
			'google_font'         => '',
			'font_size_base'      => '15',
			'font_size_title'     => '22',
			'font_size_result'    => '28',

			// ---- Colors: input card -----------------------------------------------------
			'input_bg'            => '#ffffff',
			'input_border'        => '#e5e7eb',
			'input_text'          => '#1f2937',
			'input_title_color'   => '#1f2937',
			'label_color'         => '#374151',
			'accent_color'        => '#2563eb',
			'icon_color'          => '#2563eb',
			'card_border_color'   => '#e5e7eb',

			// ---- Colors: results card ----------------------------------------------------
			'results_grad_start'  => '#1e1b6e',
			'results_grad_end'    => '#5b21b6',
			'results_title_color' => '#ffffff',
			'results_subtitle_color' => '#c7c2f0',
			'results_label_color' => '#cbc6ef',
			'results_value_color' => '#ffffff',
			'results_unit_color'  => '#cbc6ef',
			'divider_color'       => 'rgba(255,255,255,0.18)',
			'disclaimer_bg'       => 'rgba(255,255,255,0.12)',

			// ---- Misc -------------------------------------------------------------------
			'custom_css'          => '',
		);
	}

	/**
	 * Get the full saved settings array merged over defaults so new
	 * keys added in updates always have a sane fallback.
	 */
	public static function get_all() {
		$saved = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		return wp_parse_args( $saved, self::defaults() );
	}

	/**
	 * Get one setting, optionally overridden (e.g. by Elementor widget
	 * instance controls). Pass $overrides = array of widget settings;
	 * only non-empty-string values override the global default.
	 */
	public static function get( $key, $overrides = array() ) {
		if ( is_array( $overrides ) && isset( $overrides[ $key ] ) && $overrides[ $key ] !== '' && $overrides[ $key ] !== null ) {
			return $overrides[ $key ];
		}
		$all = self::get_all();
		return isset( $all[ $key ] ) ? $all[ $key ] : '';
	}

	public static function install_defaults() {
		if ( false === get_option( self::OPTION_KEY, false ) ) {
			add_option( self::OPTION_KEY, self::defaults() );
		}
	}

	/**
	 * Sanitize the whole settings array on save (Settings API callback).
	 */
	public static function sanitize( $input ) {
		$defaults  = self::defaults();
		$clean     = array();
		$color_keys = array(
			'input_bg', 'input_border', 'input_text', 'input_title_color', 'label_color',
			'accent_color', 'icon_color', 'card_border_color', 'results_grad_start',
			'results_grad_end', 'results_title_color', 'results_subtitle_color',
			'results_label_color', 'results_value_color', 'results_unit_color',
			'disclaimer_text_color',
		);
		$rawcolor_keys = array( 'divider_color', 'disclaimer_bg' ); // may be rgba()
		$number_keys = array(
			'default_vial_mg', 'default_water_ml', 'default_dose_mcg',
			'syringe_custom_units_per_ml', 'decimals_concentration', 'decimals_volume',
			'decimals_units', 'card_radius', 'input_radius', 'card_padding', 'card_gap',
			'font_size_base', 'font_size_title', 'font_size_result',
		);

		foreach ( $defaults as $key => $default_val ) {
			if ( ! isset( $input[ $key ] ) ) {
				// Checkboxes that are unchecked won't be posted at all.
				$clean[ $key ] = in_array( $key, array( 'show_disclaimer', 'show_icons', 'show_shadow' ), true ) ? '0' : $default_val;
				continue;
			}
			$val = $input[ $key ];

			if ( in_array( $key, $color_keys, true ) ) {
				$clean[ $key ] = sanitize_hex_color( $val ) ? $val : $default_val;
			} elseif ( in_array( $key, $rawcolor_keys, true ) ) {
				$clean[ $key ] = preg_match( '/^[#a-zA-Z0-9,\.\(\)\s%]+$/', $val ) ? sanitize_text_field( $val ) : $default_val;
			} elseif ( in_array( $key, $number_keys, true ) ) {
				$clean[ $key ] = is_numeric( $val ) ? $val : $default_val;
			} elseif ( in_array( $key, array( 'custom_css' ), true ) ) {
				$clean[ $key ] = wp_strip_all_tags( $val );
			} elseif ( in_array( $key, array( 'show_disclaimer', 'show_icons', 'show_shadow' ), true ) ) {
				$clean[ $key ] = '1';
			} else {
				$clean[ $key ] = sanitize_text_field( wp_unslash( $val ) );
			}
		}

		// disclaimer_text appears both as a content key and is also used as a
		// dictionary key name above accidentally shadowed; ensure correct value:
		$clean['disclaimer_text'] = isset( $input['disclaimer_text'] ) ? sanitize_textarea_field( wp_unslash( $input['disclaimer_text'] ) ) : $defaults['disclaimer_text'];

		return $clean;
	}

	/** Units-per-mL for the configured syringe type. */
	public static function units_per_ml( $overrides = array() ) {
		$type = self::get( 'syringe_type', $overrides );
		switch ( $type ) {
			case 'u40':
				return 40;
			case 'u50':
				return 50;
			case 'custom':
				return (float) self::get( 'syringe_custom_units_per_ml', $overrides );
			case 'u100':
			default:
				return 100;
		}
	}

	public static function syringe_display_label( $overrides = array() ) {
		$type  = self::get( 'syringe_type', $overrides );
		$label = self::get( 'syringe_label', $overrides );
		$suffix = '';
		switch ( $type ) {
			case 'u40':
				$suffix = ' (U-40)';
				break;
			case 'u50':
				$suffix = ' (U-50)';
				break;
			case 'u100':
				$suffix = ' (U-100)';
				break;
			case 'custom':
				$suffix = '';
				break;
		}
		return $label . $suffix;
	}

	public static function syringe_unit_word( $overrides = array() ) {
		$type = self::get( 'syringe_type', $overrides );
		if ( 'custom' === $type ) {
			$word = self::get( 'syringe_custom_label', $overrides );
			return $word ? $word : 'units';
		}
		return 'units';
	}
}
