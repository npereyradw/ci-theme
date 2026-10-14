<?php
/**
 * Render: ci/features — numbered items, with an accent dot or as cards. The numbering (01,
 * 02…) is automatic.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$items = array_values(
	array_filter(
		isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array(),
		static function ( $item ) {
			return ! empty( $item['title'] ) || ! empty( $item['text'] );
		}
	)
);
$variant = isset( $attributes['variant'] ) && 'cards' === $attributes['variant'] ? 'cards' : 'dot';
$id      = wp_unique_id( 'ci-features-title-' );
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-features', 'classes' => array( 'ci-features--' . $variant ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<div class="ci-container ci-stack ci-stack--lg">
		<?php echo CI_Theme_Section::intro( $attributes, array( 'id' => $id, 'link' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		<?php if ( $items ) : ?>
			<div data-anim-stagger class="ci-features-list">
				<?php foreach ( $items as $i => $item ) : ?>
					<div class="ci-features-item">
						<?php if ( 'dot' === $variant ) : ?>
							<span class="ci-features-head">
								<span class="ci-features-dot" aria-hidden="true"></span>
								<span class="ci-features-num"><?php echo esc_html( CI_Theme_Content::pad( $i + 1 ) ); ?></span>
							</span>
							<h3 class="ci-h3"><?php echo esc_html( isset( $item['title'] ) ? $item['title'] : '' ); ?></h3>
						<?php else : ?>
							<span class="ci-features-num"><?php echo esc_html( CI_Theme_Content::pad( $i + 1 ) ); ?></span>
							<h3 class="ci-features-title"><?php echo esc_html( isset( $item['title'] ) ? $item['title'] : '' ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $item['text'] ) ) : ?>
							<p class="ci-features-text"><?php echo esc_html( $item['text'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
