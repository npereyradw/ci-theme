<?php
/**
 * Render: ci/not-found — 404 page: a large "4 ◯ 4", the message, buttons and links.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$text = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};
$links = array_filter(
	isset( $attributes['links'] ) && is_array( $attributes['links'] ) ? $attributes['links'] : array(),
	static function ( $link ) {
		return ! empty( $link['label'] ) && ! empty( $link['url'] );
	}
);
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'ci-not-found' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<div class="ci-container ci-not-found-inner">
		<div class="ci-not-found-code" aria-hidden="true">
			<span>4</span>
			<svg viewBox="0 0 100 100" class="ci-not-found-logo" focusable="false">
				<path d="M 78.28 21.72 A 40 40 0 1 0 78.28 78.28" fill="none" stroke="<?php echo esc_attr( CI_Theme_Section::hex( 'navy' ) ); ?>" stroke-width="15" />
				<circle cx="50" cy="50" r="20" fill="<?php echo esc_attr( CI_Theme_Section::hex( 'yellow' ) ); ?>" />
				<rect x="86" y="27" width="8" height="46" fill="<?php echo esc_attr( CI_Theme_Section::hex( 'red-bright' ) ); ?>" />
			</svg>
			<span>4</span>
		</div>
		<?php if ( '' !== $text( 'label' ) ) : ?>
			<p class="ci-not-found-label"><?php echo esc_html( $text( 'label' ) ); ?></p>
		<?php endif; ?>
		<h1 class="ci-h2 ci-not-found-title"><?php echo esc_html( $text( 'title' ) ); ?></h1>
		<?php if ( '' !== $text( 'text' ) ) : ?>
			<p class="ci-not-found-text"><?php echo esc_html( $text( 'text' ) ); ?></p>
		<?php endif; ?>
		<?php echo CI_Theme_Section::buttons( isset( $attributes['buttons'] ) ? $attributes['buttons'] : array(), 'ci-not-found-actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		<?php if ( $links ) : ?>
			<nav class="ci-not-found-nav" aria-label="<?php echo esc_attr( $text( 'navLabel' ) ); ?>">
				<?php foreach ( $links as $link ) : ?>
					<a href="<?php echo CI_Theme_Section::url( $link['url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" class="ci-not-found-link"><?php echo esc_html( $link['label'] ); ?> <span aria-hidden="true">→</span></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</div>
</section>
