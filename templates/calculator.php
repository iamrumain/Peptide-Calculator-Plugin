<?php
/**
 * Calculator markup.
 * Available vars (from PC_Render::render): $uid, $get (closure), $layout,
 * $show_icons, $show_shadow, $show_disc, $style_attr, $data, $overrides
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$drop_icon = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5s6.5 7.2 6.5 11.8a6.5 6.5 0 0 1-13 0C5.5 9.7 12 2.5 12 2.5Z"/></svg>';
$dose_icon = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16.5 3.5 4 4-10 10-5 1 1-5 10-10Z"/><path d="m13.5 6.5 4 4"/></svg>';

$syringe_type    = $get( 'syringe_type' );
$syringe_suffixes = array(
	'u40'  => ' (U-40)',
	'u50'  => ' (U-50)',
	'u100' => ' (U-100)',
);
$syringe_suffix = isset( $syringe_suffixes[ $syringe_type ] ) ? $syringe_suffixes[ $syringe_type ] : '';
?>
<div id="<?php echo esc_attr( $uid ); ?>" class="pc-calculator <?php echo esc_attr( $layout ); ?><?php echo $show_shadow ? ' pc-shadow' : ''; ?><?php echo $show_icons ? '' : ' pc-icons-hidden'; ?>" style="<?php echo $style_attr; ?>"
	data-vial="<?php echo esc_attr( $data['vial'] ); ?>"
	data-water="<?php echo esc_attr( $data['water'] ); ?>"
	data-dose="<?php echo esc_attr( $data['dose'] ); ?>"
	data-units-per-ml="<?php echo esc_attr( $data['unitsPerMl'] ); ?>"
	data-unit-word="<?php echo esc_attr( $data['unitWord'] ); ?>"
	data-dec-conc="<?php echo esc_attr( $data['decConc'] ); ?>"
	data-dec-vol="<?php echo esc_attr( $data['decVol'] ); ?>"
	data-dec-units="<?php echo esc_attr( $data['decUnits'] ); ?>"
	data-dose-unit="<?php echo esc_attr( $data['doseUnit'] ); ?>"
>
	<div class="pc-panel pc-panel-inputs">
		<h3 class="pc-calc-title" data-label="calc_title"><?php echo esc_html( $get( 'calc_title' ) ); ?></h3>

		<div class="pc-field">
			<div class="pc-field-row">
				<span class="pc-field-label">
					<span class="pc-icon"><?php echo $drop_icon; ?></span>
					<span data-label="vial_label"><?php echo esc_html( $get( 'vial_label' ) ); ?></span>
				</span>
				<span class="pc-field-value" data-readout="vial"><?php echo esc_html( $data['vial'] ); ?></span>
			</div>
			<input type="number" inputmode="decimal" step="any" min="0" class="pc-input" data-field="vial" value="<?php echo esc_attr( $data['vial'] ); ?>" />
		</div>

		<div class="pc-field">
			<div class="pc-field-row">
				<span class="pc-field-label">
					<span class="pc-icon"><?php echo $drop_icon; ?></span>
					<span data-label="water_label"><?php echo esc_html( $get( 'water_label' ) ); ?></span>
				</span>
				<span class="pc-field-value" data-readout="water"><?php echo esc_html( $data['water'] ); ?></span>
			</div>
			<input type="number" inputmode="decimal" step="any" min="0" class="pc-input" data-field="water" value="<?php echo esc_attr( $data['water'] ); ?>" />
		</div>

		<div class="pc-field">
			<div class="pc-field-row">
				<span class="pc-field-label">
					<span class="pc-icon"><?php echo $dose_icon; ?></span>
					<span data-label="dose_label"><?php echo esc_html( $get( 'dose_label' ) ); ?></span>
				</span>
				<span class="pc-field-value" data-readout="dose"><?php echo esc_html( $data['dose'] ); ?></span>
			</div>
			<input type="number" inputmode="decimal" step="any" min="0" class="pc-input" data-field="dose" value="<?php echo esc_attr( $data['dose'] ); ?>" />
		</div>
	</div>

	<div class="pc-panel pc-panel-results">
		<h3 class="pc-results-title" data-label="results_title"><?php echo esc_html( $get( 'results_title' ) ); ?></h3>
		<p class="pc-results-subtitle" data-label="results_subtitle"><?php echo esc_html( $get( 'results_subtitle' ) ); ?></p>

		<div class="pc-result-row">
			<span class="pc-result-label" data-label="concentration_label"><?php echo esc_html( $get( 'concentration_label' ) ); ?></span>
			<span class="pc-result-value"><span data-result="concentration">0</span> <span class="pc-result-unit" data-label="concentration_unit"><?php echo esc_html( 'mcg' === $data['doseUnit'] ? 'mcg/mL' : 'mg/mL' ); ?></span></span>
		</div>
		<div class="pc-divider"></div>
		<div class="pc-result-row">
			<span class="pc-result-label" data-label="volume_label"><?php echo esc_html( $get( 'volume_label' ) ); ?></span>
			<span class="pc-result-value"><span data-result="volume">0</span> <span class="pc-result-unit">mL</span></span>
		</div>
		<div class="pc-divider"></div>
		<div class="pc-result-row">
			<span class="pc-result-label"><span data-label="syringe_label"><?php echo esc_html( $get( 'syringe_label' ) ); ?></span><span data-syringe-suffix><?php echo esc_html( $syringe_suffix ); ?></span></span>
			<span class="pc-result-value"><span data-result="units">0</span> <span class="pc-result-unit" data-unit-word-display><?php echo esc_html( $data['unitWord'] ); ?></span></span>
		</div>

		<div class="pc-disclaimer" data-label="disclaimer_text" <?php echo $show_disc && $get( 'disclaimer_text' ) ? '' : 'style="display:none"'; ?>><?php echo esc_html( $get( 'disclaimer_text' ) ); ?></div>
	</div>
</div>
