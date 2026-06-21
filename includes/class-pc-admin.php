<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PC_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'admin_post_pc_reset_defaults', array( __CLASS__, 'handle_reset' ) );
		add_filter( 'plugin_action_links_' . PC_BASENAME, array( __CLASS__, 'plugin_links' ) );
	}

	public static function menu() {
		add_menu_page(
			__( 'Peptide Calculator', 'peptide-calculator' ),
			__( 'Peptide Calculator', 'peptide-calculator' ),
			'manage_options',
			'peptide-calculator',
			array( __CLASS__, 'render_page' ),
			'dashicons-calculator',
			58
		);
	}

	public static function plugin_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=peptide-calculator' ) . '">' . __( 'Settings', 'peptide-calculator' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public static function register() {
		register_setting(
			'pc_settings_group',
			PC_Settings::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'PC_Settings', 'sanitize' ),
				'default'           => PC_Settings::defaults(),
			)
		);
	}

	public static function assets( $hook ) {
		if ( 'toplevel_page_peptide-calculator' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		PC_Assets::enqueue_frontend();

		wp_enqueue_style( 'pc-admin', PC_URL . 'assets/css/pc-admin.css', array( 'wp-color-picker' ), PC_VERSION );
		wp_enqueue_script( 'pc-admin', PC_URL . 'assets/js/pc-admin.js', array( 'jquery', 'wp-color-picker', 'pc-frontend' ), PC_VERSION, true );
	}

	public static function handle_reset() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'peptide-calculator' ) );
		}
		check_admin_referer( 'pc_reset_defaults' );
		update_option( PC_Settings::OPTION_KEY, PC_Settings::defaults() );
		wp_safe_redirect( add_query_arg( array( 'page' => 'peptide-calculator', 'reset' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/* ---------------------------------------------------------------
	 * Field rendering helpers
	 * ------------------------------------------------------------- */

	private static function field_name( $key ) {
		return PC_Settings::OPTION_KEY . '[' . $key . ']';
	}

	private static function text( $key, $opts, $placeholder = '' ) {
		printf(
			'<input type="text" class="regular-text" name="%1$s" id="pc_%2$s" value="%3$s" placeholder="%4$s" />',
			esc_attr( self::field_name( $key ) ),
			esc_attr( $key ),
			esc_attr( $opts[ $key ] ),
			esc_attr( $placeholder )
		);
	}

	private static function textarea( $key, $opts, $rows = 3 ) {
		printf(
			'<textarea class="large-text" rows="%3$d" name="%1$s" id="pc_%2$s">%4$s</textarea>',
			esc_attr( self::field_name( $key ) ),
			esc_attr( $key ),
			(int) $rows,
			esc_textarea( $opts[ $key ] )
		);
	}

	private static function number( $key, $opts, $min = '', $max = '', $step = '1' ) {
		printf(
			'<input type="number" class="small-text pc-live-number" name="%1$s" id="pc_%2$s" value="%3$s" min="%4$s" max="%5$s" step="%6$s" />',
			esc_attr( self::field_name( $key ) ),
			esc_attr( $key ),
			esc_attr( $opts[ $key ] ),
			esc_attr( $min ),
			esc_attr( $max ),
			esc_attr( $step )
		);
	}

	private static function color( $key, $opts, $allow_alpha = false ) {
		if ( $allow_alpha ) {
			printf(
				'<input type="text" class="regular-text pc-color-field-raw" name="%1$s" id="pc_%2$s" value="%3$s" placeholder="rgba(0,0,0,0.2) or #rrggbb" />',
				esc_attr( self::field_name( $key ) ),
				esc_attr( $key ),
				esc_attr( $opts[ $key ] )
			);
			return;
		}
		printf(
			'<input type="text" class="pc-color-field" name="%1$s" id="pc_%2$s" value="%3$s" data-default-color="%4$s" />',
			esc_attr( self::field_name( $key ) ),
			esc_attr( $key ),
			esc_attr( $opts[ $key ] ),
			esc_attr( PC_Settings::defaults()[ $key ] )
		);
	}

	private static function select( $key, $opts, $choices ) {
		echo '<select name="' . esc_attr( self::field_name( $key ) ) . '" id="pc_' . esc_attr( $key ) . '">';
		foreach ( $choices as $value => $label ) {
			printf(
				'<option value="%1$s" %3$s>%2$s</option>',
				esc_attr( $value ),
				esc_html( $label ),
				selected( $opts[ $key ], $value, false )
			);
		}
		echo '</select>';
	}

	private static function checkbox( $key, $opts, $label ) {
		printf(
			'<label><input type="checkbox" name="%1$s" id="pc_%2$s" value="1" %3$s /> %4$s</label>',
			esc_attr( self::field_name( $key ) ),
			esc_attr( $key ),
			checked( $opts[ $key ], '1', false ),
			esc_html( $label )
		);
	}

	private static function row( $label, $callback, $desc = '', $tr_attr = '' ) {
		echo '<tr ' . $tr_attr . '><th scope="row">' . esc_html( $label ) . '</th><td>'; // phpcs:ignore WordPress.Security.EscapeOutput -- $tr_attr is hardcoded by callers, not user input.
		call_user_func( $callback );
		if ( $desc ) {
			echo '<p class="description">' . esc_html( $desc ) . '</p>';
		}
		echo '</td></tr>';
	}

	/* ---------------------------------------------------------------
	 * Page
	 * ------------------------------------------------------------- */

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$opts = PC_Settings::get_all();
		$elementor_active = did_action( 'elementor/loaded' );
		?>
		<div class="wrap pc-admin-wrap">
			<div class="pc-admin-header">
				<h1><span class="dashicons dashicons-calculator"></span> <?php esc_html_e( 'Peptide Calculator', 'peptide-calculator' ); ?></h1>
				<p class="pc-admin-tagline"><?php esc_html_e( 'Configure the reconstitution calculator used by the shortcode and Elementor widget.', 'peptide-calculator' ); ?></p>
			</div>

			<?php if ( isset( $_GET['settings-updated'] ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'peptide-calculator' ); ?></p></div>
			<?php endif; ?>
			<?php if ( isset( $_GET['reset'] ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings reset to defaults.', 'peptide-calculator' ); ?></p></div>
			<?php endif; ?>

			<div class="pc-usage-box">
				<div>
					<strong><?php esc_html_e( 'Shortcode', 'peptide-calculator' ); ?>:</strong>
					<code>[peptide_calculator]</code>
					<span class="description"><?php esc_html_e( 'Works in any page, post, or builder.', 'peptide-calculator' ); ?></span>
				</div>
				<div>
					<strong><?php esc_html_e( 'Elementor widget', 'peptide-calculator' ); ?>:</strong>
					<?php if ( $elementor_active ) : ?>
						<span class="pc-pill pc-pill-ok"><?php esc_html_e( 'Elementor detected — search “Peptide Calculator” in the widget panel.', 'peptide-calculator' ); ?></span>
					<?php else : ?>
						<span class="pc-pill pc-pill-warn"><?php esc_html_e( 'Elementor not detected. Install Elementor to use the drag-and-drop widget, or use the shortcode above.', 'peptide-calculator' ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<div class="pc-admin-columns">
				<div class="pc-admin-main">
					<form method="post" action="options.php" id="pc-settings-form">
						<?php settings_fields( 'pc_settings_group' ); ?>

						<nav class="nav-tab-wrapper pc-tabs">
							<a href="#" class="nav-tab nav-tab-active" data-tab="content"><?php esc_html_e( 'Content & Labels', 'peptide-calculator' ); ?></a>
							<a href="#" class="nav-tab" data-tab="disclaimer"><?php esc_html_e( 'Disclaimer', 'peptide-calculator' ); ?></a>
							<a href="#" class="nav-tab" data-tab="input-style"><?php esc_html_e( 'Input Card Style', 'peptide-calculator' ); ?></a>
							<a href="#" class="nav-tab" data-tab="results-style"><?php esc_html_e( 'Results Card Style', 'peptide-calculator' ); ?></a>
							<a href="#" class="nav-tab" data-tab="typography"><?php esc_html_e( 'Typography', 'peptide-calculator' ); ?></a>
							<a href="#" class="nav-tab" data-tab="layout"><?php esc_html_e( 'Layout', 'peptide-calculator' ); ?></a>
							<a href="#" class="nav-tab" data-tab="advanced"><?php esc_html_e( 'Advanced', 'peptide-calculator' ); ?></a>
						</nav>

						<div class="pc-tab-panel" data-tab-panel="content">
							<table class="form-table">
								<?php
								self::row( __( 'Calculator title', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'calc_title', $opts ); } );
								self::row( __( 'Vial size label', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'vial_label', $opts ); } );
								self::row( __( 'Default vial size', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'default_vial_mg', $opts, 0, '', 'any' ); } );
								self::row( __( 'Water label', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'water_label', $opts ); } );
								self::row( __( 'Default water volume', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'default_water_ml', $opts, 0, '', 'any' ); } );
								self::row( __( 'Desired dose label', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'dose_label', $opts ); } );
								self::row( __( 'Default desired dose', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'default_dose_mcg', $opts, 0, '', 'any' ); } );
								self::row( __( 'Dose unit', 'peptide-calculator' ), function () use ( $opts ) { self::select( 'dose_unit', $opts, array( 'mcg' => 'mcg', 'mg' => 'mg' ) ); } );
								?>
								<tr><th colspan="2"><h3><?php esc_html_e( 'Results panel', 'peptide-calculator' ); ?></h3></th></tr>
								<?php
								self::row( __( 'Results title', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'results_title', $opts ); } );
								self::row( __( 'Results subtitle', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'results_subtitle', $opts ); } );
								self::row( __( 'Concentration row label', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'concentration_label', $opts ); } );
								self::row( __( 'Injection volume row label', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'volume_label', $opts ); } );
								self::row( __( 'Syringe row label', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'syringe_label', $opts ); } );
								self::row( __( 'Syringe type', 'peptide-calculator' ), function () use ( $opts ) {
									self::select( 'syringe_type', $opts, array(
										'u100'   => __( 'U-100 insulin syringe', 'peptide-calculator' ),
										'u40'    => __( 'U-40 insulin syringe', 'peptide-calculator' ),
										'u50'    => __( 'U-50 insulin syringe', 'peptide-calculator' ),
										'custom' => __( 'Custom', 'peptide-calculator' ),
									) );
								} );
								self::row( __( 'Custom units per mL', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'syringe_custom_units_per_ml', $opts, 1, '', 'any' ); }, __( 'Only used when syringe type is "Custom".', 'peptide-calculator' ), 'data-show-if="syringe_type:custom"' );
								self::row( __( 'Custom unit word', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'syringe_custom_label', $opts ); }, __( 'e.g. "units", "clicks"', 'peptide-calculator' ), 'data-show-if="syringe_type:custom"' );
								self::row( __( 'Decimal places — concentration', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'decimals_concentration', $opts, 0, 4 ); } );
								self::row( __( 'Decimal places — volume', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'decimals_volume', $opts, 0, 4 ); } );
								self::row( __( 'Decimal places — syringe units', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'decimals_units', $opts, 0, 4 ); } );
								?>
							</table>
						</div>

						<div class="pc-tab-panel" data-tab-panel="disclaimer" hidden>
							<table class="form-table">
								<?php
								self::row( __( 'Show disclaimer', 'peptide-calculator' ), function () use ( $opts ) { self::checkbox( 'show_disclaimer', $opts, __( 'Display the disclaimer box', 'peptide-calculator' ) ); } );
								self::row( __( 'Disclaimer text', 'peptide-calculator' ), function () use ( $opts ) { self::textarea( 'disclaimer_text', $opts, 3 ); } );
								self::row( __( 'Disclaimer text color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'disclaimer_text_color', $opts ); } );
								self::row( __( 'Disclaimer background', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'disclaimer_bg', $opts, true ); }, __( 'Supports transparency.', 'peptide-calculator' ) );
								?>
							</table>
						</div>

						<div class="pc-tab-panel" data-tab-panel="input-style" hidden>
							<table class="form-table">
								<?php
								self::row( __( 'Card background', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'input_bg', $opts ); } );
								self::row( __( 'Card border color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'card_border_color', $opts ); } );
								self::row( __( 'Title color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'input_title_color', $opts ); } );
								self::row( __( 'Field label color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'label_color', $opts ); } );
								self::row( __( 'Accent / value badge color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'accent_color', $opts ); } );
								self::row( __( 'Icon color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'icon_color', $opts ); } );
								self::row( __( 'Show field icons', 'peptide-calculator' ), function () use ( $opts ) { self::checkbox( 'show_icons', $opts, __( 'Show droplet / syringe icons', 'peptide-calculator' ) ); } );
								self::row( __( 'Input box border', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'input_border', $opts ); } );
								self::row( __( 'Input text color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'input_text', $opts ); } );
								self::row( __( 'Card shadow', 'peptide-calculator' ), function () use ( $opts ) { self::checkbox( 'show_shadow', $opts, __( 'Show drop shadow on cards', 'peptide-calculator' ) ); } );
								?>
							</table>
						</div>

						<div class="pc-tab-panel" data-tab-panel="results-style" hidden>
							<table class="form-table">
								<?php
								self::row( __( 'Gradient start', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'results_grad_start', $opts ); } );
								self::row( __( 'Gradient end', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'results_grad_end', $opts ); } );
								self::row( __( 'Title color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'results_title_color', $opts ); } );
								self::row( __( 'Subtitle color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'results_subtitle_color', $opts ); } );
								self::row( __( 'Row label color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'results_label_color', $opts ); } );
								self::row( __( 'Value color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'results_value_color', $opts ); } );
								self::row( __( 'Unit text color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'results_unit_color', $opts ); } );
								self::row( __( 'Divider line color', 'peptide-calculator' ), function () use ( $opts ) { self::color( 'divider_color', $opts, true ); }, __( 'Supports transparency.', 'peptide-calculator' ) );
								?>
							</table>
						</div>

						<div class="pc-tab-panel" data-tab-panel="typography" hidden>
							<table class="form-table">
								<?php
								self::row( __( 'Heading font family', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'font_heading', $opts, "Georgia, serif" ); }, __( 'Used for the two card titles. Any valid CSS font-family value.', 'peptide-calculator' ) );
								self::row( __( 'Body font family', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'font_body', $opts, '-apple-system, sans-serif' ); } );
								self::row( __( 'Google Font name (optional)', 'peptide-calculator' ), function () use ( $opts ) { self::text( 'google_font', $opts, 'e.g. Inter' ); }, __( 'If set, this is loaded from Google Fonts and used for the body font.', 'peptide-calculator' ) );
								self::row( __( 'Base font size (px)', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'font_size_base', $opts, 10, 30 ); } );
								self::row( __( 'Title font size (px)', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'font_size_title', $opts, 14, 48 ); } );
								self::row( __( 'Result number font size (px)', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'font_size_result', $opts, 14, 60 ); } );
								?>
							</table>
						</div>

						<div class="pc-tab-panel" data-tab-panel="layout" hidden>
							<table class="form-table">
								<?php
								self::row( __( 'Layout', 'peptide-calculator' ), function () use ( $opts ) {
									self::select( 'layout', $opts, array(
										'row'   => __( 'Side by side', 'peptide-calculator' ),
										'stack' => __( 'Stacked', 'peptide-calculator' ),
									) );
								}, __( 'Side by side automatically stacks on small screens.', 'peptide-calculator' ) );
								self::row( __( 'Card corner radius (px)', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'card_radius', $opts, 0, 60 ); } );
								self::row( __( 'Input corner radius (px)', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'input_radius', $opts, 0, 60 ); } );
								self::row( __( 'Card padding (px)', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'card_padding', $opts, 8, 80 ); } );
								self::row( __( 'Gap between cards (px)', 'peptide-calculator' ), function () use ( $opts ) { self::number( 'card_gap', $opts, 0, 80 ); } );
								?>
							</table>
						</div>

						<div class="pc-tab-panel" data-tab-panel="advanced" hidden>
							<table class="form-table">
								<?php self::row( __( 'Custom CSS', 'peptide-calculator' ), function () use ( $opts ) { self::textarea( 'custom_css', $opts, 8 ); }, __( 'Loaded after all other styles for advanced tweaks.', 'peptide-calculator' ) ); ?>
							</table>
						</div>

						<?php submit_button( __( 'Save Changes', 'peptide-calculator' ) ); ?>
					</form>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="pc-reset-form" onsubmit="return confirm('<?php echo esc_js( __( 'Reset all Peptide Calculator settings to their defaults? This cannot be undone.', 'peptide-calculator' ) ); ?>');">
						<input type="hidden" name="action" value="pc_reset_defaults" />
						<?php wp_nonce_field( 'pc_reset_defaults' ); ?>
						<button type="submit" class="button button-link-delete"><?php esc_html_e( 'Reset to defaults', 'peptide-calculator' ); ?></button>
					</form>
				</div>

				<div class="pc-admin-preview">
					<div class="pc-preview-sticky">
						<h2><?php esc_html_e( 'Live Preview', 'peptide-calculator' ); ?></h2>
						<p class="description"><?php esc_html_e( 'Updates as you edit — save to apply everywhere.', 'peptide-calculator' ); ?></p>
						<div id="pc-live-preview">
							<?php echo PC_Render::render(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
