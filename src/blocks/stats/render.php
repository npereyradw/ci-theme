<?php
/**
 * Render: ci/stats — text with highlighted figures.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$id    = wp_unique_id( 'ci-stats-title-' );
$stats = array_filter(
	isset( $attributes['stats'] ) && is_array( $attributes['stats'] ) ? $attributes['stats'] : array(),
	static function ( $stat ) {
		return ! empty( $stat['value'] ) || ! empty( $stat['label'] );
	}
);
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-stats' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<div class="ci-container ci-stats-split">
		<div class="ci-stack">
			<?php echo CI_Theme_Section::eyebrow( isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 id="<?php echo esc_attr( $id ); ?>" class="ci-h2"><?php echo esc_html( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['text'] ) ) : ?>
				<p class="ci-text"><?php echo CI_Theme_Section::lines( $attributes['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?></p>
			<?php endif; ?>
			<?php echo CI_Theme_Section::link( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		</div>
		<?php if ( $stats ) : ?>
			<dl data-anim-stagger class="ci-stats-list">
				<?php foreach ( $stats as $stat ) : ?>
					<div class="ci-stats-item">
						<dt class="ci-stats-label"><?php echo esc_html( isset( $stat['label'] ) ? $stat['label'] : '' ); ?></dt>
						<dd data-anim="count" class="ci-stats-value"><?php echo esc_html( isset( $stat['value'] ) ? $stat['value'] : '' ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
	</div>
</section>
