<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PC_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'peptide_calculator';
	}

	public function get_title() {
		return __( 'Peptide Calculator', 'peptide-calculator' );
	}

	public function get_icon() {
		return 'eicon-calculator';
	}

	public function get_categories() {
		return array( 'peptide-calculator', 'general' );
	}

	public function get_keywords() {
		return array( 'peptide', 'calculator', 'reconstitution', 'dosage', 'syringe' );
	}

	/** Options helper: 'Use dashboard default' first, then explicit choices. */
	private function inherit_options( $choices ) {
		return array( '' => __( '— Use dashboard default —', 'peptide-calculator' ) ) + $choices;
	}

	protected function register_controls() {

		/* =====================================================
		 * CONTENT TAB
		 * ===================================================== */

		$this->start_controls_section(
			'sec_content_inputs',
			array(
				'label' => __( 'Calculator', 'peptide-calculator' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control( 'calc_title', array(
			'label'       => __( 'Title', 'peptide-calculator' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'placeholder' => __( 'Reconstitution Calculator', 'peptide-calculator' ),
			'default'     => '',
		) );

		$this->add_control( 'vial_label', array(
			'label'   => __( 'Vial size label', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );
		$this->add_control( 'default_vial_mg', array(
			'label'   => __( 'Default vial size (mg)', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );

		$this->add_control( 'water_label', array(
			'label'   => __( 'Water label', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );
		$this->add_control( 'default_water_ml', array(
			'label'   => __( 'Default water volume (mL)', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );

		$this->add_control( 'dose_label', array(
			'label'   => __( 'Desired dose label', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );
		$this->add_control( 'default_dose_mcg', array(
			'label'   => __( 'Default desired dose', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'sec_content_results',
			array(
				'label' => __( 'Results', 'peptide-calculator' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control( 'results_title', array(
			'label'   => __( 'Results title', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );
		$this->add_control( 'results_subtitle', array(
			'label'   => __( 'Results subtitle', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );
		$this->add_control( 'concentration_label', array(
			'label'   => __( 'Concentration row label', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );
		$this->add_control( 'volume_label', array(
			'label'   => __( 'Injection volume row label', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );
		$this->add_control( 'syringe_label', array(
			'label'   => __( 'Syringe row label', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );
		$this->add_control( 'syringe_type', array(
			'label'   => __( 'Syringe type', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => $this->inherit_options( array(
				'u100'   => __( 'U-100 insulin syringe', 'peptide-calculator' ),
				'u40'    => __( 'U-40 insulin syringe', 'peptide-calculator' ),
				'u50'    => __( 'U-50 insulin syringe', 'peptide-calculator' ),
				'custom' => __( 'Custom', 'peptide-calculator' ),
			) ),
		) );
		$this->add_control( 'syringe_custom_units_per_ml', array(
			'label'     => __( 'Custom units per mL', 'peptide-calculator' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => '',
			'condition' => array( 'syringe_type' => 'custom' ),
		) );
		$this->add_control( 'syringe_custom_label', array(
			'label'     => __( 'Custom unit word', 'peptide-calculator' ),
			'type'      => \Elementor\Controls_Manager::TEXT,
			'default'   => '',
			'condition' => array( 'syringe_type' => 'custom' ),
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'sec_content_disclaimer',
			array(
				'label' => __( 'Disclaimer', 'peptide-calculator' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control( 'show_disclaimer_sel', array(
			'label'   => __( 'Show disclaimer', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => $this->inherit_options( array(
				'1' => __( 'Show', 'peptide-calculator' ),
				'0' => __( 'Hide', 'peptide-calculator' ),
			) ),
		) );
		$this->add_control( 'disclaimer_text', array(
			'label'   => __( 'Disclaimer text', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => '',
		) );

		$this->end_controls_section();

		/* =====================================================
		 * STYLE TAB — Input card
		 * ===================================================== */

		$this->start_controls_section(
			'sec_style_input',
			array(
				'label' => __( 'Input Card', 'peptide-calculator' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		foreach ( array(
			'input_bg'          => __( 'Background', 'peptide-calculator' ),
			'card_border_color' => __( 'Border color', 'peptide-calculator' ),
			'input_title_color' => __( 'Title color', 'peptide-calculator' ),
			'label_color'       => __( 'Field label color', 'peptide-calculator' ),
			'accent_color'      => __( 'Accent / value color', 'peptide-calculator' ),
			'icon_color'        => __( 'Icon color', 'peptide-calculator' ),
			'input_border'      => __( 'Input box border', 'peptide-calculator' ),
			'input_text'        => __( 'Input text color', 'peptide-calculator' ),
		) as $key => $label ) {
			$this->add_control( $key, array(
				'label'   => $label,
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '',
			) );
		}

		$this->add_control( 'show_icons_sel', array(
			'label'   => __( 'Show field icons', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => $this->inherit_options( array(
				'1' => __( 'Show', 'peptide-calculator' ),
				'0' => __( 'Hide', 'peptide-calculator' ),
			) ),
		) );

		$this->end_controls_section();

		/* =====================================================
		 * STYLE TAB — Results card
		 * ===================================================== */

		$this->start_controls_section(
			'sec_style_results',
			array(
				'label' => __( 'Results Card', 'peptide-calculator' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		foreach ( array(
			'results_grad_start'     => __( 'Gradient start', 'peptide-calculator' ),
			'results_grad_end'       => __( 'Gradient end', 'peptide-calculator' ),
			'results_title_color'    => __( 'Title color', 'peptide-calculator' ),
			'results_subtitle_color' => __( 'Subtitle color', 'peptide-calculator' ),
			'results_label_color'    => __( 'Row label color', 'peptide-calculator' ),
			'results_value_color'    => __( 'Value color', 'peptide-calculator' ),
			'results_unit_color'     => __( 'Unit text color', 'peptide-calculator' ),
			'disclaimer_text_color'  => __( 'Disclaimer text color', 'peptide-calculator' ),
		) as $key => $label ) {
			$this->add_control( $key, array(
				'label'   => $label,
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '',
			) );
		}

		$this->add_control( 'divider_color', array(
			'label'   => __( 'Divider line color', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'alpha'   => true,
		) );
		$this->add_control( 'disclaimer_bg', array(
			'label'   => __( 'Disclaimer background', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'alpha'   => true,
		) );

		$this->end_controls_section();

		/* =====================================================
		 * STYLE TAB — Typography & Layout
		 * ===================================================== */

		$this->start_controls_section(
			'sec_style_type_layout',
			array(
				'label' => __( 'Typography & Layout', 'peptide-calculator' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control( 'font_heading', array(
			'label'       => __( 'Heading font family', 'peptide-calculator' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => 'Georgia, serif',
		) );
		$this->add_control( 'font_body', array(
			'label'       => __( 'Body font family', 'peptide-calculator' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => '-apple-system, sans-serif',
		) );
		$this->add_control( 'google_font', array(
			'label'       => __( 'Google Font name (optional)', 'peptide-calculator' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => 'e.g. Inter',
		) );
		$this->add_control( 'font_size_base', array(
			'label'   => __( 'Base font size (px)', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );
		$this->add_control( 'font_size_title', array(
			'label'   => __( 'Title font size (px)', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );
		$this->add_control( 'font_size_result', array(
			'label'   => __( 'Result number font size (px)', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );

		$this->add_control( 'layout', array(
			'label'   => __( 'Layout', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => $this->inherit_options( array(
				'row'   => __( 'Side by side', 'peptide-calculator' ),
				'stack' => __( 'Stacked', 'peptide-calculator' ),
			) ),
		) );
		$this->add_control( 'show_shadow_sel', array(
			'label'   => __( 'Card shadow', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => $this->inherit_options( array(
				'1' => __( 'Show', 'peptide-calculator' ),
				'0' => __( 'Hide', 'peptide-calculator' ),
			) ),
		) );
		$this->add_control( 'card_radius', array(
			'label'   => __( 'Card corner radius (px)', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );
		$this->add_control( 'input_radius', array(
			'label'   => __( 'Input corner radius (px)', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );
		$this->add_control( 'card_padding', array(
			'label'   => __( 'Card padding (px)', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );
		$this->add_control( 'card_gap', array(
			'label'   => __( 'Gap between cards (px)', 'peptide-calculator' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
		) );

		$this->end_controls_section();
	}

	protected function render() {
		PC_Assets::enqueue_frontend();

		$s = $this->get_settings_for_display();

		// Translate the inherit-aware SELECT proxies back onto the real setting keys.
		$overrides = $s;
		if ( isset( $s['show_disclaimer_sel'] ) && $s['show_disclaimer_sel'] !== '' ) {
			$overrides['show_disclaimer'] = $s['show_disclaimer_sel'];
		}
		if ( isset( $s['show_icons_sel'] ) && $s['show_icons_sel'] !== '' ) {
			$overrides['show_icons'] = $s['show_icons_sel'];
		}
		if ( isset( $s['show_shadow_sel'] ) && $s['show_shadow_sel'] !== '' ) {
			$overrides['show_shadow'] = $s['show_shadow_sel'];
		}

		// Strip Elementor's own internal/unrelated keys so they can't collide.
		foreach ( array( '_id', '_title', '_element_id', '_css_classes' ) as $k ) {
			unset( $overrides[ $k ] );
		}

		echo PC_Render::render( $overrides ); // phpcs:ignore WordPress.Security.EscapeOutput -- PC_Render escapes internally.
	}
}
