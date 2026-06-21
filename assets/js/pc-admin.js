/* global jQuery */
( function ( $ ) {
	'use strict';

	var CSS_VAR_FIELDS = {
		input_bg: '--pc-input-bg',
		card_border_color: '--pc-card-border-color',
		input_title_color: '--pc-input-title-color',
		label_color: '--pc-label-color',
		accent_color: '--pc-accent-color',
		icon_color: '--pc-icon-color',
		input_border: '--pc-input-border',
		input_text: '--pc-input-text',
		results_grad_start: '--pc-results-grad-start',
		results_grad_end: '--pc-results-grad-end',
		results_title_color: '--pc-results-title-color',
		results_subtitle_color: '--pc-results-subtitle-color',
		results_label_color: '--pc-results-label-color',
		results_value_color: '--pc-results-value-color',
		results_unit_color: '--pc-results-unit-color',
		divider_color: '--pc-divider-color',
		disclaimer_bg: '--pc-disclaimer-bg',
		disclaimer_text_color: '--pc-disclaimer-text',
		font_heading: '--pc-font-heading'
	};

	var CSS_VAR_PX_FIELDS = {
		font_size_base: '--pc-font-size-base',
		font_size_title: '--pc-font-size-title',
		font_size_result: '--pc-font-size-result',
		card_radius: '--pc-radius-card',
		input_radius: '--pc-radius-input',
		card_padding: '--pc-padding',
		card_gap: '--pc-gap'
	};

	var TEXT_FIELDS = [
		'calc_title', 'vial_label', 'water_label', 'dose_label',
		'results_title', 'results_subtitle',
		'concentration_label', 'volume_label', 'syringe_label',
		'disclaimer_text'
	];

	var SYRINGE_SUFFIX = { u100: ' (U-100)', u40: ' (U-40)', u50: ' (U-50)', custom: '' };

	$( function () {
		var $form = $( '#pc-settings-form' );
		if ( ! $form.length ) {
			return;
		}
		var $preview = $( '#pc-live-preview' );
		var calcEl = $preview.find( '.pc-calculator' ).get( 0 );

		initTabs();
		initColorPickers();

		if ( calcEl ) {
			bindCssVarFields();
			bindPxFields();
			bindTextFields();
			bindToggles();
			bindLayout();
			bindSyringe();
			bindDoseUnit();
			bindDefaults();
			bindDecimals();
			bindGoogleFont();
			bindCustomCss();
		}

		applyConditionals();
		$form.on( 'change input', '[data-show-if]', applyConditionals );
		$form.on( 'change', '#pc_syringe_type', applyConditionals );

		/* ---------------- helpers ---------------- */

		function field( key ) {
			return document.getElementById( 'pc_' + key );
		}

		function setVar( name, value ) {
			if ( calcEl ) {
				calcEl.style.setProperty( name, value );
			}
		}

		function setText( key, value ) {
			if ( ! calcEl ) return;
			var el = calcEl.querySelector( '[data-label="' + key + '"]' );
			if ( el ) {
				el.textContent = value;
			}
		}

		function recalc() {
			if ( window.pcRecalcAll ) {
				window.pcRecalcAll();
			}
		}

		function initTabs() {
			$( '.pc-tabs a' ).on( 'click', function ( e ) {
				e.preventDefault();
				var tab = $( this ).data( 'tab' );
				$( '.pc-tabs a' ).removeClass( 'nav-tab-active' );
				$( this ).addClass( 'nav-tab-active' );
				$( '.pc-tab-panel' ).attr( 'hidden', true );
				$( '[data-tab-panel="' + tab + '"]' ).removeAttr( 'hidden' );
			} );
		}

		function initColorPickers() {
			if ( ! $.fn.wpColorPicker ) {
				return;
			}
			$( '.pc-color-field' ).each( function () {
				var key = $( this ).attr( 'id' ).replace( 'pc_', '' );
				$( this ).wpColorPicker( {
					change: function ( event, ui ) {
						applyColor( key, ui.color.toString() );
					},
					clear: function () {
						applyColor( key, '' );
					}
				} );
			} );
		}

		function applyColor( key, value ) {
			if ( CSS_VAR_FIELDS[ key ] ) {
				setVar( CSS_VAR_FIELDS[ key ], value );
			}
		}

		function bindCssVarFields() {
			Object.keys( CSS_VAR_FIELDS ).forEach( function ( key ) {
				var el = field( key );
				if ( ! el || el.classList.contains( 'pc-color-field' ) ) {
					return; // color pickers handled separately
				}
				var handler = function () {
					setVar( CSS_VAR_FIELDS[ key ], el.value );
				};
				el.addEventListener( 'input', handler );
				el.addEventListener( 'change', handler );
			} );

			// raw alpha-capable color fields (plain text inputs)
			$( '.pc-color-field-raw' ).each( function () {
				var key = $( this ).attr( 'id' ).replace( 'pc_', '' );
				if ( ! CSS_VAR_FIELDS[ key ] ) return;
				this.addEventListener( 'input', function () {
					setVar( CSS_VAR_FIELDS[ key ], this.value );
				} );
			} );

			// font_body: combine with google font live too
			var bodyEl = field( 'font_body' );
			if ( bodyEl ) {
				bodyEl.addEventListener( 'input', function () {
					setVar( '--pc-font-body', bodyEl.value );
				} );
			}
		}

		function bindPxFields() {
			Object.keys( CSS_VAR_PX_FIELDS ).forEach( function ( key ) {
				var el = field( key );
				if ( ! el ) return;
				var handler = function () {
					var v = el.value === '' ? '0' : el.value;
					setVar( CSS_VAR_PX_FIELDS[ key ], v + 'px' );
				};
				el.addEventListener( 'input', handler );
				el.addEventListener( 'change', handler );
			} );
		}

		function bindTextFields() {
			TEXT_FIELDS.forEach( function ( key ) {
				var el = field( key );
				if ( ! el ) return;
				var handler = function () {
					setText( key, el.value );
				};
				el.addEventListener( 'input', handler );
			} );
		}

		function bindToggles() {
			var showIcons = field( 'show_icons' );
			if ( showIcons ) {
				showIcons.addEventListener( 'change', function () {
					calcEl.classList.toggle( 'pc-icons-hidden', ! showIcons.checked );
				} );
			}
			var showShadow = field( 'show_shadow' );
			if ( showShadow ) {
				showShadow.addEventListener( 'change', function () {
					calcEl.classList.toggle( 'pc-shadow', showShadow.checked );
				} );
			}
			var showDisclaimer = field( 'show_disclaimer' );
			var disclaimerEl = calcEl.querySelector( '[data-label="disclaimer_text"]' );
			if ( showDisclaimer && disclaimerEl ) {
				showDisclaimer.addEventListener( 'change', function () {
					disclaimerEl.style.display = showDisclaimer.checked ? '' : 'none';
				} );
			}
		}

		function bindLayout() {
			var layoutEl = field( 'layout' );
			if ( ! layoutEl ) return;
			layoutEl.addEventListener( 'change', function () {
				calcEl.classList.remove( 'pc-layout-row', 'pc-layout-stack' );
				calcEl.classList.add( layoutEl.value === 'stack' ? 'pc-layout-stack' : 'pc-layout-row' );
			} );
		}

		function bindSyringe() {
			var typeEl = field( 'syringe_type' );
			var unitsEl = field( 'syringe_custom_units_per_ml' );
			var labelEl = field( 'syringe_custom_label' );
			var suffixEl = calcEl.querySelector( '[data-syringe-suffix]' );
			var unitWordEl = calcEl.querySelector( '[data-unit-word-display]' );

			function apply() {
				var type = typeEl ? typeEl.value : 'u100';
				if ( suffixEl ) {
					suffixEl.textContent = SYRINGE_SUFFIX[ type ] || '';
				}
				var unitsPerMl = 100;
				var unitWord = 'units';
				if ( type === 'u40' ) unitsPerMl = 40;
				else if ( type === 'u50' ) unitsPerMl = 50;
				else if ( type === 'custom' ) {
					unitsPerMl = parseFloat( unitsEl && unitsEl.value ) || 1;
					unitWord = ( labelEl && labelEl.value ) || 'units';
				}
				calcEl.dataset.unitsPerMl = unitsPerMl;
				calcEl.dataset.unitWord = unitWord;
				if ( unitWordEl ) {
					unitWordEl.textContent = unitWord;
				}
				recalc();
			}

			[ typeEl, unitsEl, labelEl ].forEach( function ( el ) {
				if ( el ) {
					el.addEventListener( 'input', apply );
					el.addEventListener( 'change', apply );
				}
			} );
		}

		function bindDoseUnit() {
			var el = field( 'dose_unit' );
			var unitLabel = calcEl.querySelector( '[data-label="concentration_unit"]' );
			if ( ! el ) return;
			el.addEventListener( 'change', function () {
				calcEl.dataset.doseUnit = el.value;
				if ( unitLabel ) {
					unitLabel.textContent = el.value === 'mcg' ? 'mcg/mL' : 'mg/mL';
				}
				recalc();
			} );
		}

		function bindDefaults() {
			var map = { default_vial_mg: 'vial', default_water_ml: 'water', default_dose_mcg: 'dose' };
			Object.keys( map ).forEach( function ( key ) {
				var el = field( key );
				if ( ! el ) return;
				el.addEventListener( 'input', function () {
					var fieldKey = map[ key ];
					var input = calcEl.querySelector( '[data-field="' + fieldKey + '"]' );
					var readout = calcEl.querySelector( '[data-readout="' + fieldKey + '"]' );
					if ( input ) input.value = el.value;
					if ( readout ) readout.textContent = el.value;
					calcEl.dataset[ fieldKey ] = el.value;
					recalc();
				} );
			} );
		}

		function bindDecimals() {
			var map = {
				decimals_concentration: 'decConc',
				decimals_volume: 'decVol',
				decimals_units: 'decUnits'
			};
			Object.keys( map ).forEach( function ( key ) {
				var el = field( key );
				if ( ! el ) return;
				el.addEventListener( 'input', function () {
					calcEl.dataset[ map[ key ] ] = el.value;
					recalc();
				} );
			} );
		}

		function bindGoogleFont() {
			var el = field( 'google_font' );
			var bodyEl = field( 'font_body' );
			if ( ! el ) return;
			el.addEventListener( 'change', function () {
				var name = el.value.trim();
				var linkId = 'pc-preview-google-font';
				var existing = document.getElementById( linkId );
				if ( existing ) {
					existing.remove();
				}
				if ( name ) {
					var link = document.createElement( 'link' );
					link.id = linkId;
					link.rel = 'stylesheet';
					link.href = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent( name ) + ':wght@400;600;700&display=swap';
					document.head.appendChild( link );
					var fallback = bodyEl ? bodyEl.value : '-apple-system, sans-serif';
					setVar( '--pc-font-body', "'" + name + "', " + fallback );
				} else if ( bodyEl ) {
					setVar( '--pc-font-body', bodyEl.value );
				}
			} );
		}

		function bindCustomCss() {
			var el = field( 'custom_css' );
			if ( ! el ) return;
			var styleId = 'pc-preview-custom-css';
			el.addEventListener( 'input', function () {
				var style = document.getElementById( styleId );
				if ( ! style ) {
					style = document.createElement( 'style' );
					style.id = styleId;
					$preview.append( style );
				}
				style.textContent = el.value;
			} );
		}

		function applyConditionals() {
			$form.find( '[data-show-if]' ).each( function () {
				var cond = $( this ).attr( 'data-show-if' ).split( ':' );
				var input = field( cond[ 0 ] );
				var match = input && input.value === cond[ 1 ];
				$( this ).toggle( !! match );
			} );
		}
	} );
} )( jQuery );
