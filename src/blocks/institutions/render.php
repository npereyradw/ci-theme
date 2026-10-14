<?php
/**
 * Render: ci/institutions — logos of professional boards and associations.
 * - statement: a statement and the logos, with a line above.
 * - label: a label as the title (h2) and the logos.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$list = CI_Theme_Institutions::render( array( 'dark' => isset( $attributes['scheme'] ) && 'dark' === $attributes['scheme'] ) );
if ( '' === $list ) {
	return;
}

$variant = isset( $attributes['variant'] ) && 'label' === $attributes['variant'] ? 'label' : 'statement';
$id      = wp_unique_id( 'ci-institutions-title-' );
$label   = isset( $attributes['label'] ) ? trim( (string) $attributes['label'] ) : '';
$text    = isset( $attributes['text'] ) ? trim( (string) $attributes['text'] ) : '';
$aria    = 'label' === $variant && '' !== $label
	? ' aria-labelledby="' . esc_attr( $id ) . '"'
	: ' aria-label="' . esc_attr( '' !== $label ? $label : __( 'Registrations', 'ci-theme' ) ) . '"';
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-institutions-band', 'classes' => array( 'ci-institutions-band--' . $variant ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo $aria; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>>
	<div class="ci-container ci-institutions-band-inner">
		<?php if ( 'label' === $variant ) : ?>
			<?php if ( '' !== $label ) : ?>
				<h2 id="<?php echo esc_attr( $id ); ?>" class="ci-eyebrow"><?php echo esc_html( $label ); ?></h2>
			<?php endif; ?>
		<?php elseif ( '' !== $text ) : ?>
			<p class="ci-institutions-band-text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<?php echo $list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Institutions. ?>
	</div>
</section>
