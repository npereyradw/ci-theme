<?php
/**
 * Render: ci/testimonials — client testimonials. Hidden while none are published.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$items = CI_Theme_Content::testimonials();
if ( ! $items ) {
	return;
}

$id = wp_unique_id( 'ci-testimonials-title-' );
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-testimonials' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<div class="ci-container ci-stack ci-stack--md">
		<?php echo CI_Theme_Section::intro( $attributes, array( 'id' => $id, 'link' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		<div data-anim-stagger class="ci-cards">
			<?php
			foreach ( $items as $item ) {
				echo CI_Theme_Cards::testimonial( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cards.
			}
			?>
		</div>
	</div>
</section>
