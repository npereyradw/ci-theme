<?php
/**
 * Render: ci/cta — closing call to action.
 *
 * The text comes from the site settings unless the block replaces it (useGlobal = false); empty
 * fields of its own keep using the site settings.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$site = CI_Theme_Section::site();
$own  = isset( $attributes['useGlobal'] ) && false === $attributes['useGlobal'];

// Own text: each empty field falls back to the site settings (e.g. to change only the title).
$pick = static function ( $key, $global ) use ( $attributes, $own, $site ) {
	$own = $own && isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
	return '' !== $own ? $own : $site[ $global ];
};
$eyebrow = $pick( 'eyebrow', 'cta_eyebrow' );
$title   = $pick( 'title', 'cta_title' );
$text    = $pick( 'text', 'cta_text' );

if ( '' === trim( (string) $title ) ) {
	return;
}

$id = wp_unique_id( 'ci-cta-title-' );
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-cta' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?> aria-labelledby="<?php echo esc_attr( $id ); ?>">
	<div class="ci-container">
		<div class="ci-cta-box">
			<span class="ci-arc ci-cta-arc" aria-hidden="true"></span>
			<span data-anim="pop" class="ci-cta-dot" aria-hidden="true"></span>
			<div class="ci-cta-text">
				<?php echo CI_Theme_Section::eyebrow( $eyebrow, 'ci-eyebrow--light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
				<h2 id="<?php echo esc_attr( $id ); ?>" data-anim="mask" class="ci-cta-title ci-title"><?php echo esc_html( $title ); ?></h2>
				<?php if ( '' !== trim( (string) $text ) ) : ?>
					<p class="ci-cta-body ci-body"><?php echo esc_html( $text ); ?></p>
				<?php endif; ?>
			</div>
			<?php echo CI_Theme_Section::buttons( isset( $attributes['buttons'] ) ? $attributes['buttons'] : array(), 'ci-cta-actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		</div>
	</div>
</section>
