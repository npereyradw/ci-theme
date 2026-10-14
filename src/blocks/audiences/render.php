<?php
/**
 * Render: ci/audiences — "Who we work for". Each audience links to its services (saved by
 * slug; services that no longer exist are skipped).
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
$by_slug = array_column( CI_Theme_Content::services(), null, 'slug' );
$id      = wp_unique_id( 'ci-audiences-title-' );
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-audiences' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<div class="ci-container ci-stack ci-stack--lg">
		<?php echo CI_Theme_Section::intro( $attributes, array( 'id' => $id, 'link' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		<?php if ( $items ) : ?>
			<div data-anim-stagger class="ci-audiences-list">
				<?php foreach ( $items as $item ) : ?>
					<div class="ci-audiences-item">
						<h3 class="ci-audiences-title"><?php echo esc_html( isset( $item['title'] ) ? $item['title'] : '' ); ?></h3>
						<?php if ( ! empty( $item['text'] ) ) : ?>
							<p class="ci-audiences-text"><?php echo esc_html( $item['text'] ); ?></p>
						<?php endif; ?>
						<?php
						$links = array();
						foreach ( isset( $item['services'] ) ? (array) $item['services'] : array() as $slug ) {
							if ( isset( $by_slug[ $slug ] ) ) {
								$links[] = $by_slug[ $slug ];
							}
						}
						if ( $links ) :
							?>
							<ul class="ci-audiences-links">
								<?php foreach ( $links as $service ) : ?>
									<li><a href="<?php echo esc_url( $service['url'] ); ?>" class="ci-audiences-link"><?php echo esc_html( $service['title'] ); ?><span aria-hidden="true">→</span></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
