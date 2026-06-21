/**
 * Peptide Calculator — frontend logic.
 *
 * Formula:
 *   concentration (per mL) = (vial_amount * 1000ish-aware) / water_mL
 *     - if dose unit is mcg, vial mg is converted to mcg (×1000)
 *     - if dose unit is mg, vial mg stays as mg
 *   injection volume (mL)  = desired_dose / concentration
 *   syringe units           = injection volume (mL) × units-per-mL
 */
( function () {
	'use strict';

	function formatNum( n, decimals ) {
		if ( ! isFinite( n ) ) {
			return '0';
		}
		return n.toLocaleString( undefined, {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
			useGrouping: false,
		} );
	}

	function calc( el ) {
		var vialInput  = el.querySelector( '[data-field="vial"]' );
		var waterInput = el.querySelector( '[data-field="water"]' );
		var doseInput  = el.querySelector( '[data-field="dose"]' );

		var vial  = parseFloat( vialInput.value );
		var water = parseFloat( waterInput.value );
		var dose  = parseFloat( doseInput.value );

		var doseUnit   = el.dataset.doseUnit || 'mcg';
		var unitsPerMl = parseFloat( el.dataset.unitsPerMl ) || 100;
		var decConc    = parseInt( el.dataset.decConc, 10 );
		var decVol     = parseInt( el.dataset.decVol, 10 );
		var decUnits   = parseInt( el.dataset.decUnits, 10 );
		if ( isNaN( decConc ) ) decConc = 1;
		if ( isNaN( decVol ) ) decVol = 3;
		if ( isNaN( decUnits ) ) decUnits = 1;

		var vialInDoseUnit = vial;
		if ( 'mcg' === doseUnit ) {
			vialInDoseUnit = vial * 1000; // mg -> mcg
		}

		var concentration = 0;
		var volume = 0;
		var units = 0;

		if ( water > 0 ) {
			concentration = vialInDoseUnit / water;
		}
		if ( concentration > 0 ) {
			volume = dose / concentration;
			units  = volume * unitsPerMl;
		}

		setText( el, 'concentration', formatNum( concentration, decConc ) );
		setText( el, 'volume', formatNum( volume, decVol ) );
		setText( el, 'units', formatNum( units, decUnits ) );

		setText( el, 'vial', formatNum( vial, vial % 1 === 0 ? 0 : 2 ), '[data-readout="vial"]' );
		setText( el, 'water', formatNum( water, water % 1 === 0 ? 0 : 2 ), '[data-readout="water"]' );
		setText( el, 'dose', formatNum( dose, dose % 1 === 0 ? 0 : 2 ), '[data-readout="dose"]' );
	}

	function setText( el, key, value, selectorOverride ) {
		var selector = selectorOverride || '[data-result="' + key + '"]';
		var target = el.querySelector( selector );
		if ( target ) {
			target.textContent = value;
		}
	}

	function init( el ) {
		var inputs = el.querySelectorAll( '.pc-input' );
		inputs.forEach( function ( input ) {
			input.addEventListener( 'input', function () {
				calc( el );
			} );
		} );
		calc( el );
	}

	function boot() {
		document.querySelectorAll( '.pc-calculator' ).forEach( init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}

	// Expose for Elementor editor preview refreshes.
	window.pcInitCalculators = boot;

	// Expose recalc-only (no re-binding) for the admin live preview.
	window.pcRecalcAll = function () {
		document.querySelectorAll( '.pc-calculator' ).forEach( calc );
	};
	window.pcRecalcOne = calc;
} )();
