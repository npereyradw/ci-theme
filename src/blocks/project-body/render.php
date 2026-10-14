<?php
/**
 * Render: ci/project-body — project description and the quote box. The button takes the
 * project service to the contact form.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$post = CI_Theme_Content::context_post( CI_Theme_Content::PROJECT );
if ( ! $post ) {
	return;
}
$p    = CI_Theme_Content::project_item( $post );
$text = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};
$uid     = wp_unique_id( 'ci-project-body-' );
$content_html = CI_Theme_Content::content_html( $post );
$button  = CI_Theme_Section::url( $text( 'buttonUrl' ) );
if ( '' !== $p['service_slug'] && '' !== $button ) {
	$button = esc_url( add_query_arg( 'service', $p['service_slug'], $button ) );
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'ci-project-body' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo '' !== $text( 'title' ) ? ' aria-labelledby="' . esc_attr( $uid ) . '"' : ''; ?>>
	<div class="ci-container ci-project-body-grid">
		<div class="ci-project-body-description">
			<?php if ( '' !== $text( 'title' ) ) : ?>
				<h2 id="<?php echo esc_attr( $uid ); ?>" data-anim="mask" class="ci-project-body-title"><?php echo esc_html( $text( 'title' ) ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $p['excerpt'] ) : ?>
				<p class="ci-project-body-summary"><?php echo esc_html( $p['excerpt'] ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== trim( $content_html ) ) : ?>
				<div class="ci-prose ci-project-body-content">
					<?php echo $content_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- post content filtered by the_content. ?>
				</div>
			<?php endif; ?>
		</div>
		<aside class="ci-project-body-aside" aria-label="<?php echo esc_attr( $text( 'buttonLabel' ) ); ?>">
			<span class="ci-arc ci-project-body-aside-arc" aria-hidden="true"></span>
			<span data-anim="pop" class="ci-project-body-aside-dot" aria-hidden="true"></span>
			<?php if ( '' !== $text( 'asideTitle' ) ) : ?>
				<p class="ci-project-body-aside-title"><?php echo esc_html( $text( 'asideTitle' ) ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $text( 'asideText' ) ) : ?>
				<p class="ci-project-body-aside-text"><?php echo esc_html( $text( 'asideText' ) ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $text( 'buttonLabel' ) && '' !== $button ) : ?>
				<a href="<?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>" class="ci-btn ci-btn--primary ci-project-body-aside-btn"><?php echo esc_html( $text( 'buttonLabel' ) ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $p['service_url'] && '' !== $text( 'serviceLinkLabel' ) ) : ?>
				<a href="<?php echo esc_url( $p['service_url'] ); ?>" class="ci-project-body-aside-link"><?php echo esc_html( $text( 'serviceLinkLabel' ) . ' ' . $p['service'] . ' →' ); ?></a>
			<?php endif; ?>
		</aside>
	</div>
</section>
