<?php
/**
 * Render: ci/text-media — text with a graphic cover or an image.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$id    = wp_unique_id( 'ci-text-media-title-' );
$left  = isset( $attributes['mediaPosition'] ) && 'left' === $attributes['mediaPosition'];
$image = isset( $attributes['media'] ) && 'image' === $attributes['media'] ? (int) $attributes['image'] : 0;
$media = CI_Theme_Cover::render(
	array(
		'seed'  => isset( $attributes['coverSeed'] ) ? $attributes['coverSeed'] : '1',
		'ratio' => isset( $attributes['ratio'] ) ? $attributes['ratio'] : '5 / 4',
		'image' => $image,
		'sizes' => '(max-width: 959px) 100vw, 600px',
	)
);
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-text-media', 'classes' => $left ? array( 'ci-text-media--left' ) : array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<div class="ci-container ci-text-media-split">
		<div class="ci-stack ci-text-media-text">
			<?php echo CI_Theme_Section::eyebrow( isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 id="<?php echo esc_attr( $id ); ?>" class="ci-h2"><?php echo esc_html( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<?php echo CI_Theme_Section::paragraphs( isset( $attributes['text'] ) ? $attributes['text'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		</div>
		<div class="ci-text-media-media">
			<?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cover. ?>
		</div>
	</div>
</section>
