<?php
/**
 * Render: ci/map — location map behind a light "facade".
 *
 * The page shows a static image of the map (chosen in the block) with an animated marker. The
 * interactive map (MapLibre + OpenFreeMap) is downloaded only when the visitor taps "Explore
 * map", so it does not affect the page speed. Without coordinates there is no interactive map;
 * without images the card sits on a plain background.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$site  = CI_Theme_Section::site();
$place = isset( $attributes['place'] ) && '' !== trim( (string) $attributes['place'] ) ? trim( (string) $attributes['place'] ) : (string) $site['location'];
$text  = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};
$lat   = isset( $attributes['lat'] ) ? (float) $attributes['lat'] : 0.0;
$lng   = isset( $attributes['lng'] ) ? (float) $attributes['lng'] : 0.0;
$zoom  = isset( $attributes['zoom'] ) ? (float) $attributes['zoom'] : 12.0;

$has_coords = 0.0 !== $lat || 0.0 !== $lng;
if ( '' === $place && ! $has_coords ) {
	if ( CI_Theme_Content::is_preview() ) {
		echo '<p class="ci-map-missing">' . esc_html__( 'Set the place or the coordinates of the map in the block settings.', 'ci-theme' ) . '</p>';
	}
	return;
}

$uid     = wp_unique_id( 'ci-map-' );
$route   = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $has_coords ? $lat . ',' . $lng : $place );
$desktop = ! empty( $attributes['imageDesktop'] ) ? wp_get_attachment_image_url( (int) $attributes['imageDesktop'], 'full' ) : '';
$mobile  = ! empty( $attributes['imageMobile'] ) ? wp_get_attachment_image_url( (int) $attributes['imageMobile'], 'full' ) : '';

// Texts of the map controls (MapLibre locale keys).
$labels = array(
	'CooperativeGesturesHandler.WindowsHelpText' => __( 'Use Ctrl + scroll to zoom the map', 'ci-theme' ),
	'CooperativeGesturesHandler.MacHelpText'     => __( 'Use ⌘ + scroll to zoom the map', 'ci-theme' ),
	'CooperativeGesturesHandler.MobileHelpText'  => __( 'Use two fingers to move the map', 'ci-theme' ),
	'NavigationControl.ZoomIn'                   => __( 'Zoom in', 'ci-theme' ),
	'NavigationControl.ZoomOut'                  => __( 'Zoom out', 'ci-theme' ),
	'AttributionControl.ToggleAttribution'       => __( 'Toggle attribution', 'ci-theme' ),
);

// Marker: arc, disc and bar with a pulse behind; the motion script animates it.
$marker = sprintf(
	'<span class="ci-map-marker" aria-hidden="true"><span class="ci-map-pulse"></span><svg data-area-icon viewBox="0 0 100 100" class="ci-map-logo"><circle cx="50" cy="50" r="50" fill="#FFFFFF"/><path data-icon="draw" d="M 73.04 26.96 A 32.6 32.6 0 1 0 73.04 73.04" fill="none" stroke="%1$s" stroke-width="12"/><circle data-icon="pop" cx="50" cy="50" r="15" fill="%2$s"/><rect data-icon="grow-up" x="78" y="31" width="7" height="38" fill="%3$s"/></svg></span>',
	esc_attr( CI_Theme_Section::hex( 'navy' ) ),
	esc_attr( CI_Theme_Section::hex( 'yellow' ) ),
	esc_attr( CI_Theme_Section::hex( 'red-bright' ) )
);
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-map-section' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?> aria-label="<?php esc_attr_e( 'Location', 'ci-theme' ); ?>">
	<div class="ci-container">
		<div class="ci-map<?php echo $desktop ? '' : ' ci-map--plain'; ?>" data-ci-map data-state="image" data-lat="<?php echo esc_attr( (string) $lat ); ?>" data-lng="<?php echo esc_attr( (string) $lng ); ?>" data-zoom="<?php echo esc_attr( (string) $zoom ); ?>" data-worker="<?php echo esc_url( CI_THEME_URI . 'assets/map/maplibre-gl-worker.js' ); ?>" data-loading="<?php echo esc_attr( $text( 'loadingLabel' ) ); ?>" data-error="<?php echo esc_attr( $text( 'errorLabel' ) ); ?>" data-labels="<?php echo esc_attr( wp_json_encode( $labels ) ); ?>">
			<?php if ( $desktop ) : ?>
				<?php /* Static preview, without any map JavaScript. */ ?>
				<picture class="ci-map-preview">
					<?php if ( $mobile ) : ?>
						<source media="(max-width: 699px)" srcset="<?php echo esc_url( $mobile ); ?>">
					<?php endif; ?>
					<img src="<?php echo esc_url( $desktop ); ?>" alt="" loading="lazy" decoding="async">
				</picture>
			<?php endif; ?>

			<?php if ( $has_coords ) : ?>
				<?php /* Interactive map container, filled when the visitor taps "Explore map". */ ?>
				<div class="ci-map-canvas" role="region" aria-label="<?php /* translators: %s: place. */ echo esc_attr( sprintf( __( 'Interactive map of %s', 'ci-theme' ), $place ) ); ?>" data-ci-map-canvas></div>
			<?php endif; ?>

			<div class="ci-map-center" data-ci-map-center><?php echo $marker; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></div>
			<template data-ci-map-marker><?php echo $marker; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></template>

			<?php if ( $has_coords && '' !== $text( 'exploreLabel' ) ) : ?>
				<button type="button" class="ci-map-explore" data-ci-map-explore><?php echo esc_html( $text( 'exploreLabel' ) ); ?></button>
			<?php endif; ?>

			<div class="ci-map-card">
				<span class="ci-map-dot" aria-hidden="true"></span>
				<span class="ci-map-text">
					<span class="ci-map-title"><?php echo esc_html( $place ); ?></span>
					<?php if ( '' !== $text( 'subtitle' ) ) : ?>
						<span class="ci-map-sub"><?php echo esc_html( $text( 'subtitle' ) ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $text( 'directionsLabel' ) ) : ?>
						<a href="<?php echo esc_url( $route ); ?>" target="_blank" rel="noopener noreferrer" class="ci-map-link">
							<?php echo esc_html( $text( 'directionsLabel' ) ); ?> <?php echo CI_Theme_Section::external_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							<span class="visually-hidden"> <?php esc_html_e( '(opens Google Maps in a new tab)', 'ci-theme' ); ?></span>
						</a>
					<?php endif; ?>
				</span>
			</div>

			<?php if ( $has_coords ) : ?>
				<?php /* Map data credits (required by the license), folded behind a button. */ ?>
				<div class="ci-map-credits">
					<button type="button" class="ci-map-info" aria-expanded="false" aria-controls="<?php echo esc_attr( $uid ); ?>-credits" aria-label="<?php esc_attr_e( 'Map credits', 'ci-theme' ); ?>" data-ci-map-info>
						<svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false"><circle cx="8" cy="8" r="7" fill="none" stroke="currentColor" stroke-width="1.4"/><circle cx="8" cy="4.9" r="1" fill="currentColor"/><path d="M8 7.2 V11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
					</button>
					<p id="<?php echo esc_attr( $uid ); ?>-credits" class="ci-map-credits-text" hidden>
						<a href="https://openfreemap.org" target="_blank" rel="noopener noreferrer">OpenFreeMap</a> ·
						<a href="https://www.openmaptiles.org/" target="_blank" rel="noopener noreferrer">© OpenMapTiles</a> ·
						<?php esc_html_e( 'Data ©', 'ci-theme' ); ?>
						<a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'OpenStreetMap contributors', 'ci-theme' ); ?></a>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
