<?php
/**
 * Render: ci/areas — the areas of the firm (ci_area taxonomy), each with its animated icon.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$areas = CI_Theme_Content::areas();
if ( ! $areas ) {
	return;
}

$id      = wp_unique_id( 'ci-areas-title-' );
$pattern = isset( $attributes['linkPattern'] ) ? (string) $attributes['linkPattern'] : '';
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-areas' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<span class="ci-arc ci-areas-arc" aria-hidden="true"></span>
	<div class="ci-container ci-areas-inner">
		<?php echo CI_Theme_Section::intro( $attributes, array( 'id' => $id, 'link' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		<div data-anim-stagger class="ci-areas-list">
			<?php foreach ( $areas as $i => $area ) : ?>
				<div class="ci-areas-item">
					<?php echo CI_Theme_Area_Icon::svg( CI_Theme_Area_Icon::resolve( $area['icon'], $i ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					<span class="ci-areas-num"><?php echo esc_html( CI_Theme_Content::pad( $i + 1 ) ); ?></span>
					<h3 class="ci-areas-title"><?php echo esc_html( $area['name'] ); ?></h3>
					<?php if ( '' !== $area['text'] ) : ?>
						<p class="ci-areas-text"><?php echo esc_html( $area['text'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== trim( $pattern ) ) : ?>
						<a href="<?php echo esc_url( $area['url'] ); ?>" class="ci-areas-link"><?php echo esc_html( str_replace( '{area}', $area['name'], $pattern ) ); ?></a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
