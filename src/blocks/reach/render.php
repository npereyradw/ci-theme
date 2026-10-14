<?php
/**
 * Render: ci/reach — reach section: a large animated arc as the background and the
 * registrations in a list.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$id    = wp_unique_id( 'ci-reach-title-' );
$items = ! empty( $attributes['showList'] ) ? CI_Theme_Content::institutions() : array();
$art   = ! empty( $attributes['showArt'] );
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-reach', 'classes' => $art ? array( 'ci-reach--art' ) : array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<?php if ( $art ) : ?>
		<div class="ci-reach-art">
			<?php echo CI_Theme_Cover::render( array( 'seed' => 'hero', 'fill' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		</div>
		<div class="ci-reach-shade" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="ci-container ci-reach-inner">
		<div class="ci-stack">
			<?php echo CI_Theme_Section::eyebrow( isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 id="<?php echo esc_attr( $id ); ?>" class="ci-h2"><?php echo esc_html( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<?php echo CI_Theme_Section::paragraphs( isset( $attributes['text'] ) ? $attributes['text'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		</div>
		<?php if ( $items ) : ?>
			<ul data-anim-stagger class="ci-reach-list">
				<?php foreach ( $items as $item ) : ?>
					<li class="ci-reach-item">
						<span class="ci-reach-name"><?php echo esc_html( $item['full_name'] ); ?></span>
						<span class="ci-reach-type"><?php echo esc_html( $item['type'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
